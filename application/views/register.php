<!-- WHEN LOGGED IN -->
<style type="text/css">
div.success{
    background: none repeat scroll 0 0 #D6F9D6;
    border: 1px dashed green;
    color: green;
    font-weight: bold;
    padding: 5px;
}
div.error{
    background: none repeat scroll 0 0 #FCE3E6;
    border: 1px dashed red;
    color: red;
    font-weight: bold;
    padding: 5px;
}
.mainbanner{
background-image:url(<?php echo base_url();?>assets/images/mainbanner.jpg);
	
}
</style>
 
<div class="mainbanner">
	<div class="bannerrtxt">
    	<p><?php echo _('NA promotion or a planned leave? No problem: with 1 click of a button, via the built-in email system all your customers informed');?>!...<a target="_blank" href="/Docs/OBS-FLYER.pdf"></a></p>
	</div>
</div>	 
<div class="midlftmain">
  <h4><?php echo _('Free Sign Up - DEMO Request'); ?></h4>
  <p>&nbsp;</p>
  <p align="left"><?php echo _('Are you a small or wholesaler and are interested in our online ordering, please feel free the form below and we will give you more information on the options, rates, demo, video tutorials, ...'); ?></p>
  <p align="left">&nbsp;</p>
  <p align="left"><?php echo _('Sre you');?> <u><strong><?php echo _('no')?></strong></u> <?php echo _('owner of a small or wholesale business you will not access the secure section. Do you have a question, comment or are you interested in a collaboration? can you let us know through our')?> <a href="<?php echo base_url();?>welcome/contact"><?php echo _('Contact');?></a>.<br>
    <br>
  </p>
  
  <?php if(isset($_SESSION['message'])) { ?>
  <div class="<?php echo $_SESSION['message']['status']; ?>">
     <p><?php echo $_SESSION['message']['response']; ?></p>
  </div>
  <?php } ?>
  
  <?php if($this->session->flashdata('success')) { ?>
  <div class="success">
     <p><?php echo $this->session->flashdata('success'); ?></p>
  </div>
  <?php } ?>
  
  <p>&nbsp;</p>
  <div class="form">
    <form action="<?php echo base_url();?>welcome/register" enctype="multipart/form-data" method="post" id="frm_companies_add" name="frm_companies_add">
      <input type="hidden" value="register" name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW">
      <table width="100%" cellspacing="0" cellpadding="0" border="0" style="text-transform:capitalize;">
        <tbody>
          <tr>
            <td><table width="100%" cellspacing="0" cellpadding="0" border="0" class="register">
                <tbody>
                  <tr>
                    <td colspan="2"><br>
                      <br>
                      <span style="color:#FF0000" id="dup_msg"></span></td>
                  </tr>
                       <tr>
                    <td width="42%" align="right"><?php echo _('Company Name');?><span class="red_star">&nbsp;*</span></td>
                    <td width="58%"><input type="text" size="30" class="input" id="company_name" name="company_name" value="<?php echo set_value('company_name'); ?>" >
                    <?php if(form_error('company_name')){echo "<br />".form_error('company_name');}?>
                    </td>
                  </tr>
                  <tr>
                    <td align="right"><?php echo _('Sector');?><span class="red_star">&nbsp;*</span></td>
                    <td><select style="width:208px; padding-top:2px;" type="select" id="type_id" name="type_id" class="input">
                        <option value="-1">-- <?php echo _('Select');?> --</option>
                        <?php if(sizeof($company_type)>0):
						        foreach($company_type as $type):
						  ?>
                        <option value="<?php echo $type->id;?>"  <?php  if(set_value('type_id')==$type->id){echo 'selected="selected"';}?> ><?php echo $type->company_type_name;?></option>
						
                        <?php endforeach;endif;?>
                      </select>
                      <?php if(form_error('type_id')){echo "<br />".form_error('type_id');}?>
                    </td>
                  </tr>
                  <tr>
                    <td align="right"><?php echo _('Your first name')?><span class="red_star">&nbsp;*</span></td>
                    <td><input type="text" size="30" class="input" id="first_name" name="first_name" value="<?php echo set_value('first_name'); ?>" >
                    <?php if(form_error('first_name')){echo "<br />".form_error('first_name');}?>
                    </td>
                  </tr>
                  <tr>
                    <td align="right"><?php echo _('Your surname');?><span class="red_star">&nbsp;*</span></td>
                    <td><input type="text" size="30" class="input" id="last_name" name="last_name" value="<?php echo set_value('last_name'); ?>" >
                    <?php if(form_error('last_name')){echo "<br />".form_error('last_name');}?>
                    </td>           
                  </tr>
                  <tr>
                    <td align="right"><?php echo _('Email')?><span class="red_star">&nbsp;*</span></td>
                    <td><input type="text" onchange="check_email();" size="30" class="input" id="email" name="email" value="<?php echo set_value('email'); ?>" >
                    <?php if(form_error('email')){echo "<br />".form_error('email');}?>
                    </td>                  	
                  </tr>
                  <tr>
                    <td align="right"><?php echo _('Tel'); ?><span class="red_star">&nbsp;*</span></td>
                    <td><input type="text" size="30" class="input" id="phone" name="phone" value="<?php echo set_value('phone'); ?>" >
                    <?php if(form_error('phone')){echo "<br />".form_error('phone');}?>
                    </td>                  	
                  </tr>
                  <tr>
                        <td align="right"><?php echo _('I have a website'); ?>&nbsp;<span class="red_star">*</span></td>
                        <td width="60%" style="text-align:left;"><input type="checkbox" checked="checked" value="1" name="have_website" id="have_website" onclick="display_nowebsite();" />
                        </td>
                      </tr>
                  <tr id="website" class="havewebsite" >
                    <td align="right"><?php echo _('Website'); ?><span class="red_star">&nbsp;*</span><br />(e.g. www.website.domain)</td>
                    <td><input type="text" size="30" class="input" id="website" name="website" value="<?php echo set_value('website'); ?>" >
                    <?php if(form_error('website')){echo "<br />".form_error('website');}?>
                    </td>                  	
                  </tr>
                  <tr id="package" class="nowebsite" style="display: none;">
                        <td align="right"> <?php echo _('I am interested in package'); ?><span class="red_star">*&nbsp;<span class="alignright"></span></span></td>
                        <td width="208px" style="text-align:left; padding-top:2px;"><select id="package" name="package" class="input">
					<option value="-1">--  <?php echo _('Select'); ?> --</option>
                           <?php if(!empty($packages)):
					   			foreach($packages as $p):?>
					   <option value="<?php echo $p->id;?>" <?php  if(set_value('type_id')==$p->id){echo 'selected="selected"';}?> ><?php echo $p->package_name;?></option>
					   <?php endforeach; endif;?>
                          </select>
                          <?php if(form_error('package')){echo "<br />".form_error('package');}?>
                        </td>
                      </tr>
                      <tr id="domain" class="nowebsite" style="display: none;">
                        <td align="right"><?php echo _('Which domain would you'); ?>&nbsp;<span class="red_star">*</span><br />(e.g. www.website.domain)</td>
                        <td width="60%"><input type="text" id="domain" class="input" name="domain" value="<?php echo set_value('domain'); ?>" >
                        <?php if(form_error('domain')){echo "<br />".form_error('domain');}?>
                        </td>
                      </tr>
                      <tr class="nowebsite" style="display: none;">
                        <td align="right"><?php echo _('Register Your domain Name'); ?>&nbsp;<span class="red_star">*</span></td>
                        <td width="60%" style="text-align:left;"><select id="canregister" name="canregister">
                            <option name="yes">Yes</option>
                            <option name="no">No</option>
                          </select>
                        </td>
                      </tr>
                </tbody>
              </table></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><table width="100%" cellspacing="0" cellpadding="0" border="0" class="register">
                <tbody>
                  <tr>
                    <td width="42%" align="right"><?php echo _('Address');?><span class="red_star"> + nr&nbsp;*</span></td>
                    <td width="58%"><input type="text" size="30" class="input" id="address" name="address" value="<?php echo set_value('address'); ?>" >
                    <?php if(form_error('address')){echo "<br />".form_error('address');}?>
                    </td>                  	
                  </tr>
                  <tr>
                    <td align="right"><?php echo _('Postcode');?><span class="red_star">&nbsp;*</span></td>
                    <td><input type="text" size="30" class="input" id="zipcode" name="zipcode" value="<?php echo set_value('zipcode'); ?>" >
                    <?php if(form_error('zipcode')){echo "<br />".form_error('zipcode');}?>
                    </td>                	
                  </tr>
                  <tr>
                    <td align="right"><?php echo _('City');?><span class="red_star">&nbsp;*</span></td>
                    <td><input type="text" size="30" class="input" id="city" name="city" value="<?php echo set_value('city'); ?>" >
                    <?php if(form_error('city')){echo "<br />".form_error('city');}?>
                    </td>                  	
                  </tr>
                  <tr>
                    <td align="right"><?php echo _('Country')?><span class="red_star">&nbsp;*</span></td>
                    <td><select style="width:208px" type="select" id="country_id" name="country_id" class="input">
                        <option value="-1">-- <?php echo _('Select')?> --</option>
                        <?php if(sizeof($country)>0):
						        foreach($country as $countries):?>
                        <option value="<?php echo $countries->id;?>" <?php  if(set_value('country_id')==$countries->id){echo 'selected="selected"';}?> ><?php echo $countries->country_name;?></option>

                        <?php endforeach;endif;?>
                      </select>
                      <?php if(form_error('country_id')){echo "<br />".form_error('country_id');}?>
                      </td>
                  </tr>
                  <tr>
                    <td align="right"><?php echo _('VAT No.'); ?><span class="red_star">&nbsp;</span></td>
                    <td><input type="text" size="30" class="input" id="vat" name="vat"></td>
                  </tr>
                  <tr>
                    <td align="right"><?php echo _('Choose a Username');?><span class="red_star">&nbsp;*</span></td>
                    <td><input type="text" onchange="check_username();" size="30" class="input" id="r_username" name="username" value="<?php echo set_value('username'); ?>" >
                    <?php if(form_error('username')){echo "<br />".form_error('username');}?>
                    </td>                  	
                  </tr>
                  <tr>
                    <td align="right"><?php echo _('Choose a Password');?><span class="red_star">&nbsp;*</span></td>
                    <td><input type="password" size="30" class="input" id="password" name="password">
                    <?php if(form_error('password')){echo "<br />".form_error('password');}?>
                    </td>
                  </tr>
                  <tr>
                    <td align="right"><?php echo _('Confirm Password');?><span class="red_star">&nbsp;*</span></td>
                    <td><input type="password" size="30" class="input" id="confirm_password" name="confirm_password">
                    <?php if(form_error('confirm_password')){echo "<br />".form_error('confirm_password');}?>
                    </td>
                  </tr>
                  <tr>
		                 <td align="right" ><?php echo _('Enter the word you see');?><span class="red_star">&nbsp;*</span></td>
	                 <td>
	                    <div id="cap-img">
	                		<?php print_r($captcha['image']); ?>
	                		<input type="text" class="input" name="captcha" id="captcha" />
	                		<?php if(form_error('captcha')){echo "<br />".form_error('captcha');}?>
	                        <input type="hidden" name="captcha-enc" id="captcha-enc" value="<?php echo md5($captcha['word']); ?>" />
	                	</div>
	                 </td>
	              </tr>
	              <tr>
	              	<td></td>
	              	<td><a href="javascript:void(0);" onclick="renew_captcha();"><?php echo _('Renew Captcha');?></a></td>
	              </tr>
                </tbody>
              </table></td>
          </tr>
          <tr>
            <td><table width="100%" cellspacing="0" cellpadding="0" border="0">
                <tbody>
                  <tr>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td colspan="2"><input type="submit" class="send2" value="<?php echo _('Register');?>" id="btn_register" name="btn_register">
                      <input type="hidden" value="add" id="act" name="act">
                    </td>
                  </tr>
                </tbody>
              </table></td>
          </tr>
        </tbody>
      </table>
    </form>
    <script type="text/javascript" language="javascript">
		var frmValidator = new Validator("frm_companies_add");
		frmValidator.setCallBack(validation_callback);
		frmValidator.EnableMsgsTogether();
		frmValidator.addValidation("company_name","req","<?php echo _('Please enter a company name.'); ?>");
		frmValidator.addValidation("type_id","dontselect=-1","<?php echo _('Please define your sector.'); ?>");	
		frmValidator.addValidation("first_name","req","<?php echo _('Please give your first name.'); ?>");	
		frmValidator.addValidation("last_name","req","<?php echo _('Please give your last name.'); ?>");	
		frmValidator.addValidation("email","req","<?php echo _('Please give your email address.'); ?>");	
		frmValidator.addValidation("email","email","<?php echo _('Enter email address is not valid.'); ?>");	
		frmValidator.addValidation("phone","req","<?php echo _('Please enter a valid phone number'); ?>");			
		frmValidator.addValidation("address","req","<?php echo _('Please give an address.'); ?>");	
		frmValidator.addValidation("zipcode","req","<?php echo _('Please enter a postcode.'); ?>");	
		frmValidator.addValidation("city","req","<?php echo _('Please select a city.'); ?>");	
		frmValidator.addValidation("country_id","dontselect=-1","<?php echo _('Please select a country.'); ?>");	
		frmValidator.addValidation("username","req","<?php echo _('Please enter a username.'); ?>");	
		frmValidator.addValidation("password","req","<?php echo _('Please enter a password.'); ?>");
		frmValidator.addValidation("confirm_password","req","<?php echo _('Please confirm your password.'); ?>");
		frmValidator.addValidation("captcha","req","<?php echo _('Please enter the word given in Captcha.'); ?>");
		frmValidator.addValidation("website","req","<?php echo _('Please give your website URL.'); ?>","VWZ_IsChecked(document.forms['frm_companies_add'].elements['have_website'],'1')");
		frmValidator.addValidation("website","regexp=(http://){0,1}(https://){0,1}(w){3}\.[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,4}(/\S*)?$","<?php echo _('Please enter valid website url.'); ?>");
		frmValidator.addValidation("domain","regexp=(http://){0,1}(https://){0,1}(w){3}\.[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,4}(/\S*)?$","<?php echo _('Please enter valid domain url.'); ?>");
		
		function validation_callback(result){
		if(result){

			if(!document.getElementById('have_website').checked){	
				if(document.frm_companies_add.package.value == '-1' && document.frm_companies_add.domain.value == ''){
					alert("<?php echo _('Please define your package.'); ?>\n<?php echo _('Please give your website domain.'); ?>");
					document.frm_companies_add.domain.focus();
					return false;
				}else{
					if(document.frm_companies_add.package.value == '-1'){
						alert("<?php echo _('Please define your package.'); ?>");
						return false;
					}
					if(document.frm_companies_add.domain.value == ''){
						alert("<?php echo _('Please give your website domain.'); ?>");
						return false;
					}
				}
			}
			if(MD5(document.frm_companies_add.captcha.value) != document.getElementById('captcha-enc').value ){
				alert("<?php echo _('Please re-enter the Captcha word.');?>\n<?php echo _('  Remember It is case sensitive.')?>");
				document.frm_companies_add.captcha.focus();
				return false;
			}
			
		    // VALIDAT PHONE NUMBER WITH OUT FIX FORMAT
			var RE_SSN = /[0-9\/.]$/;	
			// VALIDAT PHONE NUMBER WITH FIX FORMAT
			//var RE_SSN = /^[0-9]{3}[\/]{1}[0-9]{2}[\.]{1}[0-9]{2}[\.]{1}[0-9]{2}$/;
			phone_flag = false;
			phone = document.frm_companies_add.phone.value;
			if(RE_SSN.test(phone)){
				phone_flag = true;
			}else{
				alert("<?php echo _('Please enter a valid telephone number.'); ?>");
				document.getElementById("phone").focus();
				return false;
			} 
			
			
			
			if(phone_flag == true){
				return true;
			}
			return false;
			
		}
		return false;
		}
		function display_nowebsite(){
			$('.nowebsite').toggle();
			$('.havewebsite').toggle();
		}
	</script>
    <!-- WHEN LOGGED IN -->
  </div>
</div>
<!-- <div style="font-weight:bold; font-size:13px"><p>&nbsp;</p>Reeds <span style="font-size:18px">704</span> bestellingen via OBS</div>-->
