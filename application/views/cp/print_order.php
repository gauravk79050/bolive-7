<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="<?php echo base_url()?>assets/cp/new_css/style.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url()?>assets/cp/new_css/table.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url()?>assets/cp/new_css/print.css" rel="stylesheet" type="text/css" media="print" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo _('Print Order Data'); ?></title>
<style type="text/css">
	.break {page-break-after: always}
</style>
</head>
<body id="printing_order">

<?php if($print_count == "all"):?>
	
	<?php for($j=0;$j<count($orderData);$j++): ?>
	
    <table cellspacing="2" cellpadding="2" border="0" id="single-order" align="center">		
		<tr>
			<td><table width="100%" border="0" cellspacing="0" cellpadding="2">		
				<tr>
			 		<td align="left"><strong><?php echo _('Order Number')?></strong> : <?php echo $orderData[$j][0]->id; ?></td>
                </tr>
				<tr>
			  		<td align="left"><strong><?php echo _('Order Date')?></strong> : <? 
					    $da = date("d",strtotime($orderData[$j][0]->created_date));
						$mo_eng = date("F",strtotime($orderData[$j][0]->created_date));
						$yr = date("y",strtotime($orderData[$j][0]->created_date));
						echo $date_dutch = $da." ".$mo_eng." ".$yr;
					  ?>
					  <?php //if($_SERVER['REMOTE_ADDR'] == "122.163.238.81"){?>
								<p style="text-align: right">
									<?php if($orderData[$j][0]->payment_via_paypal == 2 ){?>
										<?php if($orderData[$j][0]->payment_status == 1){?>
										<span style="color: green;"><?php echo _("PAID");?></span>
										<?php }else{?>
										<span style="color: red;"><?php echo _("PENDING");?></span>
										<?php }?>
										<br/>
										<?php echo _("via")." ".$orderData[$j][0]->billing_option; ?>
									<?php }?>
								</p>
								<?php //}?>
			       </td>
				</tr>		
				<tr>
					<td width="52%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2">
						<tr>
							<td bgcolor="#F0F0F0" align="left">
								<b><?php echo _('Customer Address')?></b>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo $orderData[$j][0]->company_c;?>
                        		<br/>
								<?php echo $orderData[$j][0]->lastname_c?>&nbsp;<?php echo $orderData[$j][0]->firstname_c?>
								<br />
								<?php echo $orderData[$j][0]->address_c?>&nbsp; <?php echo $orderData[$j][0]->housenumber_c?>
								<br />
								<?php echo $orderData[$j][0]->postcode_c?>&nbsp;<?php echo $orderData[$j][0]->city_c?>
								<br/>
								<?php echo $orderData[$j][0]->country_name?>
								<br/>
								<br/>
								<strong> <?php echo _('Telephone No')?></strong>&nbsp;&nbsp;:<?php echo $orderData[$j][0]->phone_c?>
								<br/>
								<strong><?php echo _('GSM'); ?></strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;<?php echo $orderData[$j][0]->mobile_c?>
								<br/>
								<strong><?php echo _('Email')?></strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;<?php echo $orderData[$j][0]->email_c?>&nbsp;-&nbsp;<a class="edit" href="mailto:<?php echo $orderData[$j][0]->email_c?>?subject=Re: <?php echo _('your order')?>  <?php echo $orderData[$j][0]->id?>&amp;body=<?php echo _('Dear client')?>,"><?php echo _('Email')?></a></span>
							</td>		
						</tr>	
						<tr>
							<td>&nbsp;</td>
						</tr>
						<?php if($orderData[$j][0]->option == "2"):?>
						<tr>
							<td><table width="100%" cellpadding="0" cellspacing="0" border="0" >
								<tr>
									<td width="200">&nbsp;</td>
								</tr>
								<tr>
									<td width="200" style="padding:3px"><strong><?php echo _('Delivery Date')?></strong></td>
							 		<td width="15%" style="padding:3px"> <? 
									        $da = date("d",strtotime($orderData[$j][0]->delivery_date));
											$mo_eng = date("F",strtotime($orderData[$j][0]->delivery_date));
											$yr = date("y",strtotime($orderData[$j][0]->delivery_date));
											echo $date_dutch = $da." ".$mo_eng." `".$yr;
									   ?>
									</td>
									<td width="30%" style="padding:3px"><strong><?php echo _('Delivery Address')?></strong></td>
									<td width="35%" style="padding:3px"> <?php echo $orderData[$j][0]->delivery_streer_address?></td>
								</tr>
								<tr>
									<td width="200" style="padding:3px"><strong><?php echo _('Delivery Day')?></strong></td>
							  		<td width="15%" style="padding:3px"><?php echo $day = $orderData[$j][0]->delivery_day;?></td>
									<td width="30%" style="padding:3px"><strong> <?php echo _('Delivery Area')?></strong></td>
									<td width="35%" style="padding:3px"><?php if(isset($orderData[$j][0]->delivery_area_name)) { echo $orderData[$j][0]->delivery_area_name; } ?></td>
								</tr>
								<tr>
									<td width="200" style="padding:3px"><strong><?php echo _('Delivery City')?></strong></td>
							  		<td width="15%" style="padding:3px"><?php if(isset($orderData[$j][0]->delivery_city_name)) { echo $orderData[$j][0]->delivery_city_name; } ?></td>
									<td width="30%" style="padding:3px"><strong> <?php echo _('Delivery Zip')?></strong></td>
									<td width="35%" style="padding:3px"><?php echo $orderData[$j][0]->delivery_zip?></td>
								</tr>
								<tr>
									<td width="200" style="padding:3px"><strong> <?php echo _('Delivery Hour')?></strong></td>
							  		<td width="15%" style="padding:3px"> <?php echo $orderData[$j][0]->delivery_hour?>&nbsp;<?php echo _('HOURS')?></td>
									<td width="30%" style="padding:3px"><strong><?php echo _('Delivery Minute')?></strong></td>
									<td width="35%" style="padding:3px"><?php echo $orderData[$j][0]->delivery_minute?>&nbsp;<?php echo _('MINS')?></td>
								</tr>
								<tr>
									<td width="200" style="padding:3px"><strong> <?php echo _('Remarks')?></strong></td>
									<td width="35%" style="padding:3px" colspan="3"><?php echo $orderData[$j][0]->delivery_remarks?></td>
								</tr>
					  		</table></td>
						</tr>
						<?php elseif($orderData[$j][0]->option == "1"):?>
						<tr>
							<td><table width="100%" cellpadding="0" cellspacing="0" border="0" class="override">
								<tr>
									<td width="120" style="padding:3px"><strong><?php echo _('Pick Date')?></strong></td>
									<td style="padding:3px"><? 
									    $da = date("d",strtotime($orderData[$j][0]->order_pickupdate));
										$mo_eng = date("F",strtotime($orderData[$j][0]->order_pickupdate));
										$yr = date("y",strtotime($orderData[$j][0]->order_pickupdate));
										echo $date_dutch = $da." ".$mo_eng." `".$yr;?>
									</td>
						  		</tr>
								<tr>
									<td width="20%" style="padding:3px"><strong><?php echo _('Pick Day')?></strong></td>
									<td style="padding:3px"> <?php echo $day = $orderData[$j][0]->order_pickupday;?></td>
								</tr>
								<tr>
									<td width="20%" style="padding:3px"><strong><?php echo _('Pick Time')?></strong></td>
									<td style="padding:3px"><?php echo $orderData[$j][0]->order_pickuptime?> &nbsp;<?php echo _('HOUR')?></td>
								</tr>
								<tr>
									<td width="20%" style="padding:3px"><strong><?php echo _('Note')?></strong></td>
									<td style="padding:3px"><?php echo $orderData[$j][0]->order_remarks?></td>
								</tr>
							</table></td>
						</tr>	
						<?php elseif($orderData[$j][0]->option == "0"):?>
						<tr>
							<td><table width="100%" cellpadding="0" cellspacing="0" border="0" class="override">
								<tr>
						  			<td width="98" style="padding:3px"><strong><?php echo _('Date')?></strong></td>
						  			<td width="436" style="padding:3px"> <?   
										$da = date("d",strtotime($orderData[$j][0]->order_pickupdate));
										$mo_eng = date("F",strtotime($orderData[$j][0]->order_pickupdate));
										$yr = date("y",strtotime($orderData[$j][0]->order_pickupdate));
										echo $date_dutch = $da." ".$mo_eng." `".$yr;
									?>
									</td>
								</tr>	
								<tr>
						  			<td width="98" style="padding:3px"><strong><?php echo _('Order Hours')?></strong></td>
						  			<td style="padding:3px"> <?php echo $orderData[$j][0]->order_pickuptime?>&nbsp;<?php echo _('HOUR')?></td>
								</tr>	
								<tr>
						  			<td width="98" style="padding:3px"><strong><?php echo _('Note')?></strong></td>
						  			<td style="padding:3px"> <?php echo $orderData[$j][0]->order_remarks?></td>
								</tr>
					  		</table></td>
						</tr>	
						<? endif; ?>	
					</table></td>		
		   		</tr>
		  	</table><!--</td>		
		</tr>
		<tr>		
			<td>--><table border="1" cellpadding="2" cellspacing="2" width="100%">			
				<tr>
					<td width="34%" bgcolor="#F2F2F2"><strong><?php echo _('Product')?></strong></td>
				  	<td width="5%" align="left" bgcolor="#F2F2F2"><strong><?php echo _('Quantity')?></strong></td>
				  	<td width="11%" align="center" bgcolor="#F2F2F2"><strong><?php echo _('Default Price')?></strong></td>
				  	<td width="40%" align="left" bgcolor="#F2F2F2"><strong><?php echo _('Extra')?></strong></td>			
				  	<td width="2%" align="right" bgcolor="#F2F2F2">&nbsp;</td>	
					<td width="8%" align="right" bgcolor="#F2F2F2"><strong><?php echo _('Sub Total')?></strong></td>		
		    	</tr>
				<? for($i=0;$i<count($order_details_data[$j]);$i++):?>
				<tr>
					<td valign="top" align="right"><?php echo $order_details_data[$j][$i]->proname?>&nbsp;&nbsp;
					
					<?php  if($order_details_data[$j][$i]->type == "1"):?>
						<img src="<?php echo base_url()?>assets/cp/images/new.png" alt="New" width="16" height="16" />
					<?php  endif; ?>
					&nbsp;&nbsp;
					<?php  if($order_details_data[$j][$i]->discount != "0" && $order_details_data[$j][$i]->discount != "multi"):?>
						<img src="<?php echo base_url()?>assets/cp/images/message.png" alt="discount" width="16" height="16" title="<?php echo $order_details_data[$j][$i]->discount?>" />
					<?php elseif($order_details_data[$j][$i]->discount == "multi"):?>
						<img src="<?php echo base_url()?>assets/cp/images/discount.jpg" width="16" height="16" title="<?php echo $order_details_data[$j][$i]->discount_per_qty?>" border="0" />
					<? endif; ?>
					
					</td>			
					<td align="left" valign="top"><?php echo $order_details_data[$j][$i]->quantity?></td>
                    
                  	<td align="center" valign="top"> <?php /*echo $actual_price = ($order_details_data[$j][$i]->price+$order_details_data[$j][$i]->discount);*/ ?><?php if( $order_details_data[$j][$i]->content_type == '1'): $actual_price = round(((float)($order_details_data[$j][$i]->price_weight))*1000,2); echo $actual_price.'&nbsp;&euro;/kg'; else: $actual_price = round(((float)($order_details_data[$j][$i]->price_per_unit)),2); echo $actual_price.'&nbsp;&euro;';  endif; ?></td>
				  	<td align="left" valign="top">
				  		
						<?php if(array_key_exists($j,$TempExtracosts) &&  array_key_exists($i,$TempExtracosts[$j]) && $TempExtracosts[$j][$i] != ""):?>
							<?php for($n=0;$n<count($TempExtracosts[$j][$i]);$n++):?> 
							<span style="color:red;"><?php echo $TempExtracosts[$j][$i][$n][0]?></span> :<?php echo $TempExtracosts[$j][$i][$n][2] ;?>&euro;<br>
							<?php endfor; ?>
						<?php endif;?>
						<?php if($order_details_data[$j][$i]->discount == "multi" && !empty($order_details_data[$j][$i]->discount_per_qty)):?>
					<br><span style="color:red;"><?php echo _('Extra Discount')?>:</span>&nbsp;<?php echo $order_details_data[$j][$i]->discount_per_qty?>&nbsp;&euro;		
						<?php endif;?>
						<?php if($order_details_data[$j][$i]->pro_remark != ""):?>
					<span style="font-size:11px"><?php echo $order_details_data[$j][$i]->pro_remark?></span><br><br>
						<?php else:?><span style="font-size:11px"><?php echo _('Not Available')?></span><br><br>
						<?php endif;?>
					
					</td>
                    <td align="right" valign="middle">&nbsp;</td>	
					<td align="right" valign="top"><b>&euro;<?php echo str_replace('.',',',round($order_details_data[$j][$i]->total,2)); ?></b></td>				
		    	</tr>
				<?php endfor;?>
				<?php if($orderData[$j][0]->option == "2"):?>
				<tr>
					<td colspan="5" align="right" valign="top"><div align="right"><strong><?php echo _('Order Total')?></strong></div></td>	
					<td align="right" valign="top"><div align="left"><strong>&euro;&nbsp;<?php echo str_replace('.',',',round($total[$j],2)); ?></strong></div></td>
				</tr>
				<tr>
					<td colspan="5" align="right" valign="top"><div align="right"><strong><?php echo _('Delivery Cost')?></strong></div></td>	
					<td align="right" valign="top"><b>&euro;&nbsp;<span id="delivery_cost"><?php echo str_replace('.',',',round($orderData[$j][0]->delivery_cost,2)); ?></span> </b></td>
				</tr>
				<?php endif;?>
				<tr>
					<td colspan="5" align="right" valign="top"><div align="right"><strong><?php echo _('Total')?></strong></div></td>	
					<td align="right" valign="top"><div align="left"><b>&euro;&nbsp;<span id="total_cost"><?php echo str_replace('.',',',round(($total[$j]+$orderData[$j][0]->delivery_cost),2)); ?></span> </b></div></td>
				</tr>
				<?php if($activate_discount_card):?>
				<tr>
					<td colspan="6" align="left" valign="top"><strong><?php echo _('Discount Card Number')?>:</strong>&nbsp;<b><span><?php echo ($discount_card_number[$j])?$discount_card_number[$j]:'--';?></span> </b></td>
				</tr>
				<?php endif;?>				
	    	</table></td>
		</tr>
	</table>
    <p></p>
	<hr/>
	<p></p>
	<br class="break"> 
	<?php endfor;?>
	
