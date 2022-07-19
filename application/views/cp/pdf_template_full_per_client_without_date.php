


	<div class="title_head">
		<div class="top_head">
			<span><?php echo $company_name; ?></span>
		</div>
		<div class="bot_head">
			<span><?php echo _('Overview of order per client'); ?></span>
		</div>
	</div>


 <?php
if(!empty($order_detail))
{ $finalTotal = 0;?>
	<div class="summary">
		<table cellspacing="0" border="0" id="prod_list" width="100%">
			<?php
			foreach($order_detail as $key=>$order)
			{ ?>
	            <tr>
	              <td width="30%" valign="top">
	                <p class="order_number"><span class="bold small"><?php echo _("Order No.");?></span> <span><?php echo $order->id;?></span></p>
	                <br>
	                <span><?php echo $order_client->firstname_c.' '.$order_client->lastname_c;?></span>
	                <?php if($order_client->address_c != ''){?>
	                <br/>
	                <span><?php echo $order_client->address_c; ?></span> <span><?php echo $order_client->housenumber_c;?></span>
	                <?php }?>
	                <?php if($order_client->city_c != ''){?>
	                <br/>
	                <span><?php echo $order_client->city_c;?></span>
	                <?php }?>
	                <?php if($order_client->mobile_c != '' || $order_client->phone_c != ''){?>
	                <br/>
	                <span><?php echo (($order_client->mobile_c)?$order_client->mobile_c:$order_client->phone_c);?></span>
	                <?php }?>
	                <br />
	                <span class="bold small"><?php echo $order_client->email_c;?></span>
	              </td>
	              <td width="50%" valign="top" >
				    <?php foreach($order->order_description as $order_details){?>
	                <span class="bold medium">
	                <?php if($order_details->content_type != '1'){ echo $order_details->quantity; }else{ echo ($order_details->quantity/1000);}?>
	                <?php if($order_details->content_type == '1'){ echo _("Kg");}elseif($order_details->content_type == '2'){ echo _("Person"); } ?>
	                x <?php echo $order_details->proname?></span> <span><?php echo _("Price");?>: <?php if($order_details->content_type != '1'){ echo $order_details->quantity; }else{ echo ($order_details->quantity/1000);}?> x <?php echo defined_money_format($order_details->default_price);?> &euro; = <?php echo defined_money_format($order_details->total);?>&euro;</span>
	                <?php if($order_details->pro_remark){?>
	                <br />
	                <span><?php echo $order_details->pro_remark;?></span>
	                <?php }?>
	                <br/>
	                <?php }?>
	              </td>
	              <td width="20%" valign="top">
	              <span class="bold medium underline">
	                <?php
						if($order->order_pickupdate != "0000-00-00"){
							echo _("Pickup"); }else{ echo _("Delivery");
						}
					 ?>
	                </span>
	                <p class="order_date"><span><?php

					  if($order->order_pickupdate != "0000-00-00"){
						  $month = date("F",strtotime($order->order_pickupdate));
						  echo date("d",strtotime($order->order_pickupdate));
					  }else{
						  $month = date("F",strtotime($order->delivery_date));
						  echo date("d",strtotime($order->delivery_date));
					  }

					  if(isset($_COOKIE['locale']) && $_COOKIE['locale'] == 'nl_NL')
					  {
						  if( $month == "January" ){
							  $month = 'Januari';
						  }
						  if( $month == "February" ){
							  $month = 'Februari';
						  }
						  if( $month == "March" ){
							  $month = 'Maart';
						  }
						  if( $month == "May" ){
							  $month = 'Mei';
						  }
						  if( $month == "June" ){
							  $month = 'Juni';
						  }
						  if( $month == "July" ){
							  $month = 'Juli';
						  }
						  if( $month == "August" ){
							  $month = 'Augustus';
						  }
						  if( $month == "October" ){
							  $month = 'Oktober';
						  }

					  }
					  echo ' '.$month.' ';

					  if($order->order_pickupdate != "0000-00-00"){
						  echo "om ".$order->order_pickuptime;
					  }else{
						  echo "om ".$order->delivery_hour.":".$order->delivery_minute;
					  }
				  ?>
	                </span>
	                </p>
	                <br> <br> <?php $finalTotal = $finalTotal + $order->order_total1;?>
	                <span class="bold large" style="display:block; text-align: right;"><?php echo _("Total");?> = <?php echo defined_money_format($order->order_total1);?> &euro;</span></td>
	            </tr>
	            <?php

	        }
	        ?>
		            <tr>
		            	<td colspan="3" style="text-align:right;border-top: 2px solid #000000;">
		            		<span class="bold large"><?php echo _("Total");?> : <?php echo defined_money_format($finalTotal);?> &euro;</span>
		            	</td>
		            </tr>
		</table>
	</div>
<?php
}
?>

