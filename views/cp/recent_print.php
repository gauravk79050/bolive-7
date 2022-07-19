<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="<?php echo base_url()?>assets/cp/new_css/style.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url()?>assets/cp/new_css/table.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url()?>assets/cp/new_css/print.css" rel="stylesheet" type="text/css" media="print" />
<script src="<?php echo base_url(); ?>assets/cp/js/jquery-1.6.2.min.js?version=<?php echo version;?>" type="text/javascript"></script>
<script type="text/javascript">
	var base_url="<?php echo base_url();?>";<!--this is for the js files included in header -->
</script>
<script src="<?php echo base_url();?>assets/cp/new_js/print_recent_orders.js?version=<?php echo version;?>"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo _('Print Order Data'); ?></title>
<style type="text/css">
	.break {page-break-after: always}
</style>
</head>
<body id="printing_order" class="auto_print">

<?php if(isset($orderData) && !empty($orderData)):?>
	
	<?php for($j=0;$j<count($orderData);$j++): ?>
    <table cellspacing="2" cellpadding="2" border="0" id="single-order" align="center">		
		<tr>
			<td><table width="100%" border="0" cellspacing="0" cellpadding="">		
				<tr>
					<td style="font-size:12px;">
						<?php echo _('A new order was passed through the shop')?>
					</td>
				</tr>
				<tr>
					<td style="font-size:12px;">
						<?php echo _('Here are some details:')?>
					</td>
				</tr>
				<tr>
					<td></td>
				</tr>				
				<tr>
					<td width="52%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2">
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
					       </td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>		
						<tr>
							<td bgcolor="#F0F0F0"><b><?php echo _('Customer')?>:</b></td>		
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
								<br/>
								<strong><?php echo _('GSM'); ?></strong>:&nbsp;<?php echo $orderData[$j][0]->mobile_c?>
								<br/>
								<strong> <?php echo _('Telephone No')?></strong>:&nbsp;<?php echo $orderData[$j][0]->phone_c?>
								<br/>								
								<strong><?php echo _('Email Customer')?></strong>:&nbsp;<?php echo $orderData[$j][0]->email_c?>&nbsp;-&nbsp;<a class="edit" href="mailto:<?php echo $orderData[$j][0]->email_c?>?subject=Re: <?php echo _('your order')?>  <?php echo $orderData[$j][0]->id?>&amp;body=<?php echo _('Dear client')?>,"><?php echo _('Email')?></a></span>
							</td>		
						</tr>	
						<tr>
							<td>&nbsp;</td>
						</tr>
						<?php if($orderData[$j][0]->option == "2"):?>
						<tr>
							<td><table width="100%" cellpadding="0" cellspacing="0" border="0" >
								<tr>
									<td style="padding:3px 0px 3px 36px;">
										<ul style="list-style: outside !important;">
											<li><strong><?php echo _('Delivery Date')?>:</strong><?php 
												$da = date("d",strtotime($orderData[$j][0]->delivery_date));
												$mo_eng = date("F",strtotime($orderData[$j][0]->delivery_date));
												$yr = date("y",strtotime($orderData[$j][0]->delivery_date));
												$date_dutch = $da." ".$mo_eng." `".$yr;
												if(isset($date_dutch) && ($date_dutch != ""))
												{
													echo ' <strong>:</strong>'.$date_dutch;
												}
												?>
											</li>
											<li><strong><?php echo _('Delivery Day')?>:</strong><?php 
												$day = $orderData[$j][0]->delivery_day.' '._('to').' '.$orderData[$j][0]->delivery_hour.' '. _('HOURS').' '.$orderData[$j][0]->delivery_minute.' '._('MINS');
												if(isset($day) && ($day != ""))
												{
													echo ' <strong>:</strong>'.$day;
												}
												?>
											</li>
											<li><strong><?php echo _('Delivery Address')?>:</strong><?php 
												if((isset($orderData[$j][0]->delivery_streer_address) && ($orderData[$j][0]->delivery_streer_address != '')) || isset($orderData[$j][0]->delivery_area_name) || isset($orderData[$j][0]->delivery_zip))
												{
													echo ' <strong>:</strong>';
												}
												echo $orderData[$j][0]->delivery_streer_address;
												if(isset($orderData[$j][0]->delivery_area_name)) { echo ' '.$orderData[$j][0]->delivery_area_name; } 
												echo ' '.$orderData[$j][0]->delivery_zip;
												if(isset($orderData[$j][0]->delivery_city_name)) { echo ' '.$orderData[$j][0]->delivery_city_name; } 							
												?>
											</li>
											<li><strong><?php echo _('Remarks')?>:</strong><?php
												if((isset($orderData[$j][0]->delivery_remarks)) && ($orderData[$j][0]->delivery_remarks != "")) 
												echo ' <strong>:</strong>'.$orderData[$j][0]->delivery_remarks;
												?>
											</li>
										</ul>
									</td>
								</tr>
								</table>
							</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
							</tr>
						<?php elseif($orderData[$j][0]->option == "1"):?>
						<tr>
							<td><table width="100%" cellpadding="0" cellspacing="0" border="0" class="override">
								<tr>
									<td style="padding:3px 0px 3px 36px;">
										<ul style="list-style: outside !important;">
											<li><strong><?php echo _('Pick Date')?>:</strong><?php 
												$da = date("d",strtotime($orderData[$j][0]->order_pickupdate));
												$mo_eng = date("F",strtotime($orderData[$j][0]->order_pickupdate));
												$yr = date("y",strtotime($orderData[$j][0]->order_pickupdate));
												$date_dutch = $da." ".$mo_eng." `".$yr;
												if(isset($date_dutch) && ($date_dutch != ""))
												{
													echo ' <strong>:</strong>'.$date_dutch;
												}
												?>
											</li>
											<li><strong><?php echo _('Pick Day')?>:</strong><?php 
												$day = $orderData[$j][0]->order_pickupday.' '._('to').' '.$orderData[$j][0]->order_pickuptime;
												if(isset($day) && ($day != ""))
												{
													echo ' <strong>:</strong>'.$day;
												}
												?>
											</li>
											<li><strong><?php echo _('Note')?>:</strong><?php
												if(isset($orderData[$j][0]->order_remarks) && ($orderData[$j][0]->order_remarks != ""))
												{ 
													echo ' <strong>:</strong>'.$orderData[$j][0]->order_remarks;
												}?>
											</li>																							
										</ul>
									</td>
								</tr>
							</table></td>
						</tr>	
						<?php elseif($orderData[$j][0]->option == "0"):?>
						<tr>
							<td><table width="100%" cellpadding="0" cellspacing="0" border="0" class="override">
								<tr>
									<td style="padding:3px 0px 3px 36px;">
										<ul style="list-style: outside !important;">
											<li><strong><?php echo _('Date')?>:</strong><?php 
												$da = date("d",strtotime($orderData[$j][0]->order_pickupdate));
												$mo_eng = date("F",strtotime($orderData[$j][0]->order_pickupdate));
												$yr = date("y",strtotime($orderData[$j][0]->order_pickupdate));
												$date_dutch = $da." ".$mo_eng." `".$yr;
												if(isset($date_dutch) && ($date_dutch != ""))
												{
													echo ' <strong>:</strong>'.$date_dutch;
												}
												?>
											</li>
											<li><strong><?php echo _('Order Hours')?>:</strong><?php 
												if(isset($orderData[$j][0]->order_pickuptime) && ($orderData[$j][0]->order_pickuptime != ""))
												{
													echo ' <strong>:</strong>'.$orderData[$j][0]->order_pickuptime.' '. _('HOUR');
												}?>
											</li>
											<li><strong><?php echo _('Note')?>:</strong><?php 
												if(isset($orderData[$j][0]->order_remarks) && ($orderData[$j][0]->order_remarks != ""))
												{
													echo ' <strong>:</strong>'.$orderData[$j][0]->order_remarks;
												}
												?>
											</li>
										</ul>
									</td>
								</tr>
					  		</table></td>
						</tr>	
						<? endif; ?>	
					</table></td>		
		   		</tr>
		  	</table><!--</td>		
		</tr>
		<tr>		
			<td>--><table border="0" cellpadding="0" cellspacing="0" width="100%" class="detail_product">			
				<tr>
					<td width="34%" bgcolor="#F2F2F2"><strong><?php echo _('Product')?></strong></td>
				  	<td width="5%" align="left" bgcolor="#F2F2F2"><strong><?php echo _('Quantity')?></strong></td>
				  	<td width="11%" align="center" bgcolor="#F2F2F2"><strong><?php echo _('Default Price')?></strong></td>
				  	<td width="40%" align="left" bgcolor="#F2F2F2"><strong><?php echo _('Remark')?></strong></td>			
				  	<td width="2%" align="right" bgcolor="#F2F2F2">&nbsp;</td>	
					<td width="8%" align="right" bgcolor="#F2F2F2"><strong><?php echo _('Total')?></strong></td>		
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
							<span style="color:red;"><?php echo $TempExtracosts[$j][$i][$n][0]?>:</span>&nbsp;<?php echo $TempExtracosts[$j][$i][$n][1].'='.$TempExtracosts[$j][$i][$n][2] ;?><br>
							<?php endfor; ?>
						<?php endif;?>
						<?php if($order_details_data[$j][$i]->discount == "multi" && !empty($order_details_data[$j][$i]->discount_per_qty)):?>
					<br><span style="color:red;"><?php echo _('Extra Discount')?>:</span>&nbsp;<?php echo $order_details_data[$j][$i]->discount_per_qty?>&nbsp;		
						<?php endif;?>
						<?php if($order_details_data[$j][$i]->pro_remark != ""):?>
					<span><strong><?php echo _('Remark');?>:</strong>&nbsp;<?php echo $order_details_data[$j][$i]->pro_remark?></span><br><br>
						<?php elseif(($order_details_data[$j][$i]->pro_remark == "") && (empty($TempExtracosts[$j][$i]) || $TempExtracosts[$j][$i] == "")):?><span>--</span><br><br>
						<?php endif;?>
					
					</td>
                    <td align="right" valign="middle">&nbsp;</td>	
					<td align="right" valign="top"><b><?php echo str_replace('.',',',round($order_details_data[$j][$i]->total,2)); ?>&euro;</b></td>				
		    	</tr>
				<?php endfor;?>
				<?php if($orderData[$j][0]->option == "2"):?>
				<tr>
					<td colspan="5" align="right" valign="top"><div align="right"><?php echo _('Total')?></div></td>	
					<td align="right" valign="top"><div align="left">&nbsp;<?php echo str_replace('.',',',round($total[$j],2)); ?>&euro;</div></td>
				</tr>
				<tr>
					<td colspan="5" align="right" valign="top" style="border: medium none;"><div align="right"><?php echo _('Delivery Rate')?></div></td>	
					<td align="right" valign="top" style="border: medium none;">&nbsp;<span id="delivery_cost"><?php echo str_replace('.',',',round($orderData[$j][0]->delivery_cost,2)); ?>&euro;</span></td>
				</tr>
				<?php endif;?>
				<tr>
					<td colspan="5" align="right" valign="top" style="border: medium none;"><div align="right"><strong><?php echo _('Total')?></strong></div></td>	
					<td align="right" valign="top" style="border: medium none;"><div align="left">&nbsp;<span id="total_cost"><?php echo str_replace('.',',',round(($total[$j]+$orderData[$j][0]->delivery_cost),2)); ?>&euro;</span></div></td>
				</tr>
				<?php if($activate_discount_card):?>
				<tr>
					<td colspan="6" align="left" valign="top" style="border: medium none;"><?php echo _('Sincerely')?>,</td>
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

<script type="text/javascript">
	window.print();
</script>
<?php else:?>
<div style="margin-top: 130px; margin-left:20px; font-size:25px;">
<b>No recent record found!</b>
</div>
<?php endif;?>

</body>
</html>
