<style>
	#paypal_infos{
		margin-left:100px;
	}
	#paypal_infos p{
		margin-bottom:10px;
	}
	#paypal_infos p lable{
		width:50px;
	}
</style>

<div id="content">
	<div id="main-header">
    	<h2><?php echo _('Merchant Info')?>  </h2>
	</div>
	
    <?php if(!empty($success_msg)) :?>
    	<div id="succeed_status"><?php echo _("Successfully Added info.");?></div>
    <?php endif;?>
    <?php if(!empty($failed_msg)) :?>
    	<div id="error_status"><?php echo _("Error occurred while updating info");?></div>
    <?php endif;?>
    
	<div id="content-container">
    	<div class="box">
        <?php if($show_form=='yes'):?>
        	<form id="registered" method="post">
				<?php echo _('Already Have an account?');?> 	<input type="submit" name="already_registered" value="<?php echo _('Click here');?>"><br />
			</form>
			
        	<h3><?php echo _("Create Cardgate Account"); ?></h3>
        	<div class="table merchant_create">
				<form method="POST" action="<?php echo base_url();?>cp/payment/payment_method" name="submit" enctype="multipart/form-data">
					<hr>
					<h2><?php echo _('Company information');?></h2>
					<hr>
					<table cellspacing="0" class="override">
              			<tbody>
	              			<tr>
	                  			<td class="textlabel"><?php echo _('Account name');?></td>
	                  			<td><input type="text" class="text short" name="nn_id" value="" placeholder="<?php echo _('shortname');?>" /></td>
	               			</tr>
	               			<tr>
	                  			<td class="textlabel"><?php echo _('Name');?></td>
	                  			<td><input type="text" class="text short" name="legal_company_name" placeholder="<?php echo _('test company');?>" value="" /></td>
	               			</tr>
	               		
	               			<tr>
	                  			<td class="textlabel"><?php echo _('Registration number');?></td>
	                  			<td><input type="text" class="text short" name="company_registration_number" placeholder="geef een willekeurig nr op" value="" /></td>
	               			</tr>
	               			<!-- <tr>
	                  		<td class="textlabel"><?php echo _('Company registration document');?></td>
	                  			<td><input type="file" name="coc_doc" /></td>
	               			</tr>
	               			 -->
	               			<tr>
	                  			<td class="textlabel"><?php echo _('VAT Number');?></td>
	                  			<td> <input type="text" class="text short" name="company_vat_number" placeholder="BExxxxxxx" value="" /></td>
	               			</tr>
	               			<tr>
	                  			<td class="textlabel"><?php echo _('Telephone');?></td>
	                  			<td><input type="text" class="text short" name="company_telephone" placeholder="0123456789" value="" /></td>
	               			</tr>
	               			<tr>
	                  			<td class="textlabel"><?php echo _('Company E-mail');?></td>
	                  			<td> <input type="text" class="text short" name="company_email" placeholder="naam@uwwinkel.be" value="" /></td>
	               			</tr>
	               			<tr>
	                  			<td class="textlabel"><?php echo _('Website');?></td>
	                  			<td><input type="text" class="text short" name="company_url" placeholder="http://www.uwwinkel.be" value="" /></td>
	               			</tr>
	               		</tbody>
	               	</table>
					<br />
					<hr>
					<h2><?php echo _('General information');?></h2>
					<hr>
					<table cellspacing="0" class="override">
              			<tbody>
              			
	              			<tr>
	                  			<td class="textlabel"><?php echo _('Last name');?></td>
	                  			<td><input type="text" class="text short" name="comm_last_name" value="" /></td>
	               			</tr>
	               			<tr>
	                  			<td class="textlabel"><?php echo _('First name');?></td>
	                  			<td><input type="text" class="text short" name="comm_first_name" value="" /></td>
	               			</tr>
	               			
	               			<?php /*?><tr>
	                  			<td class="textlabel"><?php echo _('Date of Birth');?></td>
	                  			<td> <input type="text" class="text short" name="main_owners_dob" placeholder="yyyy/mm/dd" value="" /></td>
	               			</tr>
	               			<tr>
	                  			<td class="textlabel"><?php echo _('Place of Birth');?></td>
	                  			<td> <input type="text" class="text short" name="main_owners_pob" placeholder="" value="" /></td>
	               			</tr><?php */?>
	               		</tbody>
	               	</table>
	               	<?php /*?>
				 	<br />
				 	<hr>
					<h2><?php echo _('Technical contact');?></h2>
					<hr>
					<table cellspacing="0" class="override">
              			<tbody>
              			
	              			<tr>
	                  			<td class="textlabel"><?php echo _('Last name');?></td>
	                  			<td><input type="text" class="text short" name="tech_last_name" value="" required/></td>
	               			</tr>
	               			<tr>
	                  			<td class="textlabel"><?php echo _('First name');?></td>
	                  			<td><input type="text" class="text short" name="tech_first_name" value="" required /></td>
	               			</tr>
	               			<tr>
	                  			<td class="textlabel"><?php echo _('Technical Email');?></td>
	                  			<td><input type="text" class="text short" name="tech_email" value="" required/></td>
	               			</tr>
	               			
	               		</tbody>
	               	</table>
				 	<br />
				  	<hr>
					<h2><?php echo _('Legal representative');?></h2>
					<hr>
					<table cellspacing="0" class="override">
              			<tbody>
              			
	              			<tr>
	                  			<td class="textlabel"><?php echo _('Last name');?></td>
	                  			<td><input type="text" class="text short" name="legal_last_name" value="" required/></td>
	               			</tr>
	               			<tr>
	                  			<td class="textlabel"><?php echo _('First name');?></td>
	                  			<td><input type="text" class="text short" name="legal_first_name" value="" required/></td>
	               			</tr>
	               			<tr>
	                  			<td class="textlabel"><?php echo _('Legal Email');?></td>
	                  			<td><input type="text" class="text short" name="legal_email" value="" required/></td>
	               			</tr>
	               		</tbody>
	               	</table>
				 	<br />	
				  	<hr>
					<h2><?php echo _('Administrative/financial contact');?></h2>
					<hr>
					<table cellspacing="0" class="override">
	              		<tbody>
	              			
	              			<tr>
	                  			<td class="textlabel"><?php echo _('Last name');?></td>
	                  			<td><input type="text" class="text short" name="admin_last_name" value="" required/></td>
	               			</tr>
	               			<tr>
	                  			<td class="textlabel"><?php echo _('First name');?></td>
	                  			<td><input type="text" class="text short" name="admin_first_name" value="" required/></td>
	               			</tr>
	               			<tr>
	                  			<td class="textlabel"><?php echo _('Administrative Email')?></td>
	                  			<td><input type="text" class="text short" name="admin_email" value="" required/></td>
	               			</tr>
	               		</tbody>
	               	</table>
				 	<br />
					<hr>
					<h2><?php echo _('Owner information');?></h2>
					<hr>
					<table cellspacing="0" class="override">
	              		<tbody>
	              			<tr>
	                  			<td class="textlabel"><?php echo _('Full christian name');?></td>
	                  			<td><input type="text" class="text short" name="main_owners_name" placeholder="Uw volledige naam" value="" /></td>
	               			</tr>
	               			<tr>
	                  			<td class="textlabel"><?php echo _('Date of birth');?></td>
	                  			<td> <input type="text" class="text short" name="main_owners_dob" placeholder="dd-mm-jaar" value="" /></td>
	               			</tr>
	               			<tr>
	                  			<td class="textlabel"><?php echo _('Place of birth');?></td>
	                  			<td> <input type="text" class="text short" name="main_owners_pob" placeholder="Stad" value="" /></td>
	               			</tr>
	               		</tbody>
	               	</table><?php */?>
					<br />
					<input type="submit" name="submit" value="<?php echo _("Register");?>" />
				</form>
			</div>
		<?php elseif($show_form=='no'):?>
			<h3><?php echo _("Company information"); ?></h3>
			<div class="table">
				<table class="merchant_status" cellspacing="0">
              		<thead>
                		<tr>
                  				<th><?php echo _("Merchant ID"); ?></th>
                  				<th><?php echo _("Username"); ?></th>
                  				<th><?php echo _("Status Id"); ?></th>
                  				<th><?php echo _("Status"); ?></th>
                  				<th><?php echo _("Stage"); ?></th>
                  		</tr>
					</thead>
              		<tbody>
              		<?php if(isset($merchant_info)) {?>
              			<tr>
              				<td> <?php echo $merchant_info['merchant']['nn_id'];?> </td>
              				<td> <?php echo $merchant_info['merchant']['name'];?> </td>
              				<td> <?php echo $merchant_info['merchant']['status_id'];?> </td>
              				<td> <?php echo $merchant_info['merchant']['status'];?> </td>
              				<td> <span class="label <?php echo $class;?>"> <?php echo $merchant_info['merchant']['stage'];?> </span> </td>
              			
              			</tr>
              			<?php } ?>
              		</tbody>	
            	</table>
            	<table class="textkleiner merchant_status" width="100%" cellspacing="0" cellpadding="0" border="0">
            		
            		<tbody>
            		 <tr></tr>
            		  <tr>
					    <td width="58%">&nbsp;</td>
				        <td width="1%"><span class="label label-default"><?php echo _("PROSPECT"); ?></span></td>
				        <td width="40%" class="textkleiner"><div class="textkleiner"><?php echo _("Prospect stage"); ?></div></td>
			          </tr>
				  	  <tr>
					    <td width="58%">&nbsp;</td>
				        <td width="1%"><span class="label label-primary"><?php echo _("Awaiting.Merchant"); ?></span></td>
				        <td width="40%" class="textkleiner"><div class="textkleiner"><?php echo _("Waiting for merchant to supply data/info"); ?></div></td>
			          </tr>
				  	  <tr>
					    <td width="58%">&nbsp;</td>
				        <td width="1%"><span class="label label-info"><?php echo _("Awaiting.CDD.Officer"); ?></span></td>
				        <td width="40%" class="textkleiner"><div class="textkleiner"><?php echo _("Waiting for CDD to check merchant data"); ?></div></td>
			          </tr>
			          
				      <tr>
					    <td width="58%">&nbsp;</td>
				        <td width="1%"> <span class="label label-info"><?php echo _("Awaiting.Compliance.Officer"); ?></span> </td>
				        <td width="40%" class="textkleiner"><div class="textkleiner"> <?php echo _("Waiting for Compliance approval"); ?></div></td>
			          </tr>
			          
			          <tr>
					    <td width="58%">&nbsp;</td>
				        <td width="1%"> <span class="label label-warning"><?php echo _("Awaiting.Risk.Officer"); ?></span></td>
				        <td width="40%" class="textkleiner"><div class="textkleiner"><?php echo _("Waiting for Risk approval"); ?></div></td>
			          </tr>
				    
				      <tr>
					    <td width="58%">&nbsp;</td>
				        <td width="1%"> <span class="label label-danger"><?php echo _("Cancelled"); ?></span></td>
				        <td width="40%" class="textkleiner"><div class="textkleiner"> <?php echo _("Application was cancelled"); ?></div></td>
			          </tr>
				    
				      <tr>
					    <td width="58%">&nbsp;</td>
				        <td width="1%"> <span class="label label-success"><?php echo _("Enabled"); ?></span> </td>
				        <td width="40%" class="textkleiner"><div class="textkleiner"><?php echo _("Account is enabled"); ?></div></td>
			          </tr>
				    
			        </tbody>
            	
            	</table><br /><br /><div align="center">U kan inloggen in uw controlepaneel van CARDGATE via <a href="https://merchants.cardgateplus.com" target="_blank">https://merchants.cardgateplus.com</a><br />
