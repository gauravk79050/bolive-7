 <style>
 	.form_error{
 		border:#c00 1px solid !important;
 	}
 </style>
 
 <!-- MAIN -->
<div id="main">
	<div id="main-header">
    	<h2><?php echo _('Customer Details')?></h2>
      	<span class="breadcrumb"><a href="<?php echo base_url()?>cp/cdashboard"><?php echo _('Home')?></a> &raquo; <a href="<?php echo base_url()?>cp/cdashboard/clients/"><?php echo _('customers')?></a> &raquo; <?php echo _('client add')?></span>
	</div>
	<?php if($this->session->flashdata('success')){?>
	<div class="success"><strong><?php echo _('Success')?> : </strong><?php echo $this->session->flashdata('success');?></div>
	<?php }?>
	
    <div id="content">
    	<div id="content-container">
			<div class="box">
			
          		<h3><?php echo _('Add Infos')?></h3>
          		<div class="table">
          			<form method="post" action="">
	            		<table border="0">
	              			<tbody>
	                			<tr>
	                  				<td class="textlabel" width="40%">
	                  					<span style="padding-left:20px"><?php echo _('First Name')?></span>
	                  				</td>
	                 				<td>
	                 					<input type="text" name="firstname_c" id="firstname_c" size="30" value="<?php echo set_value('firstname_c');?>" class="text medium <?php if(form_error('firstname_c')){echo 'form_error';}?>">
	                 				</td>
	                			</tr>
	                			<tr>
	                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('Last Name')?></span></td>
	                  				<td>
	                 					<input type="text"   name="lastname_c" id="lastname_c" size="30" value="<?php echo set_value('lastname_c');?>" class="text medium <?php if(form_error('lastname_c')){echo 'form_error';}?>">
	                 				</td>
	                			</tr>
	                			<tr>
					            	<td class="textlabel"><span style="padding-left:20px"><?php echo _('Company')?></span></td>
		            			    <td>
	                  					<input type="text"  name="company_c" id="company_c" size="30" value="<?php echo set_value('company_c');?>" class="text medium">
	                  				</td>
		                		</tr>
	                            <tr>
	            			    	<td class="textlabel"><span style="padding-left:20px"><?php echo _('Address')?></span></td>
	                  				<td>
	                 					<input type="text"   name="address_c" id="address_c" size="30" value="<?php echo set_value('address_c');?>" class="text medium <?php if(form_error('address_c')){echo 'form_error';}?>">
	                 				</td>
	                			</tr>
	
	                			<tr>
	                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('House number')?></span></td>
	                  				<td>
	                 					<input type="text"   name="housenumber_c" id="housenumber_c" size="30" value="<?php echo set_value('housenumber_c');?>" class="text medium <?php if(form_error('housenumber_c')){echo 'form_error';}?>">
	                 				</td>
	                			</tr>
	                             <tr>
	                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('Postal Code')?></span></td>
					                <td>
	                 					<input type="text"   name="postcode_c" id="postcode_c" size="30" value="<?php echo set_value('postcode_c');?>" class="text medium <?php if(form_error('postcode_c')){echo 'form_error';}?>">
	                 				</td>
	                			</tr>
	
	                			<tr>
	                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('City')?></span></td>
	                  				<td>
	                 					<input type="text"   name="city_c" id="city_c" size="30" value="<?php echo set_value('city_c');?>" class="text medium <?php if(form_error('city_c')){echo 'form_error';}?>">
	                 				</td>
	                			</tr>
	                			<tr>
	                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('Country')?></span></td>
	                  				<td>
	                 					<select name="country_id" id="country_id" style="margin: 0px; padding: 2px;">
	                 						<option value="0">-- <?php echo _('Select');?> --</option>
	                 						<?php if(!empty($countries)){?>
	                 							<?php foreach($countries as $country){?>
	                 						<option value="<?php echo $country->id;?>" <?php echo set_select('country_id', $country->id);?>><?php echo $country->country_name;?></option>
	                 							<?php }?>
	                 						<?php }?>
	                 					</select>
	                 				</td>
	                			</tr>
	                			<tr>
	                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('Telephone')?></span></td>
	                  				<td>
	                 					<input type="text" value="<?php echo set_value('phone_c');?>" name="phone_c" id="phone_c" size="30" class="text medium <?php if(form_error('phone_c')){echo 'form_error';}?>">
	                 				</td>
	                			</tr>
	                			<tr>
					                <td class="textlabel"><span style="padding-left:20px"><?php echo _('GSM')?></span></td>
	                				<td>
	                 					<input type="text" value="<?php echo set_value('mobile_c'); ?>" name="mobile_c" id="mobile_c" size="30" class="text medium">
	                 				</td>
	                			</tr>
	                			<tr>
	                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('FAX')?></span></td>
	                  				<td>
	                 					<input type="text" value="<?php echo set_value('fax_c'); ?>" name="fax_c" id="fax_c" size="30" class="text medium">
	                 				</td>
	                			</tr>
	                			<tr>
	                  				<td class="textlabel" colspan="2">&nbsp;</td>
	                			</tr>
	                			<tr>
	                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('Email')?></span></td>
	                  				<td>
	                 					<input type="email" value="<?php echo set_value('email_c'); ?>" name="email_c" id="email_c" size="30" class="text medium <?php if(form_error('email_c')){echo 'form_error';}?>">
	                 				</td>
	                			</tr>
	                			<tr>
	                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('Password')?></span></td>
	                  				<td>
	                 					<input type="password"   name="password_c" id="password_c" size="30" class="text medium <?php if(form_error('password_c')){echo 'form_error';}?>">
	                 				</td>
	                			</tr>
	                			<tr>
	                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('Confirm Password')?></span></td>
	                  				<td>
	                 					<input type="password"   name="conf_password_c" id="conf_password_c" size="30" class="text medium <?php if(form_error('conf_password_c')){echo 'form_error';}?>">
	                 				</td>
	                			</tr>
	                			<tr>
	                  				<td class="textlabel" colspan="2">&nbsp;</td>
	                			</tr>
	                			<?php if($is_discount_card_activated){?>
	                			<?php if(isset($is_set_discount_card_setting) && $is_set_discount_card_setting){?>
	                			<tr>
	                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('Discount Card Number')?></span></td>
	                  				<td>
	                  					<input type="text" name="discount_card_number" id="discount_card_number" value="<?php echo set_value('discount_card_number'); ?>" class="text medium"/>
	                  				</td>
	                			</tr>
	                			<?php }?>
	                			<?php }else{?>
	                			<tr>
	                  				<td class="textlabel"><span style="padding-left:20px;color:#C3C3C3;"><?php echo _('Discount Card Number')?></span></td>
	                  				<td>
	                  					<input type="text" name="discount_card_number" id="discount_card_number"   disabled="disabled" />
	                  				</td>
	                			</tr>
	                			<?php }?>
	                			<tr>
	                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('Newsletters')?></span></td>
	                  				<td>
	                 					<input type="checkbox" value="1" name="newsletter" id="newsletter" <?php if(set_value('newsletter') == 1){?>checked="checked"<?php }?> />
	                 				</td>
	                			</tr>
	                			<tr>
	                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('Notifications')?></span></td>
	                  				<td>
	                 					<input type="checkbox" value="1" name="notifications" id="notifications" onclick="javascript: $('.notify_show').toggle();" <?php if(set_value('notifications') == 1){?>checked="checked"<?php }?> />
	                 				</td>
	                			</tr>
	                			<tr class="notify_show" <?php if(set_value('notifications') && set_value('notifications') == 1){}else{?>style="display: none;"<?php }?>>
		                  			<td class="textlabel"><span style="padding-left:20px"><?php echo _('Vat number')?></span></td>
		                  			<td>
	                  					<input type="text" name="vat_c" id="vat_c" value="<?php echo set_value('vat_c'); ?>" class="text medium"/>
	                  				</td>
		                		</tr>
	                			<tr>
					                <td class="textlabel">
					                	<span style="padding-left:20px">
					                		<?php echo _('Special discount for this client in')?> % 
					                		<br /> 
					                		(<?php echo _("Just enter a figure only like '5', else leave blank");?>)
					                	</span>
					                </td>
	                				<td>
										<input type="text" name="disc_per_client" id="disc_per_client" value="<?php echo set_value('disc_per_client'); ?>" class="text medium"/>
									</td>
	                			</tr>
	                			
								<tr>
					                <td class="textlabel"><span style="padding-left:20px"><?php echo _('Client Number')?></span></td>
	                				<td>
										<input type="text" name="client_number" id="client_number" value="<?php echo set_value('client_number'); ?>" class="text medium"/>
									</td>
	                			</tr>
	                			<tr>
					                <td class="textlabel"></td>
	                				<td>
										<input type="submit" name="add_client" id="add_client" value="<?php echo _('ADD');?>" />
									</td>
	                			</tr>
	              			</tbody>
			            </table>
			    	</form>
          		</div>
        	</div>
      	</div>
    </div>
    <!-- /content -->