<?php elseif($print_count == "single"):?>

	<table width="550" cellspacing="2" cellpadding="2" id="single-order">		
		<tr>
			<td><table width="100%" border="0" cellspacing="0" cellpadding="2">		
				<tr>
			  		<td align="left"><strong><?php echo _('Order Number')?></strong> : <?php echo $orderData[0]->id?></td>
			  	</tr>
			  	<tr>
			  		<td align="left"><strong><?php echo _('Order Date')?></strong> :   
			 		<?php   $da = date("d",strtotime($orderData[0]->created_date));
							$mo_eng = date("F",strtotime($orderData[0]->created_date));
							$yr = date("y",strtotime($orderData[0]->created_date));
							echo $da." ".$mo_eng." ".$yr;
					?>
								<?php //if($_SERVER['REMOTE_ADDR'] == "122.163.238.81"){?>
								<p style="text-align: right">
									<?php if($orderData[0]->payment_via_paypal == 2 ){?>
										<?php if($orderData[0]->payment_status == 1){?>
										<span style="color: green;"><?php echo _("PAID");?></span>
										<?php }else{?>
										<span style="color: red;"><?php echo _("PENDING");?></span>
										<?php }?>
										<br/>
										<?php echo _("via")." ".$billing_option['0']['billing_option']; ?>
									<?php }?>
								</p>
								<?php //}?>
					</td>
				</tr>		
				<tr>
					<td width="52%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2" class="override">
						<tr>
							<td bgcolor="#F0F0F0">
								<b><?php echo _('Customer Address')?></b>
							</td>		
						</tr>
						<tr>
							<td>
								<?php echo $orderData[0]->lastname_c?>&nbsp;<?php echo $orderData[0]->firstname_c?>
                        		<br />
                        		<?php echo $orderData[0]->address_c?>&nbsp;<?php echo $orderData[0]->housenumber_c?>
                        		<br />
                        		<?php echo $orderData[0]->postcode_c?>&nbsp;<?php echo $orderData[0]->city_c?>
                        		<br/>
                        		<?php echo $orderData[0]->country_name?>
                        		<br/>
                       			<br/>
                        		<strong><?php echo _('Telephone')?></strong>&nbsp;&nbsp;:<?php echo $orderData[0]->phone_c?>
                        		<br/>
                       			 <strong>GSM</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;<?php if($orderData[0]->mobile_c): echo $orderData[0]->mobile_c; else: echo _('Not Available');endif;?>
                        		<br/>
                        		<strong><?php echo _('Email')?> </strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp; <?php echo $orderData[0]->email_c?>&nbsp;-&nbsp;<a class="edit" href="mailto:<?php echo $orderData[0]->email_c?>?subject=Re: <?php echo _('your order')?> <?php echo $orderData[0]->id?>&amp;body=<?php echo _('Dear client')?>,"><?php echo _('Email')?></a> </span>
                                
							</td>		
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						
						<?php if($orderData[0]->option == "2"):?>
						<tr>
							<td><table width="100%" cellpadding="0" cellspacing="0" border="0" >
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td width="20%" style="padding:3px"><strong><?php echo _('Delivery Date')?></strong></td>
									<td width="15%" style="padding:3px"><?php 	
									$da = date("d",strtotime($orderData[0]->delivery_date));
									$mo_eng = date("F",strtotime($orderData[0]->delivery_date));
									$yr = date("y",strtotime($orderData[0]->delivery_date));
									echo $date_dutch = $da." ".$mo_eng." `".$yr;?>
									</td>
									<td width="30%" style="padding:3px"><strong><?php echo _('Delivery Address')?></strong></td>
									<td width="35%" style="padding:3px"> <?php echo $orderData[0]->delivery_streer_address?></td>
								</tr>
								<tr>
									<td width="20%" style="padding:3px"><strong><?php echo _('Delivery Day')?></strong></td>
									<td width="15%" style="padding:3px"><?php echo $day = $orderData[0]->delivery_day;?></td>
									<td width="30%" style="padding:3px"><strong> <?php echo _('Delivery Area')?></strong></td>
									<td width="35%" style="padding:3px"><?php echo $orderData[0]->delivery_area_name?></td>
								</tr>
								<tr>
									<td width="20%" style="padding:3px"><strong><?php echo _('Delivery City')?></strong></td>
									<td width="15%" style="padding:3px"><?php echo $orderData[0]->delivery_city_name?></td>
									<td width="30%" style="padding:3px"><strong> <?php echo _('Delivery Zip')?></strong></td>
									<td width="35%" style="padding:3px"><?php echo $orderData[0]->delivery_zip?></td>
								</tr>
								<tr>
									<td width="20%" style="padding:3px"><strong> <?php echo _('Delivery Hour')?></strong></td>
									<td width="15%" style="padding:3px"> <?php echo $orderData[0]->delivery_hour?>&nbsp;<?php echo _('HOURS')?></td>
									<td width="30%" style="padding:3px"><strong><?php echo _('Delivery Minute')?></strong></td>
									<td width="35%" style="padding:3px"><?php echo $orderData[0]->delivery_minute?>&nbsp;<?php echo _('MINS')?></td>
								</tr>
								<tr>
									<td width="30%" style="padding:3px"><strong><?php echo _('Remarks')?></strong></td>
									<td width="35%" style="padding:3px" colspan="3"><?php echo $orderData[0]->delivery_remarks?></td>
								</tr>
							</table></td>
						</tr>
						<? elseif($orderData[0]->option == "1"):?>
						<tr>
							<td><table width="100%" cellpadding="0" cellspacing="0" border="0" class="override">
								<tr>
									<td width="20%" style="padding:3px"><strong><?php echo _('Pick Date')?></strong></td>
									<td style="padding:3px"><?php 
									$da = date("d",strtotime($orderData[0]->order_pickupdate));
									$mo_eng = date("F",strtotime($orderData[0]->order_pickupdate));
									$yr = date("y",strtotime($orderData[0]->order_pickupdate));
									echo $date_dutch = $da." ".$mo_eng." `".$yr;?></td>
								</tr>
								<tr>
									<td width="20%" style="padding:3px"><strong><?php echo _('Pick Day')?></strong></td>
									<td style="padding:3px"><?php echo $day = _($orderData[0]->order_pickupday);?></td>
								</tr>
								<tr>
									<td width="20%" style="padding:3px"><strong><?php echo _('Pick Time')?></strong></td>
									<td style="padding:3px"><?php echo $orderData[0]->order_pickuptime?>&nbsp;<?php echo _('Hour')?></td>
								</tr>
								<tr>
									<td width="20%" style="padding:3px"><strong><?php echo _('Note')?></strong></td>
									<td style="padding:3px"><?php echo $orderData[0]->order_remarks?></td>
								</tr>
							</table></td>
						</tr>	
						<?php elseif($orderData[0]->option == "0"):?>
						<tr>
							<td><table width="100%" cellpadding="0" cellspacing="0" border="0" class="override">
								<tr>
						  			<td width="16%" style="padding:3px"><strong><?php echo _('Date')?></strong></td>
						  			<td width="84%" style="padding:3px"> <?php 
									$da = date("d",strtotime($orderData[0]->order_pickupdate));
									$mo_eng = date("F",strtotime($orderData[0]->order_pickupdate));
									$yr = date("y",strtotime($orderData[0]->order_pickupdate));
									/*$mo_dut = getMonthInDutch($mo_eng);*/
									echo $date_dutch = $da." ".$mo_eng." `".$yr;?>
									</td>
								</tr>	
								<tr>
						  			<td width="16%" style="padding:3px"><strong><?php echo _('Hours')?></strong></td>
						  			<td style="padding:3px"> <?php echo $orderData[0]->order_pickuptime?> &nbsp;<?php echo _('Hour')?></td>
								</tr>	
								<tr>
						  			<td width="16%" style="padding:3px"><strong><?php echo _('NOTE')?></strong></td>
						  			<td style="padding:3px"> <?php echo $orderData[0]->order_remarks?></td>
								</tr>
							</table></td>
						</tr>	
						<?php endif;?>	
					</table></td>		
		  		</tr>
			</table><!--</td>		
		</tr>
		<tr>		
			<td>--><table border="1" cellpadding="2" cellspacing="2" width="100%">			
				<tr>
					<td width="34%" bgcolor="#E9E9E9"><strong><?php echo _('Product')?></strong></td>
				  	<td width="5%" align="left" bgcolor="#E9E9E9"><strong><?php echo _('Amount')?></strong></td>			
				  	<td width="11%" align="center" bgcolor="#E9E9E9"><strong><?php echo _('Rate')?></strong></td>
				  	<td width="40%" align="left" bgcolor="#E9E9E9"><strong><?php echo _('Extra')?></strong></td>	
				  	<td width="2%" align="right" bgcolor="#E9E9E9">&nbsp;</td>	
			    	<td width="8%" align="right" bgcolor="#E9E9E9"><strong><?php echo _('Sub Total')?></strong></td>
				</tr>
				<?php for($i=0;$i<count($order_details_data);$i++):?>
				<tr>
					<td valign="top" align="left"><?php echo $order_details_data[$i]->proname?>
					&nbsp;&nbsp;
					<?php  if($order_details_data[$i]->type == "1"):?>
						<img src="<?php echo base_url()?>assets/cp/images/new.png" alt="New" width="16" height="16" />
					<?php endif; ?>
					&nbsp;&nbsp;
					<?php if($order_details_data[$i]->discount != "0" && $order_details_data[$i]->discount != "multi"):?>
						<img src="<?php echo base_url()?>assets/cp/images/message.png" alt="discount" width="16" height="16" title="<?php echo $order_details_data[$i]->discount?>" />
					<?php elseif($order_details_data[$i]->discount == "multi"): ?>
						<img src="<?php echo base_url()?>assets/cp/images/discount.jpg" width="16" height="16" title="<?php echo $order_details_data[$i]->discount_per_qty?>" border="0" />
					<?php endif;?></td>			
					<td align="left" valign="top"><?php echo $order_details_data[$i]->quantity?></td>
			      	<td align="center" valign="top"><?php echo $actual_price = (float)($order_details_data[$i]->sub_total+$order_details_data[$i]->discount); ?></td>
				  	<td align="left" valign="top">
					
					<?php if($TempExtracosts != array() && isset($TempExtracosts[$i]) && $TempExtracosts[$i]!= ""):?>
					
                    	<?php for($j=0;$j<count($TempExtracosts[$i]);$j++): ?>
					  		<span style="color:red;"><?php echo $TempExtracosts[$i][$j][0]?></span> : <?php echo $TempExtracosts[$i][$j][2];?>&euro;
						<?php  endfor; ?>
					<?php endif;?>
					<?php if($order_details_data[$i]->discount == "multi" && !empty($order_details_data[$i]->discount_per_qty)):?><br>							
						<span style="color:red;">Extra Korting :</span>&nbsp;<?php echo (float)$order_details_data[$i]->discount_per_qty?>&nbsp;&euro;		
					<?php endif;?>
					<?php if($order_details_data[$i]->pro_remark != ""):?><br>
						<span style="font-size:11px"><?php echo $order_details_data[$i]->pro_remark?></span><br><br>
					<?php else:?>
						<span style="font-size:11px"><?php echo _('No Comment')?></span><br><br>
				 	<?php endif;?>	
				  	</td>
                  	<td align="right" valign="middle">&nbsp;</td>
					<td align="right" valign="top"><b>&euro;&nbsp;<?php echo str_replace('.',',',round($order_details_data[$i]->total,2))?></b></td>				
				</tr>
				<?php endfor;?>
				<?php if($orderData[0]->option == "2"):?>
				<tr>
					<td colspan="5" align="right" valign="top"><div align="right"><strong><?php echo _('Order Cost')?></strong></div></td>	
					<td align="right" valign="top"><b>&euro;&nbsp;<?=str_replace('.',',',round($total,2))?></b></td>
				</tr>
				<tr>
					<td colspan="5" align="right" valign="top"><div align="right"><strong><?php echo _('Delivery Cost')?></strong></div></td>	
					<td align="right" valign="top"><b>&euro;&nbsp;<span id="delivery_cost"><?php echo str_replace('.',',',round($orderData[0]->delivery_cost,2))?></span> </b></td>
				</tr>
				<?php endif;?>
				
				<!-- --------------- DISCOUNT PER CLIENT ------------------------------ -->
				<?php
					$disc_client_amount = 0;
					if($orderData[0]->disc_client_amount > 0 ){
						$disc_client_amount = $orderData[0]->disc_client_amount;
					}
				?>
				
				<?php if($disc_client_amount > 0 && $orderData[0]->disc_client > 0){?>
				<tr>
					<td colspan="5" align="right" valign="top"><div align="right"><strong><?php echo _('Loyality Discont')?>(<?php echo $orderData[0]->disc_client;?>%)</strong></div>:</td>
					<td align="right" valign="top"><b>-<?=round(( (float)$disc_client_amount ),2)?>&nbsp;&euro;</b></td>
				</tr>
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
					<td colspan="5" align="right" valign="top"><div align="right"><strong><?php echo _('Discount')?>(<?php echo $orderData[0]->disc_percent;?>%)</strong></div>:</td>
					<td align="right" valign="top"><b>-<?=round(( (float)$disc_amount ),2)?>&nbsp;&euro;</b></td>
				</tr>
				<?php }elseif($disc_amount > 0 && $orderData[0]->disc_price > 0){?>
				<tr>
					<td colspan="5" align="right" valign="top"><div align="right"><strong><?php echo _('Discount')?>(<?php echo $orderData[0]->disc_price;?>&euro;)</strong></div>:</td>
					<td align="right" valign="top"><b>-<?=round(( (float)$disc_amount ),2)?>&nbsp;&euro;</b></td>
				</tr>
				<?php }?>
				<!-- ------------------------------------------------------------------ -->
				
				<tr>
					<td colspan="5" align="right" valign="top"><div align="right"><strong><?php echo _('Total')?></strong></div></td>	
					<td align="right" valign="top"><b>&euro;&nbsp;<span id="total_cost"><?php echo str_replace('.',',',round(( ($total + $orderData[0]->delivery_cost) - ($disc_client_amount+$disc_amount) ),2))?></span> </b></td>
				</tr>
				<?php if($activate_discount_card):?>
				<tr>						
					<td colspan="6" align="left" valign="top"><strong><?php echo _('Discount Card Number')?>:</strong>&nbsp;<b><span id="discount_card_number"><?php echo ($discount_card_number)?$discount_card_number:'--';?></span> </b></td>
				</tr>
				<?php endif;?>
	      	</table></td>
		</tr>
	</table>      
    <p></p>
	<hr />
	<p></p>
    
<?php endif;?>

<script type="text/javascript">
	window.print();
</script>

</body>
</html>
