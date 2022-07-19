 <!-- MAIN -->
<div id="main">
	<div id="main-header">
    	<h2><?php echo _('Customer Details')?></h2>
      	<span class="breadcrumb"><a href="<?php echo base_url()?>cp/cdashboard"><?php echo _('Home')?></a> &raquo; <a href="<?php echo base_url()?>cp/clients"><?php echo _('customers')?></a> &raquo; <?php echo _('customer details')?></span>
	</div>
    <div id="content">
    	<div id="content-container">
			<div class="box">
          		<h3><?php echo _('My Account')?></h3>
          		<div class="table">
            		<table border="0">
              			<tbody>
			 			<?php if($client_details):?>
			 			<?php foreach($client_details as $client_detail):?>
			     			<tr>
                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('Created Date')?> </span></td>
                  				<td><?php echo mdate('%Y-%m-%d',human_to_unix($client_detail->created_c));?></td>

                			</tr>
                			<tr>
                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('Created Time')?></span></td>
                  				<td><?php echo mdate('%h:%i %a',human_to_unix($client_detail->created_c));?></td>
                			</tr>
                			<tr>
                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('First Name')?></span></td>
                 				<td><?php echo stripslashes( $client_detail->firstname_c );?></td>
                			</tr>
                			<tr>
                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('Lasr Name')?></span></td>
                  				<td><?php echo stripslashes( $client_detail->lastname_c);?></td>
                			</tr>
                			<tr>
			                  <td class="textlabel"><span style="padding-left:20px"><?php echo _('Business')?></span></td>
            			      <td><?php echo $client_detail->company_c;?></td>
                			</tr>
                            <tr>
            			    	<td class="textlabel"><span style="padding-left:20px"><?php echo _('Address')?></span></td>
                  				<td><?php echo stripslashes( $client_detail->address_c )?></td>
                			</tr>

                			<tr>
                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('House number')?></span></td>
                  				<td><?php echo stripslashes( $client_detail->housenumber_c )?></td>
                			</tr>
                             <tr>
                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('Postal Code')?></span></td>
				                <td><?php echo $client_detail->postcode_c?></td>
                			</tr>

                			<tr>
                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('City')?></span></td>
                  				<td><?php echo stripslashes( $client_detail->city_c )?></td>

                			</tr>
                			<tr>
                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('Country')?></span></td>
                  				<td><?php echo $client_detail->country_name?></td>
                			</tr>
                			<tr>
                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('Telephone')?></span></td>
                  				<td><?php echo $client_detail->phone_c?></td>
                			</tr>
                			<tr>
				                <td class="textlabel"><span style="padding-left:20px"><?php echo _('GSM')?></span></td>
                				<td><?php echo $client_detail->mobile_c?></td>
                			</tr>
                			<tr>
                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('FAX')?></span></td>
                  				<td><span style="padding-right:250px"><?php echo $client_detail->fax_c;?></span></td>
                			</tr>
                			<?php if($client_detail->company_c){?>
	                			<tr>
	                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('Vat number')?></span></td>
	                  				<td><span style="padding-right:250px"><?php echo $client_detail->vat_c;//  $client_detail->vat_c ;?></span></td>
	                			</tr>
                			<?php }?>
                			
                			<?php if($is_discount_card_activated){?>
                			<?php if(isset($is_set_discount_card_setting) && $is_set_discount_card_setting){?>
                			<tr>
                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('Discount Card Number')?></span></td>
                  				<td>
                  					<form method="post" action="<?php echo base_url()?>cp/clients/lijst/client_details/<?php echo $client_detail->id; ?>/<?php echo $comp_id; ?>">

                              

                  						<input type="text" name="discount_card_number" id="discount_card_number" value="<?php if(isset($client_number->discount_card_number)){ echo $client_number->discount_card_number; }?>" />
                  						<input type="hidden" name="client_id" id="client_id" value="<?php echo $client_detail->id; ?>" />
										<input type="hidden" name="company_id" id="company_id" value="<?php echo $comp_id; ?>" />
										<input type="submit" name="add_discount_card_no" id="add_discount_card_no" value="<?php echo _('Update'); ?>" />
                  					</form>
                  				</td>
                			</tr>
                			<?php }?>
                			<?php }else{?>
                			<tr>
                  				<td class="textlabel"><span style="padding-left:20px;color:#C3C3C3;"><?php echo _('Discount Card Number')?></span></td>
                  				<td>
                  					<input type="text" name="discount_card_number" id="discount_card_number" value="" disabled="disabled" />
									<input type="button" name="add_discount_card_no" id="add_discount_card_no" value="<?php echo _('Update'); ?>" disabled="disabled" />
                  				</td>
                			</tr>
                			<?php }?>
                			
                			<tr>
				                <td class="textlabel">
				                	<span style="padding-left:20px">
				                		<?php echo _('Special discount for this client in')?> % 
				                		<br /> 
				                		(<?php echo _("Just enter a figure only like '5', else leave blank");?>)
				                	</span>
				                </td>
                				<td>
								<form method="post" action="<?php echo base_url()?>cp/clients/lijst/client_details/<?php echo $client_detail->id; ?>/<?php echo $comp_id; ?>">
									<input type="text" name="disc_per_client" id="disc_per_client" value="<?php if($client_number && isset($client_number->disc_per_client)){echo $client_number->disc_per_client; }?>" />
									&nbsp;&nbsp;
									<input type="hidden" name="client_id" id="client_id" value="<?php echo $client_detail->id; ?>" />
									<input type="hidden" name="company_id" id="company_id" value="<?php echo $comp_id; ?>" />
									<input type="submit" name="add_client_discount" id="add_client_discount" value="<?php echo _('Update'); ?>" />
								</form>
								</td>
                			</tr>
                			
							<tr>
				                <td class="textlabel"><span style="padding-left:20px"><?php echo _('Client Number')?></span></td>
                				<td>
								<form method="post" action="<?php echo base_url()?>cp/clients/lijst/client_details/<?php echo $client_detail->id; ?>/<?php echo $comp_id; ?>">
									<input type="text" name="client_number" id="client_number" value="<?php if($client_number && isset($client_number->client_number)){echo $client_number->client_number; }?>" />
									&nbsp;&nbsp;
									<input type="hidden" name="client_id" id="client_id" value="<?php echo $client_detail->id; ?>" />
									<input type="hidden" name="company_id" id="company_id" value="<?php echo $comp_id; ?>" />
									<input type="submit" name="add_client_no" id="add_client_no" value="<?php echo _('Update'); ?>" />
								</form>
								</td>
                			</tr>
						<?php endforeach;?>
						<?php endif;?>
              			</tbody>
		            </table>
          		</div>
        	</div>
      	</div>
    </div>
    <!-- /content -->
