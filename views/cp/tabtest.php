<link href='<?php echo base_url()?>assets/cp/css/tabbedContent.css' rel='stylesheet' type='text/css' />
		<script src="<?php echo base_url(); ?>assets/cp/js/jquery-1.6.2.min.js" type="text/javascript"></script>
		<script src="<?php echo base_url()?>assets/cp/js/tabbedContent.js?version=<?php echo version;?>" type="text/javascript"></script>
		
		


<div id="tabs-2" style="align:center;width:900px">
				<div class="maininnermid">
      				<div class="billingmain">
      					
        				<p class="vetenstreep"><?php echo _('What is the setup cost')?></p>
        				<p><br><?php echo _('Setup cost is a one-time cost. You can choose from three different packages:')?><br>
        				<br>
        				</p>
        				<table width="50%">
         				 <tbody><tr>
            					<td width="54%"><?php echo _('STARTER PACKAGE:')?></td>
           						<td width="46%"><span class="groen">245&euro;</span> <?php echo _('VAT')?></td>
          					</tr>
							<tr>
            					<td><?php echo _('PLUS STARTER PACKAGE:')?></td>
            					<td><span class="groen">550&euro;</span> <?php echo _('VAT')?></td>
          					</tr>
        					<tr>
            					<td><?php echo _('ALL-IN-ONE PACKAGE:')?></td>
            					<td><span class="groen">950&euro;</span><?php echo _('VAT')?></td>
         					</tr>
        				</tbody></table>
      					<p>&nbsp;</p>
     				   <br>
     				   <form name="frm_order_package" id="frm_order_package" method="post" enctype="multipart/form-data" action="index.php?view=klant_setupkosten">
							<input name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW" value="klant_setupkosten" type="hidden">
       						<div class="form">
          					<table width="100%" border="0" cellpadding="0" cellspacing="0">
           						<tbody><tr>
              						<td align="center"><table width="70%" border="0" cellpadding="0" cellspacing="0">
                						<tbody><tr>
                    						<td valign="middle" width="35%"><?php echo _('Select Package:')?></td>
                   							<td><select name="package_id" id="package_id" type="select" style="width:215px">
													<option value="0"><?php echo _('Select')?></option>
													<option value="1"><?php echo _('STARTER PACKAGE (245.00 &euro; )')?></option>
													<option value="2"><?php echo _('STARTER PLUS PACKAGE (550.00 &euro; )')?></option>
													<option value="3"><?php echo _('ALL-IN-ONE PACKAGE (950.00 &euro; )')?></option></select></td>
                  							</tr>
                  							<tr>
                   							 <td style="padding-top:10px" colspan="2" valign="middle" width="35%"><div style="float:left; padding-right:10px">
                      						  <input name="agree" id="agree" value="1" type="checkbox">&nbsp;&nbsp;<?php echo _('I agree with the')?><a href="http://www.onlinebestelsysteem.net/gebruiksvoorwaarden.html" target="_blank" style="text-decoration:none"><?php echo _('terms of use')?></a></td>
                   							 <td></td>
											</tr>
                 						 	<tr>
                    							<td colspan="2">&nbsp;</td>
                  							</tr>
                  							<tr>
                    							<td>&nbsp;</td>
                    							<td><input name="btn_register" id="btn_register" value="Bestellen" class="send" style="margin-left:0px" type="submit">                      <input name="act" id="act" value="order_package" type="hidden"></td>
                  							</tr>
                						</tbody></table></td>
         						   </tr>
          					</tbody></table>
                    		<script language="javascript" type="text/javascript">
								var frmValidator = new Validator("frm_order_package");
								frmValidator.EnableMsgsTogether();
								frmValidator.addValidation("package_id","dontselect=0","Gelieve een pakket te kiezen");
								frmValidator.addValidation("agree","shouldselchk","Gelieve akkoord te gaan met de gebruikersvoorwaarden");
		  					</script>
        				</div>
						</form>
<br>
<br>


