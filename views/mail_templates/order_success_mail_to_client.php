<html>
	<head></head>
	<body>
		<?php echo $orderreceived_msg;?>
		<br /><br />
		
		<table width="100%" cellspacing="2" cellpadding="2" border="0">
  			<tr>
    			<td>
    				<table width="100%" border="0" cellspacing="0" cellpadding="2">
						<tr>
          					<td align="left">
          						<strong><?php echo $this->lang->line('mail_order_no');?></strong> : <?php echo $order_id;?>
          					</td>
          					<td style="text-align: right;" >
          						<span style="font-weight:bold; font-size:15px;"><?php //color:#FF0004;?> 
          						<?php 
                					if($order_data['payment_via_paypal'] == '2' && isset($order_data['billing_option'])){
                						
                						if(isset($order_data['payment_status']) && $order_data['payment_status'] == 1){
                							//if($order_data['payment_status'] == 1)
                								echo '<span style="color:#6F9300; font-weight:bold; font-size:20px;">'.$this->lang->line("paid_txt").'</span><br/>';
                							//else
                								//echo '<span style="color:#AA0000; font-weight:bold; font-size:20px;">'.$this->lang->line("unpaid_txt").'</span><br/>';
                								// echo $this->lang->line('via_cardgate_pending');
                								echo $this->lang->line('mail_via').' '.$order_data['billing_option'];
                						}else{
                							echo '<span style="color:#AA0000; font-weight:bold; font-size:20px;">'.$this->lang->line("unpaid_txt").'</span><br/>';
                							echo '<a href="'.$order_data['shop_url'].'#/payment?action=repayment&oid='.$order_data['temp_id'].'"><button type="button" style="background-color:#AA0000;border-radius: 12px;border: medium none;font-size: 12px;margin: 16px 0;padding: 5px 10px;text-align: center;color:#ffffff;cursor: pointer;">'.$this->lang->line('pay_order').'</button></a>';
                							echo $this->lang->line('mail_via').' '.$order_data['billing_option'];
                						}
									}elseif($order_data['payment_via_paypal'] == '1'){
										echo $this->lang->line('mail_via').' '.$this->lang->line('paypal_txt');
										//echo $this->lang->line('via_paypal_pending');
									}
								?>
								</span>
							</td>
        				</tr>
        				<tr>
          					<td align="left">
          						<strong><?php echo $this->lang->line('mail_order_date');?></strong> : <?php echo date('d-m-Y',strtotime($order_data['created_date']));?>
          					</td>
        				</tr>
        				<tr>
          					<td align="left">
          						<strong>IP Address</strong> : <?php echo $ip_address;?> 
          						<br />
            					<br />
            				</td>
        				</tr>
        				<tr>
          					<td width="100%" valign="top" colspan="2">
          						<table width="100%" border="0" cellspacing="0" cellpadding="2">
              						<tr>
                						<td bgcolor="#F0F0F0"><b><?php echo $this->lang->line('mail_customer_data');?>:</b></td>
                						<?php /* ?>
                						<td bgcolor="#F0F0F0" style="text-align: right;" >
                							<span style="color:#FF0004; font-weight:bold; font-size:15px;"> 
                							<?php 
                								if($order_data['payment_via_paypal'] == '2' && isset($order_data['billing_option'])){
													// echo $this->lang->line('via_cardgate_pending');
													echo $this->lang->line('mail_via').' '.$order_data['billing_option'].( ($order_data['payment_status'] != '1')?' : '.$this->lang->line('pending_txt'):'' );
												}elseif($order_data['payment_via_paypal'] == '1'){
													echo $this->lang->line('via_paypal_pending');
												}
											?>
											</span>
										</td>
										<?php */ ?>
              						</tr>
              						<tr>
                						<td>
				  							<strong>
					  							<?php echo $client->company_c;?><br />
												<?php echo stripslashes($client->firstname_c);?>&nbsp;<?php echo stripslashes($client->lastname_c);?><br />
								                <?php echo stripslashes($client->address_c);?>&nbsp;<?php echo stripslashes($client->housenumber_c);?><br />
								                <?php echo $client->postcode_c;?> - <?php echo stripslashes($client->city_c);?><br/>
								                <?php echo $client->country_c;?><br/>
											</strong>
			    						</td>
              						</tr>
              						<tr>
              						<td><strong>
              						<?php  if($order_data['get_invoice']){
								               echo $this->lang->line('invoice_want_txt')." : ".$this->lang->line('yes_txt')."<br/>";
								               echo $this->lang->line('vat_txt')." : ".$client->vat_c."<br/>";
								            } 
								        ?><br />
								                <?php if( $order_data['option'] == '1' ){?>								                
								                <?php if($order_data['name'] != ''){?>
								                	<?php echo stripslashes($order_data['name']);?><br />
								             <?php }}?>
								                <?php echo $this->lang->line('mail_mobile');?> :&nbsp;<?php echo $client->phone_c;?><br />
												<?php /*echo $this->lang->line('mail_mobile');?> :&nbsp;<?php echo $client->mobile_c;?>
												<?php echo $this->lang->line('mail_phone');?> :&nbsp;<?php echo $client->phone_c;?><br /><?php */ ?>
												<?php echo $this->lang->line('mail_email');?> :&nbsp;<?php echo $client->email_c;?><br /><br/>
              						</strong></td>              						
              						</tr>
								</table>
							</td>	
        				</tr>
      				</table>
      				<?php if( $order_data['option'] == '1' ){?>
					<ul>
						<li><strong><?php echo $this->lang->line('mail_pickup_date'); ?> :</strong> <?php echo $order_data['order_pickupday'].' '.date('d/m/Y',strtotime($order_data['order_pickupdate'].' 00:00:00')).' '.$this->lang->line('mail_day_on').' '.$order_data['order_pickuptime'].' '.$this->lang->line('mail_time_hour'); ?></li>
						<li><strong><?php echo $this->lang->line('mail_pickup_note'); ?> :</strong> <?php echo stripslashes($order_data['order_remarks']); ?></li>
						<li><strong><?php echo $this->lang->line('mail_shop'); ?> :</strong> <?php echo $company_name; ?></li>
					</ul>
					<?php }elseif( $order_data['option'] == '2' ){ ?>
					<ul>
						<?php /*?><li><b><?php echo $this->lang->line('mail_delivery_date'); ?> :</b> <?php echo date('d/m/Y',strtotime($order_data['delivery_date'].' 00:00:00')); ?></li><?php */?>
						<li><strong><?php echo $this->lang->line('mail_delivery_day'); ?> :</strong> <?php echo $order_data['delivery_day'].' '.date('d/m/Y',strtotime($order_data['delivery_date'].' 00:00:00')).' '.( ($order_data['delivery_hour'] != '' && $order_data['delivery_minute'] != '')?$this->lang->line('mail_day_on').' '.$order_data['delivery_hour'].':'.$order_data['delivery_minute'].$this->lang->line('mail_time_hour'):'' ); ?></li>
						<li><strong><?php echo $this->lang->line('mail_delivery_note'); ?> :</strong> <?php echo stripslashes($order_data['delivery_remarks']); ?></li>
						<li><strong><?php echo $this->lang->line('mail_shop'); ?> :</strong> <?php echo $company_name; ?></li>
					</ul>
					<?php } ?>
      			</td>
  			</tr>
  			<tr>
    			<td>
    				<table border="0" cellpadding="2" cellspacing="2" width="100%">
        				<tr>
        					<td width="10%" align="center" bgcolor="#EAEAEA"><strong><?php echo $this->lang->line('mail_quantity');?></strong></td>
        				
          					<td width="30%" bgcolor="#EAEAEA"><strong><?php echo $this->lang->line('mail_product');?></strong></td>
          					
          					<?php //if($products_pirceshow_status == '0'){ ?>
							<td width="15%" align="left" bgcolor="#EAEAEA"><strong><?php echo $this->lang->line('mail_rate');?></strong></td>
							<?php //} ?>
							
		  					<td width="35%" align="left" bgcolor="#EAEAEA"><strong><?php echo $this->lang->line('mail_extra');?></strong></td>
		  					
							<?php //if($products_pirceshow_status == '0'){ ?>
							<td width="8%" align="left" bgcolor="#EAEAEA"><strong><?php echo $this->lang->line('mail_total'); ?></strong></td>
							<?php //}?>
        				</tr>
       					<?php if( !empty($order_details_data) ){ ?>
							<?php foreach( $order_details_data as $item ){ ?>
						<tr>
							<td style="border-bottom:1px solid #ccc; padding:5px 0;"  valign="top" width="10%" align="center">
								<?php //echo ltrim($item['quantity'],"0").' '.(($item['content_type']==1)?'gr.':''); ?>
								<?php $qty = ltrim($item['quantity'],"0"); 
								if($item['content_type']== "1"){
									if(intval($qty) >= 1000){
										$qty = floatval($qty)/1000;
										$qty = defined_money_format($qty).' kg.';
									}
									else{
										$qty = $qty.' gr.';
									}
								}
								echo $qty;?>
							</td>
							<td style="border-bottom:1px solid #ccc; padding:5px 0;" valign="top" width="30%">
								<?php echo stripslashes($item['proname']); ?>
							</td>
							<?php if($products_pirceshow_status == '0' ){?>
							<?php 
								$unit = '';
								$o_price = defined_money_format($item['price_per_unit']);
								if($item['content_type'] == 0){
									$unit = ' &euro;'; 
								}else if( $item['content_type'] == 2 ){
									$o_price = defined_money_format($item['price_per_person']);
									$unit = ' &euro;/Per p.';
								}else{
									$unit = ' &euro;/kg';
									$o_price = defined_money_format( ( $item['price_weight'] * 1000 ));
								}	
							?>						
							<td style="border-bottom:1px solid #ccc; padding:5px 0;"  valign="top" width="15%" align="center">
								<?php echo $o_price.'&nbsp;'.$unit; ?>
							</td>
							<?php }else{ ?>
							<td style="border-bottom:1px solid #ccc; padding:5px 0;" valign="top" width="15%" align="center">nvt.</td>
							<?php } ?>
							<td style="border-bottom:1px solid #ccc; padding:5px 0;" valign="top" width="35%" align="left">
						
								<?php
								$rsExtracosts = explode("#",$item['add_costs']);
								for($j = 0; $j < count($rsExtracosts); $j++){
									$hold_arr = explode("_",$rsExtracosts[$j]);
									if( !empty( $hold_arr ) )
									{
										if( isset($hold_arr[0]) && isset($hold_arr[1]) && isset($hold_arr[2]) )
										{
											echo '<span style="color:red;">'.$hold_arr[0].': </span> '.$hold_arr[1].(($products_pirceshow_status == '0' )?' = '.$hold_arr[2]:'').'<br/>';
										}
									}
								}
								
								if($products_pirceshow_status == '0' ){
									if( $item['discount'] != '' && $item['discount'] != 0 )
									{
										echo '<b>'.$this->lang->line('mail_extra_discount').': </b>&nbsp;'.defined_money_format($item['discount']).'&euro;';
									}
								}
	
								if($item['pro_remark'])
									echo '<b>'.$this->lang->line('mail_remark').': </b> '.stripslashes($item['pro_remark']);
								else
									echo ' -- ';
								
								if($item['extra_field'] != '' && $item['extra_name'] != ''){
									echo '<br/><span style="color:red;">'.stripslashes($item['extra_field']).': </span> '.stripslashes($item['extra_name']);
								}
								?>
							</td>
							<?php if($products_pirceshow_status == '0' ){ ?>
							<td width="8%" valign="top" style="border-bottom:1px solid #ccc; padding:5px 0;" align="right">
								<?php echo defined_money_format($item['total']); ?>&euro;
							</td>
							<?php }else{ ?>
							<td width="8%" valign="top" style="border-bottom:1px solid #ccc; padding:5px 0;"align="right">nvt.</td>
							<?php }?>
						</tr >
							<?php } ?>
						<?php } ?>
						
						<?php $disc_per_client_amount = 0; $disc_per_other_code = 0; $disc_per_amount = 0; ?>
						
						<?php if($products_pirceshow_status == '0' ){ ?>
	        				<?php if( $order_data['option'] == '1' ){ ?>
								<?php $total_cost = $order_data['order_total']; ?>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<?php if($products_pirceshow_status == '0' ){ ?>
										<td>&nbsp;</td>
										<td align="right"><?php echo $this->lang->line('mail_order_total'); ?></td><td align="right"><?php echo defined_money_format($order_data['order_total']); ?>&euro;</td>
										<?php } ?>	
									</tr>
								
								<?php if($order_data['disc_client_amount'] > 0 && $products_pirceshow_status == '0' ){ ?>
									<?php $disc_per_client_amount = $order_data['disc_client_amount'];?>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td align="right"><?php echo $this->lang->line('loyalty_discount').'('.$order_data['disc_client'].'%)'; ?></td>
										<td align="right">-<?php echo defined_money_format($order_data['disc_client_amount']); ?>&euro;</td>
									</tr>
								<?php } ?>
								
								<?php if($order_data['other_code_type'] != '' && $order_data['disc_other_code'] != '0' ){ ?>
									<?php $disc_per_other_code = $order_data['disc_other_code'];?>
									<?php if($order_data['other_code_type'] == 'introcode'){?>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td align="right"><?php echo $this->lang->line('introcode_discount'); ?></td>
										<td align="right">-<?php echo defined_money_format($order_data['disc_other_code']); ?>&euro;</td>
									</tr>
									<?php }?>
									<?php if($order_data['other_code_type'] == 'promocode'){?>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td align="right"><?php echo $this->lang->line('promocode_discount'); ?></td>
										<td align="right">-<?php echo defined_money_format($order_data['disc_other_code']); ?>&euro;</td>
									</tr>
									<?php }?>
								<?php } ?>
		
								<?php if($company_gs->disc_per_amount == 1 && $company_gs->disc_after_amount > 0  && $order_data['disc_amount'] > 0 && ($order_data['disc_percent'] > 0 || $order_data['disc_price'] > 0)){ ?>
									<?php $disc_per_amount = $order_data['disc_amount'];?>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<?php if($order_data['disc_percent'] > 0){ ?>
										<td align="right"><?php echo $this->lang->line('discount_txt').'('.$order_data['disc_percent'].'%)'; ?></td>
										<td align="right">-<?php echo defined_money_format($disc_per_amount); ?>&euro;</td>
										<?php }else{ ?>
										<td align="right"><?php echo $this->lang->line('discount_txt').'('.$order_data['disc_price'].'&euro;)'; ?></td>
										<td align="right">-<?php echo defined_money_format($disc_per_amount); ?>&euro;</td>
										<?php } ?>
									</tr>
								<?php } ?>
		
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<?php if($products_pirceshow_status == '0' ){ ?>
										<td>&nbsp;</td>		
										<td align="right"><b><?php echo $this->lang->line('mail_grand_total'); ?></b></td>
										<td align="right"><?php echo defined_money_format((round($total_cost,2)- (round($disc_per_amount,2)+round($disc_per_other_code,2)+round($disc_per_client_amount,2)) )); ?>&euro;</td>
									<?php } ?>
								</tr>
								<?php if($company_gs->activate_discount_card != 0){ ?>
								<tr>
									<td colspan=4 align="left"><b><?php echo $this->lang->line('mail_discount_card_number'); ?>:</b>&nbsp;<?php echo $c_discount_number; ?></td>
								</tr>
								<?php } ?>		
							<?php }elseif( $order_data['option'] == '2' ){  ?>
								<?php $total_cost = (float)$order_data['delivery_cost']+(float)$order_data['order_total']; ?>
		
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<?php if($products_pirceshow_status == '0' ){ ?>
									<td>&nbsp;</td>
									<td align="right"><?php echo $this->lang->line('mail_order_total'); ?></td><td align="right"><?php echo defined_money_format($order_data['order_total']); ?>&euro;</td>
									<?php } ?>
								</tr>
								
								<?php if($order_data['disc_client_amount'] > 0 && $products_pirceshow_status == '0' ){ ?>
									<?php $disc_per_client_amount = $order_data['disc_client_amount'];?>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td align="right"><?php echo $this->lang->line('loyalty_discount').'('.$order_data['disc_client'].'%)'; ?></td>
										<td align="right">-<?php echo defined_money_format($order_data['disc_client_amount']); ?>&euro;</td>
									</tr>
								<?php } ?>
								
								<?php if($order_data['other_code_type'] != '' && $order_data['disc_other_code'] != '0' ){ ?>
									<?php $disc_per_other_code = $order_data['disc_other_code'];?>
									<?php if($order_data['other_code_type'] == 'introcode'){?>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td align="right"><?php echo $this->lang->line('introcode_discount'); ?></td>
										<td align="right">-<?php echo defined_money_format($order_data['disc_other_code']); ?>&euro;</td>
									</tr>
									<?php }?>
									<?php if($order_data['other_code_type'] == 'promocode'){?>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td align="right"><?php echo $this->lang->line('promocode_discount'); ?></td>
										<td align="right">-<?php echo defined_money_format($order_data['disc_other_code']); ?>&euro;</td>
									</tr>
									<?php }?>
								<?php } ?>
		
								<?php if($company_gs->disc_per_amount == 1 && $company_gs->disc_after_amount > 0  && $order_data['disc_amount'] > 0 && ($order_data['disc_percent'] > 0 || $order_data['disc_price'] > 0)){ ?>
									<?php $disc_per_amount = $order_data['disc_amount'];?>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<?php if($order_data['disc_percent'] > 0){ ?>
										<td align="right"><?php echo $this->lang->line('discount_txt').'('.$order_data['disc_percent'].'%)'; ?></td>
										<td align="right">-<?php echo defined_money_format($disc_per_amount); ?>&euro;</td>
										<?php }else{ ?>
										<td align="right"><?php echo $this->lang->line('discount_txt').'('.$order_data['disc_price'].'&euro;)'; ?></td>
										<td align="right">-<?php echo defined_money_format($disc_per_amount); ?>&euro;</td>
										<?php } ?>
									</tr>
								<?php } ?>
		
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<?php if($products_pirceshow_status == '0' ){ ?>
									<td>&nbsp;</td>
									<td align="right"><?php echo $this->lang->line('mail_delivery_cost'); ?></td>
									<td align="right"><?php echo defined_money_format($order_data['delivery_cost']); ?>&euro;</td>
									<?php } ?>
								</tr>
		
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<?php if($products_pirceshow_status == '0' ){ ?>
									<td>&nbsp;</td>
									<td align="right"><b><?php echo $this->lang->line('mail_grand_total'); ?></b></td>
									<td align="right"><?php echo defined_money_format((round($total_cost,2) - (round($disc_per_amount,2)+round($disc_per_other_code,2)+round($disc_per_client_amount,2)))); ?>&euro;</td>
									<?php }?>
								</tr>
								<?php if($is_set_discount_card != 0){ ?>
								<tr>
									<td colspan=4 align="left"><b><?php echo $this->lang->line('mail_discount_card_number'); ?>:</b>&nbsp;<?php echo $c_discount_number ; ?></td>
								</tr>
								<?php } ?>
							<?php } ?>
						<?php } ?>
    				</table>
    			</td>
  			</tr>
  			<tr>
    			<td>
    				<table width="100%" border="0" cellspacing="0" cellpadding="2">
	    				<tr>	
	    					<td align="left"><br /><br /></td>
	    				</tr>
        				<tr>
          					<td align="left">
          						<br />
             					<?php echo $this->lang->line('mail_regards');?>,<br /><br />
            					<?php echo $company_name; ?><br /><br />
		  					</td>
        				</tr>
		 				<tr>
          					<td align="left"><?php echo $Options4;?></td>
        				</tr>
      				</table>
      			</td>
  			</tr>	
		</table>
	</body>
</html>
