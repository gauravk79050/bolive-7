  		<table cellspacing="2" cellpadding="2" border="0" id="single-order" align="center" width="100%">
  			<tr>
    			<td>
    				<table width="100%" border="0" cellspacing="0" cellpadding="2">
						<tr>
          					<td align="left">
          						<strong><?php echo _("Order No.");?></strong> : <?php echo $order_id;?>
          					</td>
          					<td style="text-align: right;" >
          						<span style="font-weight:bold; font-size:15px;"><?php //color:#FF0004;?> 
          						<?php 
                					if($order_data['payment_via_paypal'] == '2' && isset($order_data['billing_option'])){

										if(isset($order_data['payment_status']) && $order_data['payment_status'] == 1){
											//if($order_data['payment_status'] == 1)
												echo '<span style="color:#6F9300; font-weight:bold; font-size:20px;">'._("PAID").'</span><br/>';
											//else
												//echo '<span style="color:#AA0000; font-weight:bold; font-size:20px;">'._("UNPAID").'</span><br/>';
										}else{
											echo '<span style="color:#AA0000; font-weight:bold; font-size:20px;">'._("UNPAID").'</span><br/>';
										}
										// echo $this->lang->line('via_cardgate_pending');
										echo _('via').' '.$order_data['billing_option'];
									}elseif($order_data['payment_via_paypal'] == '1'){
										echo _('via').' '._('Paypal');
										//echo $this->lang->line('via_paypal_pending');
									}
								?>
								</span>
							</td>
        				</tr>
        				<tr>
          					<td align="left">
          						<strong><?php echo _("Order Date");?></strong> : <?php echo date('d-m-Y',strtotime($order_data['created_date']));?>
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
                						<td bgcolor="#F0F0F0"><b><?php echo _("Customer Data");?>:</b></td>
                						<?php if( $order_data['option'] == '2' ){?><?php //if( $is_international ){ ?><td bgcolor="#F0F0F0"><b><?php echo _("Delivery Address");?></b></td><?php //} ?><?php }?>           						
                						<?php /* ?>
                						<td bgcolor="#F0F0F0" style="text-align: right;" >
                							<span style="color:#FF0004; font-weight:bold; font-size:15px;">
                							<?php 
                								if($order_data['payment_via_paypal'] == '2' && isset($order_data['billing_option'])){
													echo _('paid').':- '._('via').' '.$order_data['billing_option'].( ($order_data['payment_status'] != '1')?' ('._('Pending').') ':'' );
												}else if($order_data['payment_via_paypal'] == '1'){
													echo _('via Paypal : Pending');
												}
											?>
											</span>
										</td>
										<?php */ ?>
              						</tr>
              						<tr>
                						<td>
                							<strong>
					  							<?php if( $is_international ){
					  								echo '';
					  							}elseif($client->company_c != '0'){
													echo $client->company_c;
												}else{
													echo '';
												}?><br />
												<?php echo stripslashes($client->firstname_c);?>&nbsp;<?php echo stripslashes($client->lastname_c);?><br />
								                <?php echo stripslashes($client->address_c);?>&nbsp;<?php echo stripslashes($client->housenumber_c);?><br />
								                <?php echo $client->postcode_c;?> - <?php echo stripslashes($client->city_c);?><br/>
								                <?php echo $client->country_c;?><br/>										
											</strong>
			    						</td>
			    						<?php //if( $is_international ){ ?>
			    						<?php if( $order_data['option'] == '2' ){?>
			    						<td>
                							<strong>
                								<br/>
												<?php echo stripslashes($order_data['name']);?>&nbsp;<br />
								                <?php echo stripslashes($order_data['delivery_streer_address']);?>&nbsp;<?php echo $order_data['delivery_busnummer'];?><br />
								                <?php echo $order_data['delivery_zip']; if($order_data['delivery_city'] != '0'){?> - <?php echo stripslashes($order_data['delivery_city_name']);}?><br/>
								                <?php
												if(isset($order_data['delivery_country_name'])){
													echo $order_data['delivery_country_name'];
												}	
								                ?><br/>
								                <?php if( $is_international ){ ?>
								                <?php echo _("Mobile")?> :&nbsp;<?php echo $order_data['phone_reciever'];?><br />
								                <?php } ?>
								            </strong>
			    						</td>
			    						<?php }?>
			    						<?php //} ?>
              						</tr>
              						<?php if( $is_international ){ ?>
              						<tr>
              							<td colspan="2">
              								<?php if(isset($order_data['delivery_country']) && $order_data['delivery_country'] == 21 && isset($order_data['delivery_date']) && $order_data['delivery_date'] != '' && $order_data['delivery_date'] != '0000-00-00'){ ?>
              									<br/><strong><?php echo _('Delivery Day'); ?> :</strong> <?php echo date('d/m/Y',strtotime($order_data['delivery_date'].' 00:00:00')); ?>
              								<?php } ?>
              							 	<?php if(isset($order_data['delivery_remarks']) && trim($order_data['delivery_remarks']) != ''){ ?>
								            	<br/><strong><?php echo _('Delivery Note'); ?> :</strong> <?php echo stripslashes($order_data['delivery_remarks']); ?><br/> 
								            <?php } ?>
              							</td>
              						</tr>
              						<?php } ?>
              						<tr>
	              						<td>
	              							<strong>
	              								<?php
	              									if( !$is_international ){ //IS PICKUP OR NATIONAL DELIVERY
										                if($order_data['get_invoice']){
										                	echo "<br/>"._("I want an invoice")." : "._("Yes")."<br/>";
										                	echo  _("VAT")." : ".$client->vat_c."<br/>";
										                } 
										                if( $order_data['option'] == '1' && $order_data['name'] != '' ){ 
										                	echo '<br />'.stripslashes($order_data['name']).'<br />';
										                } ?>
												    <?php } ?>
												    <br />
									                <?php echo _("Mobile")?> :&nbsp;<?php echo $client->phone_c;?><br />
													<?php /*echo _("Mobile")?> :&nbsp;<?php echo $client->mobile_c;?><br />
													<?php echo _("Phone")?> :&nbsp;<?php echo $client->phone_c;?><br /><?php */ ?>
													<?php echo _("Email")?> :&nbsp;<?php echo $client->email_c;?><br />
	              							</strong>
	              						</td>
              						</tr>
								</table>
							</td>	
        				</tr>
      				</table>
	      			<?php if( !$is_international ) { //IS PICKUP OR NATIONAL DELIVERY ?>	
	      				<?php if( $order_data['option'] == '1' ){ //IS PICKUP?>
						<ul>
							<li><strong><?php echo _('Pickup Date'); ?> :</strong> <?php echo $order_data['order_pickupday'].' '.date('d/m/Y',strtotime($order_data['order_pickupdate'].' 00:00:00')).' '._('on').' '.$order_data['order_pickuptime'].' '._('hr'); ?></li>
							<li><strong><?php echo _('Pickup Note'); ?> :</strong> <?php echo stripslashes($order_data['order_remarks']); ?></li>
							<li><strong><?php echo _('Shop'); ?> :</strong> <?php echo $company_name; ?></li>
						</ul>
						<?php }elseif( $order_data['option'] == '2' ){ //IS NATIONAL DELIVERY ?>
						<ul>
							<?php /*?><li><strong><?php echo _('Delivery Date'); ?> :</strong> <?php echo date('d/m/Y',strtotime($order_data['delivery_date'].' 00:00:00')); ?></li><?php */?>
							<li><strong><?php echo _('Delivery Day'); ?> :</strong> <?php echo $order_data['delivery_day'].' '.date('d/m/Y',strtotime($order_data['delivery_date'].' 00:00:00')).' '.( ($order_data['delivery_hour'] != '' && $order_data['delivery_minute'] != '')?_('on').' '.$order_data['delivery_hour'].':'.$order_data['delivery_minute']._('hr'):'' ); ?></li>
							<li><strong><?php echo _('Delivery Note'); ?> :</strong> <?php echo stripslashes($order_data['delivery_remarks']); ?></li>
							<li><strong><?php echo _('Shop'); ?> :</strong> <?php echo $company_name; ?></li>
						</ul>
						<?php } ?>
					<?php } ?>
      			</td>
  			</tr>
  			<tr>
    			<td>
    				<table border="0" cellpadding="2" cellspacing="2" width="100%">
        				<tr>
        					<td width="10%" align="center" bgcolor="#EAEAEA"><strong><?php echo _("Quantity");?></strong></td>
        				
          					<td width="30%" bgcolor="#EAEAEA"><strong><?php echo _("Product");?></strong></td>
          					
          					<?php //if($products_pirceshow_status == '0'){ ?>
							<td width="15%" align="center" bgcolor="#EAEAEA"><strong>Tarief</strong></td>
							<?php //} ?>
							
		  					<td width="35%" align="left" bgcolor="#EAEAEA"><strong><?php echo _("Extra");?></strong></td>
		  					
							<?php //if($products_pirceshow_status == '0'){ ?>
							<td width="8%" align="right" bgcolor="#EAEAEA"><strong>Totaal</strong></td>
							<?php //}?>
        				</tr>
       					<?php if( !empty($order_details_data) ){ ?>
							<?php foreach( $order_details_data as $item ){ ?>
						<tr>
							<td style="border-bottom:1px solid #ccc; padding:5px 0;"  valign="top" width="10%" align="center">
								<?php //echo ltrim($item['quantity'],"0").' '.(($item['content_type']== "1")?'gr.':''); ?>
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
									$o_price = defined_money_format( ($item['price_weight'] * 1000 ) );
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
										echo '<b>'._('Extra Discount').': </b>&nbsp;'.defined_money_format($item['discount']).'&euro;';
									}
								}
								
								if($item['pro_remark'])
									echo '<b>'._('Remark').': </b> '.stripslashes($item['pro_remark']);
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
										<td align="right"><?php echo _('Order Total'); ?></td><td align="right"><?php echo defined_money_format($order_data['order_total']); ?>&euro;</td>
										<?php } ?>	
									</tr>
								
								<?php if($order_data['disc_client_amount'] > 0 && $products_pirceshow_status == '0' ){ ?>
									<?php $disc_per_client_amount = $order_data['disc_client_amount'];?>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td align="right"><?php echo _('Loyalty discount').'('.$order_data['disc_client'].'%)'; ?></td>
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
										<td align="right"><?php echo _('Introcode discount'); ?></td>
										<td align="right">-<?php echo defined_money_format($order_data['disc_other_code']); ?>&euro;</td>
									</tr>
									<?php }?>
									<?php if($order_data['other_code_type'] == 'promocode'){?>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td align="right"><?php echo _('Promocode discount'); ?></td>
										<td align="right">-<?php echo defined_money_format($order_data['disc_other_code']); ?>&euro;</td>
									</tr>
									<?php }?>
								<?php } ?>
		
								<?php $disc_per_amount = 0;?>
								<?php if($company_gs->disc_per_amount == 1 && $company_gs->disc_after_amount > 0  && $order_data['disc_amount'] > 0 && ($order_data['disc_percent'] > 0 || $order_data['disc_price'] > 0)){ ?>
									<?php $disc_per_amount = $order_data['disc_amount'];?>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<?php if($order_data['disc_percent'] > 0){ ?>
										<td align="right"><?php echo _('discount').'('.$order_data['disc_percent'].'%)'; ?></td>
										<td align="right">-<?php echo defined_money_format($disc_per_amount); ?>&euro;</td>
										<?php }else{ ?>
										<td align="right"><?php echo _('discount').'('.$order_data['disc_price'].'&euro;)'; ?></td>
										<td align="right">-<?php echo defined_money_format($disc_per_amount); ?>&euro;</td>
										<?php } ?>
									</tr>
								<?php } ?>
		
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<?php if($products_pirceshow_status == '0' ){ ?>
										<td>&nbsp;</td>		
										<td align="right"><b><?php echo _('Grand Total'); ?></b></td>
										<td align="right"><?php echo defined_money_format((round($total_cost,2)- (round($disc_per_amount,2)+round($disc_per_other_code,2)+round($disc_per_client_amount,2)))); ?>&euro;</td>
									<?php } ?>
								</tr>
								<?php if($company_gs->activate_discount_card != 0){ ?>
								<tr>
									<td colspan=4 align="left"><b><?php echo _('Discount Card Number'); ?>:</b>&nbsp;<?php echo $c_discount_number; ?></td>
								</tr>
								<?php } ?>		
							<?php }elseif( $order_data['option'] == '2' ){  ?>
								<?php $total_cost = (float)$order_data['delivery_cost']+(float)$order_data['order_total']; ?>
		
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<?php if($products_pirceshow_status == '0' ){ ?>
									<td>&nbsp;</td>
									<td align="right"><?php echo _('Order Total'); ?></td><td align="right"><?php echo defined_money_format($order_data['order_total']); ?>&euro;</td>
									<?php } ?>
								</tr>
								
								<?php if($order_data['disc_client_amount'] > 0 && $products_pirceshow_status == '0' ){ ?>
									<?php $disc_per_client_amount = $order_data['disc_client_amount'];?>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td align="right"><?php echo _('Loyalty discount').'('.$order_data['disc_client'].'%)'; ?></td>
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
										<td align="right"><?php echo _('Introcode discount'); ?></td>
										<td align="right">-<?php echo defined_money_format($order_data['disc_other_code']); ?>&euro;</td>
									</tr>
									<?php }?>
									<?php if($order_data['other_code_type'] == 'promocode'){?>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td align="right"><?php echo _('Promocode discount'); ?></td>
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
										<td align="right"><?php echo _('Discount').'('.$order_data['disc_percent'].'%)'; ?></td>
										<td align="right">-<?php echo defined_money_format($disc_per_amount); ?>&euro;</td>
										<?php }else{ ?>
										<td align="right"><?php echo _('Discount').'('.$order_data['disc_price'].'&euro;)'; ?></td>
										<td align="right">-<?php echo defined_money_format($disc_per_amount); ?>&euro;</td>
										<?php } ?>
									</tr>
								<?php } ?>
		
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<?php if($products_pirceshow_status == '0' ){ ?>
									<td>&nbsp;</td>
									<td align="right"><?php echo _('Delivery Cost'); ?></td>
									<td align="right"><?php echo defined_money_format($order_data['delivery_cost']); ?>&euro;</td>
									<?php } ?>
								</tr>
		
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<?php if($products_pirceshow_status == '0' ){ ?>
									<td>&nbsp;</td>
									<td align="right"><b><?php echo _('Grand Total'); ?></b></td>
									<td align="right"><?php echo defined_money_format((round($total_cost,2) - (round($disc_per_amount,2)+round($disc_per_other_code,2)+round($disc_per_client_amount,2) ))); ?>&euro;</td>
									<?php }?>
								</tr>
								<?php if($is_set_discount_card != 0){ ?>
								<tr>
									<td colspan=4 align="left"><b><?php echo _('Discount Card Number'); ?>:</b>&nbsp;<?php echo $c_discount_number ; ?></td>
								</tr>
								<?php } ?>
							<?php } ?>
						<?php } ?>
    				</table>
    			</td>
  			</tr>
  			<?php if( isset($is_send_order_mail) ){ //FOR company_api_new only ?>
  			<tr>
    			<td>
    				<table width="100%" border="0" cellspacing="0" cellpadding="2">
	    				<tr>	
	    					<td align="left"><br /><br /></td>
	    				</tr>
        				<tr>
          					<td align="left">
          						<br />
             					<?php echo _("Regards");?>,<br /><br />
            					<?php echo $company_name; ?><br /><br />
		  					</td>
        				</tr>
		 				<tr>
          					<td align="left"><?php echo $Options4;?></td>
        				</tr>
      				</table>
      			</td>
  			</tr>
  			<?php } ?>
  		</table>
  		<p></p>
		<hr/>
		<p></p>