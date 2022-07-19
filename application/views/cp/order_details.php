<!--=========================this view will be shown in thick box in orders.php===========================-->
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/cp/new_css/style.css">

<div style="max-height:300px;padding-right:10px;">

	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="override">
		<tr>
			<td colspan="6" height="30" style="text-align:right">Bestelling doorgegeven om <?php echo date('H',strtotime($orderData[0]->created_date)).'u'.date('i',strtotime($orderData[0]->created_date));?></td>
		</tr>
		<tr>
			<td width="250" class="textlabel" style="padding-left:50px;text-align:left;"><?php echo _('PRODUCT')?></td>
			<td width="100" class="textlabel" style="text-align:center"><?php echo _('AMOUNT')?></td>
			<td width="100" style="text-align:center; font-size: 14px; font-weight: bold;"><?php echo _('RATE')?></td>
			<td width="200" style="text-align:center; font-size: 14px; font-weight: bold;"><?php echo _('EXTRA')?></td>
			<td width="120" class="textlabel" style="text-align:center"><?php echo _('Total')?></td>
		</tr>
		<tr>
			<td colspan="6">&nbsp;</td>
		</tr>
		<?php $order_weight = 0;
		$is_international = false;
		if(isset($orderData[0]->phone_reciever) && trim($orderData[0]->phone_reciever) != ''){
			$is_international = true;	
		}
		?>
		<?php for($i=0;$i<count($order_details_data);$i++):?>
		<tr>
			<td style="padding-left:20px;text-align:left;"><?php echo $order_details_data[$i]->proname?>
				&nbsp;
				<?php if($order_details_data[$i]->type == '1'):?>
					<img src="<?php echo base_url()?>assets/cp/images/new.png" alt="<?php echo _('New');?>" width="16" height="16" />
				<?php endif; ?>
				&nbsp;
				<?php if($order_details_data[$i]->discount != '' && $order_details_data[$i]->discount != '0'):?>
					<img src="<?php echo base_url()?>assets/cp/images/message.png" alt="<?php echo _('Discount');?>" width="16" height="16" title="<?php echo $order_details_data[$i]->discount?>" />
				<?php endif; ?>
			</td>
			<td style="padding-left:15px;text-align:center;"><?php echo $order_details_data[$i]->quantity?><?php if($order_details_data[$i]->content_type==1){echo '&nbsp;'._('Gr.');}?></td>
			<td style="text-align:center" width="100">
				<b><?php $actual_price = defined_money_format($order_details_data[$i]->sub_total); if( $order_details_data[$i]->content_type == '1'): echo $actual_price.'&nbsp;&euro;/Kg'; else: echo $actual_price.'&nbsp;&euro;'.( ($order_details_data[$i]->content_type == '2')?'/'._('Person'):'');  endif; ?></b>
			</td>
			<td width="200">
					<?php if($TempExtracosts != array() && isset($TempExtracosts[$i]) && $TempExtracosts[$i] != ""):?>
                    	<?php for($j=0;$j<count($TempExtracosts[$i]);$j++): 
  							echo "<span style=\"color:red;\">".$TempExtracosts[$i][$j][0]."</span> : ".$TempExtracosts[$i][$j][1]." = ".$TempExtracosts[$i][$j][2]."<br>";
	   					endfor; ?>
					<?php endif;?>  
				
				<?php if($order_details_data[$i]->pro_remark != ""):echo "<span style=\"color:red;\">". _('Remark')." :</span>&nbsp;<span>".stripslashes($order_details_data[$i]->pro_remark)."</span><br/>"; endif;?>
				<?php if(isset($order_details_data[$i]->extra_name) && $order_details_data[$i]->extra_name != ""):echo "<span style=\"color:red;\">".stripslashes($order_details_data[$i]->extra_field)." :</span>&nbsp;<span>".stripslashes($order_details_data[$i]->extra_name)."</span><br><br>"; endif;?>
			</td>
			<td style="text-align:right;"><b><?php echo defined_money_format($order_details_data[$i]->total)?></b>&nbsp;&euro;
			<?php if($is_international){
					$order_weight += ($order_details_data[$i]->content_type==1)?($order_details_data[$i]->quantity)/1000:($order_details_data[$i]->weight_per_unit)*($order_details_data[$i]->quantity);
			}?></td>
		</tr>
		<?php endfor;?>
		<?php if($orderData[0]->option == "2"):  // If Service Type - Delivery ?> 
		<tr>
			<td colspan="4" style="text-align:right;margin-right:10px"><strong><?php echo  _('TOTAL COST')?></strong>:</td>
			<td style="text-align:right"><b><?=defined_money_format($total)?>&nbsp;&euro;</b></td>
		</tr>
		
		<?php if($is_international){ ?>
		<tr>
			<td colspan="4" style="text-align:right;margin-right:10px"><strong><?php echo _('DELIVERY WEIGHT')?></strong>:</td>
			<td style="text-align:right;"><b><?=($order_weight).'&nbsp;Kg'?></b></td>
		</tr>
		<?php }	?>
		
		<tr>
			<td colspan="4" style="text-align:right;margin-right:10px"><strong><?php echo _('DELIVERY COST')?></strong>:</td>
			<td style="text-align:right;"><b><?=defined_money_format($orderData[0]->delivery_cost)?>&nbsp;&euro;</b></td>
		</tr>
		
		<!-- --------------- DISCOUNT PER CLIENT ------------------------------ -->
		<?php
			$disc_client_amount = 0;
			if($orderData[0]->disc_client_amount > 0 ){
				$disc_client_amount = $orderData[0]->disc_client_amount;
			}
		?>
		
		<?php if($disc_client_amount > 0 && $orderData[0]->disc_client > 0){?>
		<tr>
			<td colspan="4" style="text-align:right;margin-right:10px"><strong><?php echo _('Loyality Discont')?>(<?php echo $orderData[0]->disc_client;?>%)</strong>:</td>
			<td style="text-align:right;"><b>-<?=defined_money_format($disc_client_amount)?>&nbsp;&euro;</b></td>
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
			<td colspan="4" style="text-align:right;margin-right:10px"><strong><?php echo _('Introcode Discount')?></strong>:</td>
			<td style="text-align:right;"><b>-<?=defined_money_format($disc_other_code);?>&nbsp;&euro;</b></td>
		</tr>
		<?php }?>
		<?php if($orderData[0]->other_code_type == 'promocode'){?>
		<tr>
			<td colspan="4" style="text-align:right;margin-right:10px"><strong><?php echo _('Promocode Discount')?></strong>:</td>
			<td style="text-align:right;"><b>-<?=defined_money_format($disc_other_code);?>&nbsp;&euro;</b></td>
		</tr>
		<?php }?>
		<?php }?>
		<!-- ------------------------------------------------------------------ -->
									
		<!-- ---------------- DISCOUNT PER AMOUNT ------------------------------ -->
		<?php
			$disc_amount = 0;
			if($orderData[0]->disc_amount > 0 ){
				$disc_amount = $orderData[0]->disc_amount;
			}
		?>
		
		<?php if($disc_amount > 0 && $orderData[0]->disc_percent > 0){?>
		<tr>
			<td colspan="4" style="text-align:right;margin-right:10px"><strong><?php echo _('Discount')?>(<?php echo $orderData[0]->disc_percent;?>%)</strong>:</td>
			<td style="text-align:right;"><b>-<?=defined_money_format($disc_amount)?>&nbsp;&euro;</b></td>
		</tr>
		<?php }elseif($disc_amount > 0 && $orderData[0]->disc_price > 0){?>
		<tr>
			<td colspan="4" style="text-align:right;margin-right:10px"><strong><?php echo _('Discount')?>(<?php echo $orderData[0]->disc_price;?>&euro;)</strong>:</td>
			<td style="text-align:right;"><b>-<?=defined_money_format($disc_amount)?>&nbsp;&euro;</b></td>
		</tr>
		<?php }?> 
		<!-- ------------------------------------------------------------------- -->
		
		<?php $grand_total = defined_money_format(round($total,2)+round($orderData[0]->delivery_cost,2) - (round($disc_amount,2)+round($disc_other_code,2)+round($disc_client_amount,2))); ?>
		
		<tr>		
			<td colspan="4" style="text-align:right;margin-right:10px;border-top:1px solid #000;"><strong><?= _('GRAND TOTAL')?></strong>:</td>
			<td style="text-align:right;border-top:1px solid #000;"><b><?=$grand_total?>&nbsp;&euro;</b></td>
		</tr>
		
		<?php elseif($orderData[0]->option == "1"):   // If Service Type - Pickup  ?>
		 
		<tr>
			<td colspan="4" style="text-align:right;margin-right:10px"><strong><?php echo  _('TOTAL COST')?></strong>:</td>
			<td style="text-align:right"><b><?=defined_money_format($total)?>&nbsp;&euro;</b></td>
		</tr>
		
		<!-- --------------- DISCOUNT PER CLIENT ------------------------------ -->
		<?php
			$disc_client_amount = 0;
			if($orderData[0]->disc_client_amount > 0 ){
				$disc_client_amount = $orderData[0]->disc_client_amount;
			}
		?>
		
		<?php if($disc_client_amount > 0 && $orderData[0]->disc_client > 0){?>
		<tr>
			<td colspan="4" style="text-align:right;margin-right:10px"><strong><?php echo _('Loyality Discont')?>(<?php echo $orderData[0]->disc_client;?>%)</strong>:</td>
			<td style="text-align:right;"><b>-<?=defined_money_format($disc_client_amount )?>&nbsp;&euro;</b></td>
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
			<td colspan="4" style="text-align:right;margin-right:10px"><strong><?php echo _('Introcode Discount')?></strong>:</td>
			<td style="text-align:right;"><b>-<?=defined_money_format($disc_other_code);?>&nbsp;&euro;</b></td>
		</tr>
		<?php }?>
		<?php if($orderData[0]->other_code_type == 'promocode'){?>
		<tr>
			<td colspan="4" style="text-align:right;margin-right:10px"><strong><?php echo _('Promocode Discount')?></strong>:</td>
			<td style="text-align:right;"><b>-<?=defined_money_format($disc_other_code);?>&nbsp;&euro;</b></td>
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
			<td colspan="4" style="text-align:right;margin-right:10px"><strong><?php echo _('Discount')?>(<?php echo $orderData[0]->disc_percent;?>%)</strong>:</td>
			<td style="text-align:right;"><b>-<?=defined_money_format($disc_amount)?>&nbsp;&euro;</b></td>
		</tr>
		<?php }elseif($disc_amount > 0 && $orderData[0]->disc_price > 0){?>
		<tr>
			<td colspan="4" style="text-align:right;margin-right:10px"><strong><?php echo _('Discount')?>(<?php echo $orderData[0]->disc_price;?>&euro;)</strong>:</td>
			<td style="text-align:right;"><b>-<?=defined_money_format($disc_amount)?>&nbsp;&euro;</b></td>
		</tr>
		<?php }?>
		<!-- ------------------------------------------------------------------ -->
		
		<?php $grand_total = defined_money_format( round($total,2) - ( round($disc_amount,2)+round($disc_other_code,2)+round($disc_client_amount,2) )); ?>
		
		<?php if($disc_amount > 0){?>
		<tr>		
			<td colspan="4" style="text-align:right;margin-right:10px;border-top:1px solid #000;"><strong><?= _('GRAND TOTAL')?></strong>:</td>
			<td style="text-align:right;border-top:1px solid #000;"><b><?=$grand_total?>&nbsp;&euro;</b></td>
		</tr>
		<?php }?>
		 
		<?php endif; ?>
		
		<tr>
			<td colspan="6" height="30">&nbsp;</td>
		</tr>
		<?php if($orderData['0']->order_remarks != ''){?>
		<tr>
			<td colspan="6" height="30" style="text-align: center;"><?php echo _('Remark');?>: <?php echo stripslashes($orderData['0']->order_remarks);?></td>
		</tr>
		<?php }?>
	</table>
</div>