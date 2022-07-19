<!--=========================this view will be shown in thick box in orders.php===========================-->
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/cp/new_css/style.css">

<div style="max-height:300px;padding-right:10px;">

	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="override">
		<tr>
			<td colspan="6" height="30">&nbsp;</td>
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
		<?php $grand_total = 0; ?>
		<?php for($i=0;$i<count($order_details_data);$i++):?>
		<tr>
			<td style="padding-left:20px;text-align:left;"><?php echo stripslashes($order_details_data[$i]->proname); ?>
				&nbsp;
				<?php if($order_details_data[$i]->type == '1'):?>
					<img src="<?php echo base_url()?>assets/cp/images/new.png" alt="<?php echo _('New');?>" width="16" height="16" />
				<? endif; ?>
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
                    if($TempExtracosts[$i][$j][0] != '0'):
  							echo "<span style=\"color:red;\">".$TempExtracosts[$i][$j][0]."</span> : ".$TempExtracosts[$i][$j][1]." = ".$TempExtracosts[$i][$j][2]."<br>";
  						endif;
	   					endfor; ?>
				<?php endif;?>  
				
				<?php if($order_details_data[$i]->pro_remark != ""):echo "<span style=\"color:red;\">". _('Remark')." :</span>&nbsp;<span>".$order_details_data[$i]->pro_remark."</span><br/>"; endif;?>
			</td>
			<td style="text-align:right;"><b><?php echo defined_money_format($order_details_data[$i]->total)?></b>&nbsp;&euro;</td>
		</tr>
		<?php 
			$grand_total += $order_details_data[$i]->total; 
		endfor;?>
		
		
		<tr>
			<td colspan="6" height="30">&nbsp;</td>
		</tr>
		<tr>		
			<td colspan="4" style="text-align:right;margin-right:10px;border-top:1px solid #000;"><strong><?= _('GRAND TOTAL')?></strong>:</td>
			<td style="text-align:right;border-top:1px solid #000;"><b><?=defined_money_format($grand_total)?>&nbsp;&euro;</b></td>
		</tr>
	</table>
</div>