<div class="tabbed_content">
		  <div class="tabs">
						<div style="left: 200px;" class="moving_bg">
							&nbsp;
						</div>
						<span class="tab_item">
							<?php echo _('STARTER PAKKET')?>
						</span>
						<span class="tab_item">
							<?php echo _('STARTER PLUS PAKKET')?>
						</span>
						<span class="tab_item">
							<?php echo _('ALL-IN-ONE PAKKET')?>
						</span>
					</div>
          <div class="slide_content">
            <div style="margin-left: -600px;" class="tabslider">
              
              <ul>
                <p><?php echo _('Save money by configuring the system yourself! With this package we <br>
                 assume that the categories and products yourself inserted into the system <br>
                 (including images, descriptions, rates, etc. ..).')?> </p>
                <p>&nbsp;</p>
                <p><span class="vetenstreep"><?php echo _('This package includes:')?></span><br>
                </p>
                <ul>
                  <li><?php echo _('Activation of your account')?></li>
                  <li><?php echo _('We apply the color / style according to the look n feel of your website')?></li>
                  <li><?php echo _('If you wish, we can test (including code) on your server. This page provides a preview of how the shop eventually')?> <br><?php echo _('will look like (without having your customers see this). After all adjustments have been made (institutions, categories and products added, etc..) ')?><br><?php echo _(' We can implement the code in the existing site')?></li> 
                 <!--  <li>Ontwerp visitekaartje om de webwinkel te promoten</li>
                  <li>Weerbestendige stickers (2 stuks) met uw url om de webwinkel te promoten</li>-->
                </ul>
                <p>&nbsp;</p>
                <p><?php echo _('One time fee:')?><span class="tarief">245 &euro;</span><?php echo _('VAT')?></p>
              </ul>
              
              
              <ul>
                <p><?php echo _('If you have little time and want a ready-made system, you can opt for this package.')?></p>
                <p>&nbsp;</p>
                <p><span class="vetenstreep"<?php echo _('This package contains the STARTER PACKAGE includes:')?>></span><br>
                </p>
                <ul>
                  <li><?php echo _('System configuration according to your wishes (settings)')?></li>
                  <li><?php echo _('All categories and products are created by us')?><br><?php echo _('(max 100 - each 100 = &euro; 75)')?></li>
                  <li><?php echo _('Photos are added to products if desired')?></li>
                  <!-- <li>250 visitekaartjes om het online bestellen te promoten</li>-->
                </ul>
                <p>&nbsp;</p>
                <p><?php echo _('One time fee:')?><span class="tarief">550 &euro;</span><?php echo _('VAT')?>  (*)</p>
                <br>
              </ul>
              
              <ul>
                <p><?php echo _('If you do not feel like taking pictures of all your products?')?><br><?php echo _('NO Problem')?>
</p>
                <br>

                <p><span class="vetenstreep"><?php echo _('This package contains the STARTER PACK PLUS includes')?></span><br>
                </p>
                <ul>
                  <li><?php echo _('Spot taking pictures of all products')?><br><?php echo _('(max 100 - each 100 = &euro; 75 extra)')?></li>
                  <li><?php echo _('All the photo editing + optimize')?></li>
                  <li><?php echo _('All photographs deploy the system')?></li>
                </ul><br><br>  <p>&nbsp;</p>


                <p><?php echo _('One time fee:')?><span class="tarief">950 &euro;</span><?php echo _('VAT')?>(*)</p>
              </ul>
            </div>
          </div>
</div>

       <br>

        <p class="textklein">(*) <?php echo _('Outside a radius of 20 km (Yellow) we charge a fee of &euro; 0.25 / km')?><br><?php echo _('All prices exclude VAT')?></p>
        <p><br>
        </p>
        <p>&nbsp;</p>
        <p class="textklein">&nbsp;</p>
<p>&nbsp;</p>
        <p>&nbsp;</p>
      
      				</div>
    			</div>
			
			
			</div>