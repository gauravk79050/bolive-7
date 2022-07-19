


	<div class="title_head">
		<div class="top_head">
			<span><?php echo $company_name; ?></span>
		</div>
		<div class="bot_head">
			<span><?php echo _('Overview of order per client'); ?></span>
		</div>
	</div>
		

 <div>
 <?php
	if (! empty ( $orderData )) {
		
		$count = 1;
		
		foreach ( $orderData as $order ) {
			$total = 0.00;
			//print_r ( $order ['client_details'] [0] );
			
			if(!empty($order ['order_details'])){
				foreach($order ['order_details'] as $order1){
					//$o_total = ($order1->order_total + $order1->pic_apply_tax + $order1->del_apply_tax + $order1->delivery_cost) - ($order1->disc_amount + $order1->disc_client); 		
					$total += $order1->order_total1;
				}
			}
			
			
			?>
			<table class="order_table" cellspacing="0" cellpadding="0" style="line-height:10px;">
			
			<?php 
			$max_row=0;
			if(!empty($order ['order_details'])){

				$order_count = count($order ['order_details']);
				
				$max_row = max($order_count, 6);
				
				/*
				foreach($order ['order_details'] as $order){
					echo "<p>"._('Order on').date('d/m/Y', strtotime($order->created_date))." = ".$order->order_total."&euro;</p>";
					$total += $order->order_total;
				}
				*/
			}
			
			?>
			
			<?php for($i = 0; $i<$max_row; $i++){ ?>	
			
			<tr>
				<td style="width: 300px;">
					<?php if($i == 0){ ?>
					<p class="c_name"><?php echo  $order ['client_details'] [0]->company_c; ?></p>
					<?php } elseif($i == 1) {?>
					<p><?php echo  $order ['client_details'] [0]->lastname_c." ".$order ['client_details'] [0]->firstname_c; ?></p>
					<?php } elseif($i ==2) {?>
					<p><?php echo  $order ['client_details'] [0]->address_c; ?></p>
					<?php } elseif($i == 3) {?>
					<p><?php echo  $order ['client_details'] [0]->city_c; ?></p>
					<?php } elseif($i == 4) {?>
					<p><?php echo  $order ['client_details'] [0]->phone_c; ?></p>
					<?php } elseif($i == 5) {?>
					<p><?php echo  $order ['client_details'] [0]->email_c; ?></p>
					<?php } else {?>
					&nbsp;
					<?php }?>
				</td>
				
				
				<td style="width: 350px;">
					<?php if(isset($order['order_details'][$i]->order_total)): 
						echo "<p>"._('Order on ').date('d/m/Y', strtotime($order['order_details'][$i]->created_date))." = ".defined_money_format($order['order_details'][$i]->order_total1)."&euro;</p>";
						//$total += $order['order_details'][$i]->order_total;
					endif; ?>			
				</td>
				
				
				
				<td style="width: 250px;" class="total">
					<?php if($i == 1){ ?>
						<?php echo _('Total = ').defined_money_format($total)."&euro;"; ?>
					<?php } ?>
				</td>
				
					
				</tr>
				<?php } ?>
				
				</table>
		<?php 	
		
		}
	}

	?>
		
						
		</div>
 
 