(wachtwoord werd u via email doorgestuurd)<br /><br />
Ga naar <strong>Merchant gegevens</strong> - <strong>CURO backoffice</strong> - <strong>gegevens</strong>. Vul daar alles verder aan tot de statusbar tot 100% komt te staan <br />
(tip: ga met de muis over de statusbar om te kijken wat je nog moet aanvullen).<br />
<br />

</div>
			</div>
			<br/>
			<h3><?php echo _("Available Payment Methods"); ?></h3>
			<div class="table">
				<form id="cp_payment" method="post" action="<?php echo current_url();?>">
					<table cellspacing="0">
						<thead>
	                		<tr>
	                  			<td style="text-align:center" colspan="8" class="notice_text"><?php echo _("** You can choose which payment methods are made available on your website."); ?></td>
	                		</tr>
	                		<?php if(!empty($success)) :?>
	    					<tr> <td>  <div id="succeed_status"><?php echo _("Successfully Selected Payment Methods."); ?></div> </td> </tr>
	    					<?php endif;?>
	    					
	        			</thead>
	        			<?php if(isset($available_methods)) { ?>
	        			<tbody>
	        			
		        		 	<?php foreach($available_methods as $method):?>
						  	<tr>
						  		<td>
						  			<input type="checkbox" id="<?php echo $method['id'];?>" name="payment_method[]" value="<?php echo $method['id'];?>" <?php if(in_array($method['id'],$selected_methods)){ echo 'checked';}?> <?php if($method['value'] == 'paypal'){?>onclick="javascript:$('#paypal_infos').toggle();"<?php }?>/>
						  			<?php echo $method['payment_method'];?>
						  			<?php if($method['value'] == 'paypal'){?>
									<div id="paypal_infos" <?php if(!in_array($method['id'],$selected_methods)){?>style="display:none;"<?php }?>>
										<p>
											<label>
												<?php echo _('Username');?>:
											</label>
										 	<input type="text" id="paypal_username" class="text short"name="paypal_username" value="<?php if(isset($merchant_info_all) && isset($merchant_info_all['0']['paypal_username'])){ echo $merchant_info_all['0']['paypal_username'];}?>"> 
										</p>
										<p>
											<label>
												<?php echo _('Password');?>:
											</label>
											<input type="password" id="paypal_password" class="text short"name="paypal_password" value="<?php if(isset($merchant_info_all) && isset($merchant_info_all['0']['paypal_password'])){ echo $merchant_info_all['0']['paypal_password'];}?>">
										</p>
										<p>
											<label><?php echo _('Signature');?>:</label>
											<input type="text" id="paypal_sign" class="text medium" name="paypal_sign" value="<?php if(isset($merchant_info_all) && isset($merchant_info_all['0']['paypal_sign'])){ echo $merchant_info_all['0']['paypal_sign'];}?>">
										</p>
									</div>
						  			<?php }?>
					      		</td>
					      	</tr>
						 	<?php endforeach;?>

		        			<tr>
		        				<td>
		        			 		<input type="submit" name="btn_set" id="btn_set" class="submit" value="<?php echo _('SET');?>">
		        				</td>
		        			</tr>
		        			
	        			</tbody>
	        			<?php } ?>
					</table>
				</form>
			</div>
		<?php endif;?>
		</div>
	</div>
</div>
