<div style="padding: 0px; display:block;" class="inside">
        	<div class="table">
<form action="<?php echo base_url()?>cp/settings/paymentsettings" enctype="multipart/form-data" method="post" id="frm_payment_settings" name="frm_payment_settings" onsubmit="return validatepayment()">
					<table cellspacing="0" border="0" id="tnc_txt_tbl" class="mceLayout">
                		<tbody  class="mceContentBody ">
                			<!-- GENERAL SETTINGS FOR ONLINE PAYMENT -->
                			<tr>
				    			<td valign="top" class="textlabel"><?php echo _('GENERAL'); ?> :</td>
			  					<td>
				  					<p>
				  						<input type="radio" name="adv_payment" value="2" <?php if($order_settings && $order_settings[0]->adv_payment == 2){?>checked="checked"<?php }?> />
				  						<?php echo _('Always pay in advance required');?>
				  					</p>
				  					<p>
				  						<input type="radio" name="adv_payment" value="1" <?php if($order_settings && $order_settings[0]->adv_payment == 1){?>checked="checked"<?php }?> />
				  						<?php echo _('Only pay in advance required when delivery');?>
				  					</p>
							  		<p>
							  			<input type="radio" name="adv_payment" value="0" <?php if($order_settings && $order_settings[0]->adv_payment == 0){?>checked="checked"<?php }?> />
							  			<?php echo _('Client may choose');?>
								  			
								  	</p>
								</td>
				   			</tr>
				   			<tr>
				   				<td colspan="2">&nbsp;</td>
				   			</tr>		
					 
					   <?php /*?>
                   			<tr>
				      			<td valign="top"><?php echo _('For Online Payment'); ?> :</td>
			  					<td>
			  						<p style="float:left;"><?php echo _('Status'); ?>&nbsp;:&nbsp;</p>
				  					<select name="online_payment" id="online_payment" style="float:left;margin-left:5px;padding:2px;width: 100px;" >
										<option value="0" <?php if($general_settings && $general_settings[0]->online_payment==0) {echo 'selected="selected"';} ?>><?php echo _('Inactive'); ?></option>
										<option value="1" <?php if($general_settings && $general_settings[0]->online_payment==1) {echo 'selected="selected"';} ?>><?php echo _('Active'); ?></option>
				  					</select>
				  					<br style="clear:both;" />
				  				
				  					<table id="pay-set-opt">
				     					<tr> 
					    					<td><?php echo _('Paypal Address'); ?></td>
			            					<td><input type="text" class="text medium" name="paypal_address" id="paypal_address" value="<?php if($general_settings && $general_settings[0]->paypal_address) {echo $general_settings[0]->paypal_address;} ?>" /></td>
					 					</tr>
					 					<tr> 
					    					<td><?php echo _('Minimum amount for payment'); ?></td>
			            					<td><input type="text" class="text short" name="minimum_amount_paypal" id="minimum_amount_paypal" value="<?php if($general_settings && $general_settings[0]->minimum_amount_paypal) {echo $general_settings[0]->minimum_amount_paypal;} ?>" />&euro;</td>
					 					</tr>		
					 					<tr>
					    					<td><?php echo _('Allowance'); ?></td>
			            					<td>
						    					<input type="checkbox" name="apply_tax" id="apply_tax" value="1" <?php if($general_settings && $general_settings[0]->apply_tax==1) echo 'checked="checked"'; ?> />
												&nbsp;<?php echo _('Enable Fee'); ?>&nbsp;-&nbsp;
												<input type="text" name="tax_percentage" id="tax_percentage" class="text short" value="<?php if($general_settings && $general_settings[0]->tax_percentage) {echo $general_settings[0]->tax_percentage;} ?>" />
						    					&nbsp;%&nbsp;+&nbsp;
												<input type="text" name="tax_amount" id="tax_amount" class="text short" value="<?php if($general_settings && $general_settings[0]->tax_amount) {echo $general_settings[0]->tax_amount;} ?>" />
												&nbsp;&euro;
											</td>
					 					</tr>
					 					<tr>
					    					<td valign="top"><?php echo _('Payment Instructions'); ?></td>
											<td>
						    					<textarea name="payment_instructions" id="payment_instructions" style="width: 550px; height: 200px;"><?php if($general_settings && $general_settings[0]->paypal_address) {echo $general_settings[0]->payment_instructions;} ?></textarea>
											</td>
					 					</tr>
					 					<tr>
					    					<td valign="top"><?php echo _('Thank You Message'); ?></td>
											<td>
						    					<textarea name="pay_complete_msg" id="pay_complete_msg" style="width: 550px; height: 200px;"><?php if($general_settings && $general_settings[0]->paypal_address) {echo $general_settings[0]->pay_complete_msg;} ?></textarea>		
											</td>
					 					</tr>
					 					<tr>
					    					<td valign="top"><?php echo _('Incomplete Message'); ?></td>
											<td>
						    					<textarea name="pay_incomplete_msg" id="pay_incomplete_msg" style="width: 550px; height: 200px;"><?php if($general_settings && $general_settings[0]->paypal_address) {echo $general_settings[0]->pay_incomplete_msg;} ?></textarea>
											</td>
					 					</tr>
					 
				  					</table>
		  						</td>
	   						</tr> <?php */?>
				   
	   						<!-- CARDGATE SETTINGS -->
	   						<tr>
	      						<td valign="top"><?php echo _('Credit Card'); ?> :</td>
		  						<td>
		  							<p style="float:left;"><?php echo _('Status'); ?>&nbsp;:&nbsp;</p>
			  						<select name="cardgate_payment" id="cardgate_payment" style="float:left;margin-left:5px;padding:2px;width: 100px;" >
										<option value="0" <?php if(!empty($cardgate_setting) && $cardgate_setting->cardgate_payment == 0) {echo 'selected="selected"';} ?>><?php echo _('Inactive'); ?></option>
										<option value="1" <?php if(!empty($cardgate_setting) && $cardgate_setting->cardgate_payment == 1) {echo 'selected="selected"';} ?>><?php echo _('Active'); ?></option>
			  						</select>
			  						<a href="<?php echo base_url(); ?>cp/payment/payment_method" style="margin-left: 20px;"><?php echo _("Overview");?></a>			  
			  						<br style="clear:both;" />
			  						
			  						<table id="pay-set-opt">
				 						<tr> 
				    						<td><?php echo _('Minimum amount for payment'); ?></td>
			            					<td><input type="text" class="text short" name="minimum_amount_cardgate" id="minimum_amount_cardgate" value="<?php if(!empty($cardgate_setting) && $cardgate_setting->minimum_amount_cardgate) {echo $cardgate_setting->minimum_amount_cardgate;} ?>" />&euro;</td>
					 					</tr>	
					 					<tr>
					    					<td><?php echo _('Allowance'); ?></td>
			            					<td>
											    <input type="checkbox" name="c_apply_tax" id="c_apply_tax" value="1" <?php if(!empty($cardgate_setting) && $cardgate_setting->c_apply_tax == 1) echo 'checked="checked"'; ?> />
												&nbsp;<?php echo _('Enable Fee'); ?>&nbsp;-&nbsp;
												<input type="text" name="c_tax_percentage" id="c_tax_percentage" class="text short" value="<?php if(!empty($cardgate_setting) && $cardgate_setting->c_tax_percentage) {echo $cardgate_setting->c_tax_percentage;} ?>" />
											    &nbsp;%&nbsp;+&nbsp;
												<input type="text" name="c_tax_amount" id="c_tax_amount" class="text short" value="<?php if(!empty($cardgate_setting) && $cardgate_setting->c_tax_amount) {echo $cardgate_setting->c_tax_amount;} ?>" />
												&nbsp;&euro;
											</td>
					 					</tr>
				  					</table>
			  					</td>
		   					</tr>
		   					<tr>
					   			<td width="22%"  valign="top" class="textlabel"><?php echo _('Show revocation')?>
					   			</td>
					   			<td style="vertical-align:top">
									<div class="other_setting_div">
										<p>
											<span class="left">
												<input type="checkbox" value="1" class="checkbox" id="show_revocation" name="show_revocation" <?php if($general_settings && $general_settings[0]->show_revocation==1):?>checked="checked"<?php endif;?>>
											</span>
										</p>
								</td>
					   		</tr>
					   		<!-- start-->
					   		<tr>
					   			<td width="22%"  valign="top" class="textlabel"><?php echo _('Switch to sandbox mode')?>
					   			</td>
					   			<td style="vertical-align:top">
									<div class="other_setting_div">
										<p>
											<span class="left">
												<input type="checkbox" value="1" class="checkbox" id="sandbox_active" name="sandbox_active" <?php if(!empty($cardgate_setting) && $cardgate_setting->sandbox_active== 1) {?> checked="checked" <?php } ?> >
									
											</span>
										</p>
								</td>
					   		</tr>
                			<!-- END -->   		
			                			                
                	
		   					<tr>
							  	<td class="save_b" colspan="2">
							  		<input type="hidden" name="act" id="act" value="update_payment_settings" />
		 					  		<input type="submit" name="btn_update" id="btn_update" value="<?php echo _('UPDATE'); ?>" />
		 					  	</td>
						   	</tr>
						</tbody>
	  				</table>
			  	</form>
			  	<script type="text/javascript" language="javascript">
					      function validatepayment(){
					          var ok_msg = tinyMCE.get('tnc_txt').getContent();
					          if(ok_msg == ""){
					            alert("<?php echo _('please Enter Terms and Conditions')?>");
					            return false;
					          }
					          return true;
					        }
    		</script>
		</div>
	</div>
  	<script type="text/javascript" language="javascript">
	  var frmValidator = new Validator("frm_payment_settings");
	  /*frmValidator.EnableMsgsTogether();
	  frmValidator.addValidation("paypal_address","req"," <?php echo _('Please give paypal address.')?>");	
	  frmValidator.addValidation("paypal_address","email"," <?php echo _('Please give a valid paypal address.')?>");	
	  frmValidator.addValidation("tax_percentage","float"," <?php echo _('Please give valid tax percentage.')?>");	
	  frmValidator.addValidation("tax_amount","float"," <?php echo _('Please give valid tax amount.')?>");	*/
    </script>