<?php if(!empty($orderData)){ $finalTotal = 0;?>
<div class="summary">
	<table cellspacing="0" border="0" id="prod_list" width="100%">
		<?php foreach($orderData as $order) { ?>
            <tr>
              <td width="30%" valign="top">
                <p class="order_number"><span class="bold small"><?php echo _("Order No.");?></span> <span><?php echo $order->id;?></span></p>
                <br>
                <span><?php echo $order->firstname_c.' '.$order->lastname_c;?></span>
                <?php if($order->address_c != ''){?>
                <br/>
                <span><?php echo $order->address_c; ?></span> <span><?php echo $order->housenumber_c;?></span>
                <?php }?>
                <?php if($order->city_c != ''){?>
                <br/>
                <span><?php echo $order->city_c;?></span>
                <?php }?>
                <?php if($order->mobile_c != '' || $order->phone_c != ''){?>
                <br/>
                <span><?php echo (($order->mobile_c)?$order->mobile_c:$order->phone_c);?></span>
                <?php }?>
                <br />
                <span class="bold small"><?php echo $order->email_c;?></span>
              </td>
              <td width="50%" valign="top" >
			    <?php foreach($order->order_details as $order_detail){?>
                <span class="bold medium">
                <?php if($order_detail->content_type != '1'){ echo $order_detail->quantity; }else{ echo ($order_detail->quantity/1000);}?>
                <?php if($order_detail->content_type == '1'){ echo _("Kg");}elseif($order_detail->content_type == '2'){ echo _("Person"); } ?>
                x <?php echo stripslashes($order_detail->proname)?></span> <span><?php echo _("Price");?>: <?php echo $order_detail->quantity;?> x <?php echo defined_money_format($order_detail->default_price);?> &euro; = <?php echo defined_money_format($order_detail->total);?>&euro;</span>
                <?php if($order_detail->pro_remark){?>
                <br />
                <span><?php echo $order_detail->pro_remark;?></span>
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
                <br> <br> <?php $finalTotal = $finalTotal + $order->order_total;?>
                <span class="bold large" style="display:block; text-align: right;"><?php echo _("Total");?> = <?php echo defined_money_format($order->order_total);?> &euro;</span></td>
            </tr>
            <?php } ?>
            <tr>
            	<td colspan="3" style="text-align:right;border-top: 2px solid #000000;">
            		<span class="bold large"><?php echo _("Total");?> : <?php echo defined_money_format($finalTotal);?> &euro;</span>
            	</td>
            </tr>
	</table>
</div>
<?php }?>