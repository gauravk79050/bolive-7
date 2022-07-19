<script language="javascript" type="text/javascript">

/*jQuery.noConflict();
jQuery(document).ready(function($)
{	
	$("#frm_companies_add_update").validate({
	
	   rules: {
				 company_name: {required: true},
				
				 type_id: {required: true},
				 
				 first_name:{required: true},
				
				 last_name:{required: true},
				 
				 email:{required: true,
						email: true},
				 
				 phone:{required: true,
						number:true,
						maxlength:12,
						minlength:10},
				 
				 website:{required: true,
						  url :true},
				 
				 address:{required: true},
				 
				 zipcode:{required: true,
						  number:true,
						  maxlength:8,
						  minlength:6},
				 
				 city:{required: true},
				 
				 country:{required: true},
				 
				 username:{required: true},
				 
				 password:{required: true},
				 
				 registration_date:{required: true},
				 
				 //expiry_date:{date_validation:true},
				 
				 earnings_year:{required: true}
				  
	   
			  },
			  
			  submitHandler:function(form)
			  {
					 $.post("<?php echo site_url('mcp/companies/validate');?>",
						{ "email":$('#email').val()},
							
						function(html){
								 if(html=='email exist')
									{    
										$('#email_msg').html('<?php echo _('This email-address already  exists.'); ?>');
										return false;
									}
							
									else 
									{   $.post("<?php echo site_url('mcp/companies/validate')?>",
										{"username":$('#username').val()},
											function(html){
											   
												if(html=='username exist')
													{
													   $('#email_msg').html('');
													   $('#user_msg').html('<?php echo _('This username already  exists.'); ?>');
													   return false;
													}   
												else
													{
													   $('#user_msg').html('');
													   form.submit();
													}
													
											});
									}
						});
						//date_validation();
						return false;
			  }
	
	});
	
	$.validator.addMethod("date_validation",function(value,element){
	
		var registration_date = $('#registration_date').val();
		var expiry_date=$('#expiry_date').val();
		<!--return Date.parse(registration_date) <= Date.parse(value) || value == "";-->
		var DateDiff = {
		 
			inDays: function(d1, d2) {
				var t2 = d2.getTime();
				var t1 = d1.getTime();
		 
				return parseInt((t2-t1)/(24*3600*1000));
			},
		 
			inWeeks: function(d1, d2) {
				var t2 = d2.getTime();
				var t1 = d1.getTime();
		 
				return parseInt((t2-t1)/(24*3600*1000*7));
			},
		 
			inMonths: function(d1, d2) {
				var d1Y = d1.getFullYear();
				var d2Y = d2.getFullYear();
				var d1M = d1.getMonth();
				var d2M = d2.getMonth();
		 
				return (d2M+12*d2Y)-(d1M+12*d1Y);
			},
		 
			inYears: function(d1, d2) {
				return d2.getFullYear()-d1.getFullYear();
			}
		}
	
		var d1 = new Date(registration_date);
		var d2 = new Date(expiry_date);
		var rd=d1.getFullYear();
		var ed=d2.getFullYear();
		var days=DateDiff.inDays(d1, d2);
		if((days>365) && (rd<ed))
		{
			return true;
		}
		else
		{
			return false;
		}
	},"<?php echo _('Expiry date must be after registeration date.'); ?>");


});*/
</script>
<!-- <script type="text/javascript">
	jQuery( document ).ready(function($) {

		$(document).on('change','#type_id',function(){
		   var value = $(this).val();
		   var type_id = ['1', '8', '9', '10', '12', '23'];
           $.each(value , function(index, val) { 
           	var check = type_id.includes(val);
					if(check == false){
						$('#show_menukartt_maker').attr('checked','checked');
						return false;
					}
					else{
						$('#show_menukartt_maker').removeAttr('checked');
				    }
				});
		});
   });
</script> -->

<!-- start of main body -->

<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tbody>
<tr>
    <td valign="top" align="center">
	    <table width="98%" cellspacing="0" cellpadding="0" border="0">
        <tbody>
        <tr>
            <td valign="top" align="center" style="border:#8F8F8F 1px solid">
	 	        <table width="100%" cellspacing="0" cellpadding="0" border="0">
                <tbody>
                <tr>
                    <td align="center" style="padding:15px 0px 5px 0px">
					    <table width="98%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url('');?>assets/mcp/images/bg.jpg) left top repeat-x;" class="page_caption">
                        <tbody>
                        <tr>
                            <td width="94%" align="left"><h3><?php echo _('Add Company'); ?></h3></td>
                            <td width="3%" align="right"></td>
                            <td width="3%" align="left">
							  <div class="icon_button">
							      <img width="16" height="16" border="0" style="cursor:pointer" onClick="javascript:history.back();" title="Go Back" alt="Go Back" src="<?php echo base_url('');?>assets/mcp/images/undo.jpg">
							  </div>
							</td>
                        </tr>
                        </tbody>
                        </table>
					</td>
                </tr>
				<tr>
				   <td style="padding:10px;"><?php echo validation_errors(); ?></td>
				</tr>
                <tr>
                    <td width="100%" valign="top" align="center">
											
						<form action="<?php echo base_url().'mcp/companies/companies_add_edit'; ?>" enctype="multipart/form-data" method="post" id="frm_companies_add_update" name="frm_companies_add_update">
						
						<input type="hidden" name="registered_by" id="registered_by" value="master_admin" />
											
						<table width="98%" cellspacing="0" cellpadding="0" border="0" style="border:1px solid #003366; text-align:left">
                        <tbody>
                        <tr>
                            <td height="20" bgcolor="#003366" align="left" style="padding-left:10px;" class="whiteSmallBold" colspan="5"><?php echo _('Company Information'); ?>							
							</td>
                        </tr>
                        <tr>
                            <td height="10" colspan="5">&nbsp;</td>
                        </tr>
                        <tr>
                            <td height="10" align="center" colspan="5"><span style="color:#FF0000" id="dup_msg"></span></td>
                        </tr>
                        <tr>
                            <td width="10%">
                                &nbsp;
                                <input type="hidden" value="companies_add_edit" name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW">
                            </td>
                              
                            <td width="37%">
							    <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tbody>
                                <tr>
                                    <td height="30" class="wd_text"><?php echo _('ID'); ?>&nbsp;&nbsp;</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td height="30" class="wd_text">
									    <?php echo _('Company Name'); ?><span class="red_star">*</span>
								    </td>
                                    <td>
									    <input type="text" size="30" class="textbox" id="company_name" name="company_name" value="">
								    </td>
                                </tr>
                                <tr>
                                    <td height="30" class="wd_text">
									  <?php echo _('Company Type');?><span class="red_star">*</span>
									</td>
                                    <td>
                                    <select style="width:100px; margin-top: 0 !important;" class="textbox" type="select" id="type_id_cat" name="type_id_cat">
                          <option value="-1">--<?php echo _('Select Company Group'); ?>--</option>

                          <?php if(!empty($company_type_group)) { foreach($company_type_group as $ctg) { ?>
                            <option value="<?php echo $ctg['id']; ?>"><?php echo $ctg['comp_grp_name']; ?></option>
                          <?php } } ?>
                        </select>
									   <select multiple style="width:200px" class="textbox" type="select" id="type_id" name="type_id[]" >
                                          <option value="-1">-- <?php echo _('Select Company Type'); ?> --</option>
										</select>
								    </td>
                                 </tr>
                                 <tr>
                                     <td height="30" class="wd_text">
									    <?php echo _('First Name'); ?><span class="red_star">*</span>
									 </td>
                                     <td>
									    <input type="text" size="30" class="textbox required" id="first_name" name="first_name" value="">
									 </td>
                                 </tr>
                                 <tr>
                                     <td height="30" class="wd_text">
									    <?php echo _('Last Name');?><span class="red_star">*</span>
									 </td>
                                     <td>
									     <input type="text" size="30" class="textbox required" id="last_name" name="last_name" value="">
									 </td>
                                 </tr>
                                 <tr>
                                     <td height="30" class="wd_text"><?php echo _('Email');?><span class="red_star">*</span></td>
                                     <td><input type="text" onChange="check_email(this.value);" size="30" class="textbox required" id="email" name="email" value=""><span id="email_msg"></span>
									 </td>
                                 </tr>
                                 <tr class="additional_email_row">
		                            <td height="30" class="wd_text add_email_text"><?php echo _('Additional Email');?></td>
		                            <td>
		                              <input type="text" onChange="check_email(this.value);" size="30" class="textbox" name="additional_email[]" value="">
		                            </td>
		                            <td>
		                              <span><input class="add_additional_email" value="+" style="border-radius: 0;height: 21px;width: 21px;" type="button"></span>
		                              <span><input class="remove_additional_email" value="-" style="border-radius: 0;height: 21px;width: 21px; display: none;" type="button"></span>
		                            </td>
		                          </tr>
                                 <tr>
                                     <td height="30" class="wd_text"><?php echo _('Phone');?><span class="red_star">*</span></td>
                                     <td><input type="text" size="30" class="textbox" id="phone" name="phone" value="">
                                     </td>
                                 </tr>
                                 <tr>
                                     <td height="30" class="wd_text"><?php echo _('Website');?></td>
                                     <td><input type="text" size="30" class="textbox" id="website" name="website" value=""></td>
                                 </tr>
                                 <tr>
              <td class="wd_text">
                <input type="radio" id = "mark_high_priority_client" value="<?php if(isset($cont->is_high_priority) ) { echo $cont->is_high_priority; } ?>" name = "is_high_priority" <?php if(isset($cont->is_high_priority) && ($cont->is_high_priority == '1')) { echo 'checked="checked"'; }?> >
              </td>
              <td height="30"><?php echo _('Priority client');?></td>
            </tr>
                                 </tbody>
                                 </table>
							 </td>
                             <td width="2%">&nbsp;</td>
                             <td width="37%">
							     <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                 <tbody>
                                 <tr>
                                    <td height="30" class="wd_text">&nbsp;</td>
                                    <td>&nbsp;</td>
                                 </tr>
                                 <tr>
                                    <td height="30" class="wd_text"><?php echo _('Address');?><span class="red_star">*</span></td>
                                    <td><input type="text" size="30" class="textbox" id="address" name="address" value="">
									</td>
                                 </tr>
                                 <tr>
                                    <td height="30" class="wd_text"><?php echo _('Zipcode');?><span class="red_star">*</span></td>
                                    <td><input type="text" size="30" class="textbox" id="zipcode" name="zipcode" value="">
									</td>
                                 </tr>
                                 <tr>
                                    <td height="30" class="wd_text"><?php echo _('City');?><span class="red_star">*</span></td>
                                    <td><input type="text" size="30" class="textbox" id="city" name="city" value="">
								    </td>
                                 </tr>
                                 <tr>
                                    <td height="30" class="wd_text"><?php echo _('Country');?><span class="red_star">*</span></td>
                                    <td>
									    <select style="width:215px" class="textbox" type="select" id="country_id" name="country_id">
                                          <option value="-1">-- <?php echo _('Select Country'); ?> --</option>
										  <?php if(!empty($country)):?>
                                          <?php foreach($country as $cont1):?>
                                          	<?php if ($cont1->id == 21 || $cont1->id == 150 ) { ?>
                                          	<option value="<?php echo $cont1->id; ?>"><?php echo $cont1->country_name; ?></option>
                                          <?php } ?>

										  <?php endforeach; ?>
										  <?php endif;?>
										  
                                        </select>
									</td>
                                 </tr>
                                 <tr>
                                    <td height="30" class="wd_text">&nbsp;</td>
                                    <td>&nbsp;</td>
                                 </tr>
                                 <tr>
                                    <td height="30" class="wd_text"><?php echo _('Username');?><span class="red_star">*</span></td>
                                    <td><input type="text" onChange="check_username(this.value);" size="30" class="textbox" id="username" name="username" value="" ><span id="user_msg"></span></td>
                                 </tr>
                                 <tr>
                                    <td height="30" class="wd_text"><?php echo _('Password');?><span class="red_star">*</span></td>
                                    <td><input type="text" size="30" class="textbox" id="password" name="password" value="">
									</td>
                                  </tr>
                                  </tbody>
                                  </table>
							  </td>
                              <td width="10%">&nbsp;</td>
                         </tr>
                              
                         <tr>
                             <td valign="middle" colspan="5">
							     <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                 <tbody>
                                 <tr>
                                    <td height="10" colspan="4">&nbsp;</td>
                                 </tr>
                                 <tr>
                                    <td width="270" height="80">&nbsp;</td>
                                    <td class="wd_text"><?php echo _('Admin Remarks');?>&nbsp;&nbsp;</td>
                                    <td>
									   <textarea cols="50" rows="5" class="textbox" type="textarea" id="admin_remarks" name="admin_remarks"></textarea>
									</td>
                                    <td width="281">&nbsp;</td>
                                 </tr>
                                 <tr>
                                     <td height="10" colspan="4">&nbsp;</td>
                                 </tr>
                                 <tr>
                                    <td width="270" height="80">&nbsp;</td>
                                    <td class="wd_text"><?php echo _('Resellers Remark');?>&nbsp;&nbsp;</td>
                                    <td>
									   <textarea cols="50" rows="5" class="textbox" type="textarea" id="reseller_remarks" name="reseller_remarks"></textarea>
									</td>
                                    <td width="281">&nbsp;</td>
                                 </tr>
                                 <tr>
                                     <td height="10" colspan="4">&nbsp;</td>
                                 </tr>
								 
								 <tr>
                                     <td width="270" height="30">&nbsp;</td>
                                     <td class="wd_text"><?php echo _('Account Type');?>&nbsp;&nbsp;</td>
                                     <td>
									     <select style="width:250px" class="textbox" type="select" id="ac_type_id" name="ac_type_id">
										  <?php if(!empty($account_types)):?>
                                          <?php foreach($account_types as $at):?>
                                          <option value="<?php echo $at->id?>" ><?php echo $at->ac_title; ?></option>
										  <?php endforeach;?>
										  <?php endif;?>
										  
                                        </select>
									 </td>
                                     <td width="281">&nbsp;</td>
                                 </tr>
                                 <tr>
                                    <td>&nbsp;</td>
                                    <td height="30" class="wd_text"><?php echo _('On trial??');?></td>
                                    <td>
									    <input type="checkbox" value="1" class="textbox" id="on_trial" name="on_trial" >
								    </td>
                                    <td>&nbsp;</td>
                                 </tr>
								 
                                 <tr>
                                     <td width="270" height="30">&nbsp;</td>
                                     <td class="wd_text"><?php echo _('Package Preferred');?>&nbsp;&nbsp;</td>
                                     <td>
									     <select style="width:250px" class="textbox" type="select" id="packages_id" name="packages_id">
                                          <option value="0">-- <?php echo _('Select Package'); ?> --</option>
										  <?php if(!empty($package)):?>
                                          <?php foreach($package as $cont2):?>
                                          <option value="<?php echo $cont2->id?>"><?php echo $cont2->package_name?></option>
										  <?php endforeach;?>
										  <?php endif;?>
										  
                                        </select>
									 </td>
                                     <td width="281">&nbsp;</td>
                                 </tr>
                                    
                                 <tr>
                                    <td width="270" height="30">&nbsp;</td>
                                    <td class="wd_text"><?php echo _('Email Ads');?>&nbsp;&nbsp;</td>
                                    <td>
									    <select type="select" id="email_ads" name="email_ads">
                                           <option value="0" ><?php echo _('No'); ?></option>
                                           <option value="1" ><?php echo _('Yes'); ?></option>
                                        </select>
                                    </td>
                                    <td width="281">&nbsp;</td>
                                 </tr>
                                 <tr>
                                    <td width="270" height="30">&nbsp;</td>
                                    <td class="wd_text"><?php echo _('Frontend Footer Text');?></td>
                                    <td>
									    <select type="select" id="footer_text" name="footer_text">
                                            <option value="0" ><?php echo _('No'); ?></option>
                                            <option value="1" ><?php echo _('Yes'); ?></option>
                                        </select>
								    </td>
                                    <td width="281">&nbsp;</td>
                                 </tr>
                                 <tr>
                                    <td height="10" colspan="4">&nbsp;</td>
                                 </tr>
                                 <tr>
                                    <td>&nbsp;</td>
                                    <td height="30" class="wd_text"><?php echo _('2 year Subscription');?></td>
                                    <td>
									    <input type="checkbox" value="1" class="textbox" id="5year_subscription" name="5year_subscription" >
								    </td>
                                    <td>&nbsp;</td>
                                 </tr>
                                 <tr>
                                    <td>&nbsp;</td>
                                    <td height="30" class="wd_text">
									   <?php echo _('Date Registration');?><span class="red_star">*</span>
									</td>
                                    <td>
									   <input type="text" value="" size="10" class="textbox" id="registration_date" name="registration_date">
									   <img border="0" src="<?php echo base_url('');?>assets/mcp/images/cal.jpeg" width="30" height="30" name="date_picker" id="date_picker" style="vertical-align:bottom">
			
										<script type="text/javascript">	
											var cal = Calendar.setup({
											  onSelect: function(cal) { cal.hide() }
											});
											cal.manageFields("date_picker", "registration_date", "%Y-%m-%d");
										</script>
								
                                    </td>
                                    <td>&nbsp;</td>
                                 </tr>
                                 <tr>
                                    <td>&nbsp;</td>
                                    <td height="30" class="wd_text"><?php echo _('Expiry Date (Every 1 Year)');?></td>
                                    <td><input type="text" readonly="readonly" value="" size="10" class="textbox" id="expiry_date" name="expiry_date">
                                    </td>
                                    <td>&nbsp;</td>
                                 </tr>
                                 <tr>
                                    <td>&nbsp;</td>
                                    <td height="30" class="wd_text"><?php echo _('Earnings/Year');?><span class="red_star"> *</span></td>
                                    <td>
									    <input type="text" size="10" class="textbox required" id="earnings_year" name="earnings_year" value="">
                                        &nbsp;&nbsp;<b>&euro;</b>
								    </td>
                                    <td>&nbsp;</td>
                                 </tr>
                                 <tr>
                                    <td>&nbsp;</td>
                                    <td height="30" class="wd_text"><?php echo _('FDD-TV');?></td>
                                    <td>
									    <input type="checkbox" size="10" class="textbox" id="fdd_tv" name="fdd_tv" value="1">
								    </td>
                                    <td>&nbsp;</td>
                                 </tr>
                                  <tr>
                                    <td>&nbsp;</td>
                                    <td height="30" class="wd_text"><?php echo _('Hide “Downloads” in Settings');?></td>
                                    <td>
                                    <input type="checkbox" size="10" class="textbox" id="hide_download" name="hide_download" value="1">
                                    </td>
                                    <td>&nbsp;</td>
                                 </tr>

                                 <tr>
                                    <td>&nbsp;</td>
                                    <td height="30" class="wd_text"><?php echo _('Enable products download in Settings');?></td>
                                    <td>
                                    	<input type="checkbox" class="textbox" id="hide_product_download" name="hide_product_download" value="1">
                                    </td>
                                    <td>&nbsp;</td>
                                 </tr>
                                 

                                 <!-- <tr>
                                   <td>&nbsp;</td>
								   <td height="30" class="wd_text"><?php echo _('Show Menucard Maker');?></td>
                                   <td>
                                   	<input type="checkbox" class="textbox" id="show_menukartt_maker" name="show_menukartt_maker" value="1">
                                   </td>
                     			   <td>&nbsp;</td>
								</tr> -->
                                 
                                <tr>
                                   <td>&nbsp;</td>
                                    <td height="30" style="padding:20px" class="wd_text">
									  <input type="checkbox" onChange="showSubAdmin(this);" value="super" id="role" name="role">
									</td>
                                    <td style="padding-top:20px;padding-bottom:20px">
									   <strong><?php echo _('Activate As \'SUPER ADMIN\'');?></strong>
									</td>
                                    <td>&nbsp;</td>
                                 </tr>
                                 <tr>
                                    <td valign="top" align="center" colspan="5">
									    <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                        <tbody>
                                        <tr>
                                            <td width="270">&nbsp;</td>
                                            <td valign="middle" height="60">
											    <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                                <tbody>
                                                <tr>
                                                    <td width="31%" align="right">&nbsp;</td>
                                                    <td style="padding-left:20px">
													   <input type="submit" value="<?php echo _('ADD COMPANY'); ?>" class="btnWhiteBack" id="btn_add_update" name="btn_add_update"  />
													</td>
                                                </tr>
                                              
                                                <tr>
                                                    <td width="33%" height="30" class="wd_text"><?php echo _('Company');?></td>
                                                    <td style="padding-left:20px">
													   
													  <select class="textbox" type="select" id="parent_id" name="parent_id">
														
														<option value="0">-- <?php echo _('Select Company'); ?> --</option>
														
														<?php if(!empty($companies)) { foreach($companies as $c) { ?>
														<option value="<?php echo $c->id; ?>" >
														   <?php echo $c->company_name; ?>
														</option>
														<?php } } ?>
														
													  </select>
													  
												   </td>
                                               </tr>
                                               </tbody>
                                               </table>
										  </td>
                                          <td width="20%">&nbsp;</td>
                                      </tr>
                                      </tbody>
                                      </table>
								</td>
                           </tr>
                           </tbody>
                           </table>
					  </td>
				 </tr>
				 </tbody>
				 </table>
				 
				 <br />
				 
	             </form>
				 
				 <script type="text/javascript">
					var frmValidator = new Validator("frm_companies_add_update");
					frmValidator.EnableMsgsTogether();
					frmValidator.addValidation("company_name","req","<?php echo _('Please enter the Company Name'); ?>");
					frmValidator.addValidation("type_id","req","<?php echo _('Please select the Company Type'); ?>");
					frmValidator.addValidation("type_id","dontselect=-1","<?php echo _('Please enter the Company Type'); ?>");	
					frmValidator.addValidation("first_name","req","<?php echo _('Please enter the First Name'); ?>");	
					frmValidator.addValidation("last_name","req","<?php echo _('Please enter the Last Name'); ?>");	
					frmValidator.addValidation("email","req","<?php echo _('Please enter the Email'); ?>");	
					frmValidator.addValidation("email","email","<?php echo _('Please enter a valid Email Address'); ?>");	
					frmValidator.addValidation("phone","req","<?php echo _('Please enter the Phone Number'); ?>");
					frmValidator.addValidation("phone","num","<?php echo _('Please enter the Phone Number in Digits'); ?>");	
					frmValidator.addValidation("address","req","<?php echo _('Please enter the Address'); ?>");	
					frmValidator.addValidation("zipcode","req","<?php echo _('Please enter the Zipcode'); ?>");	
					frmValidator.addValidation("city","req","<?php echo _('Please enter the City'); ?>");	
					frmValidator.addValidation("country_id","dontselect=-1","<?php echo _('Please Select Country'); ?>");	
					frmValidator.addValidation("username","req","<?php echo _('Please enter Username'); ?>");	
					frmValidator.addValidation("password","req","<?php echo _('Please enter Password'); ?>");
					frmValidator.addValidation("registration_date","req","<?php echo _('Please enter Date of Registration'); ?>");
					
					
					function validate_data(){
						var validate = true;
						var MSG = Array();
						field = "";
						var msg = "";	
						
						MSG[1] = "";
						if(document.getElementById("companyid") != null ){
							if(document.getElementById("companyid").value == '-1'){
								MSG[1] = "- <?php echo _('Please select the Company'); ?> \n";
								if(field == ""){
									field = "companyid";
									validate = false;
								}
							}
						}else{
							MSG[1] = "";
						}
						
						MSG[2] = "";
						if(document.getElementById("webdesigner_id").value == '-1'){
							MSG[2] = "- <?php echo _('Please select the Webdesigner'); ?> \n";
							if(field == ""){
								field = "webdesigner_id";
								validate = false;
							}
						}else{
							MSG[2] = "";
						}
						
						MSG[3] = ""	;	
						if(document.getElementById("photographer").value == ""){
							MSG[3] = "- <?php echo _('Please enter the Photographer Name'); ?> \n";
							if(field == ""){
								field = "photographer";
								validate = false;
							}
						}else{
							MSG[3] = "";
						}
						
						for(i=1; i<=3; i++){
							if(MSG[i] != ""){
								msg += MSG[i];
							}
						}
						
						if(msg != ""){
							alert(msg);
						}
						
						if(msg != "" && field !=""){
							document.getElementById(field).focus();
							return false;
						}
						
						if(validate == true){
							document.frm2_companies_add_edit.submit();
						}
					}
				</script>

				 
			 </td>
             </tr>
             
			 <?php /*?><tr>
                 <td style="padding:5px 0px 5px 0px" colspan="5">
					 
				   <form action="index.php?view=companies_add_edit" enctype="multipart/form-data" method="post" id="frm2_companies_add_edit" name="frm2_companies_add_edit">
                   <input type="hidden" value="companies_add_edit" name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW">
                       
				   <table width="100%" cellspacing="0" cellpadding="0" border="0">
                   <tbody>
                   <tr>
                       <td style="padding:0px 70px 0px 70px" colspan="4"><hr></td>
                   </tr>
                   <tr>
                       <td width="270">&nbsp;</td>
                       <td>
						   <table width="100%" cellspacing="0" cellpadding="0" border="0">
						   <tbody>
						   <tr>
							  <td width="33%" height="30" class="wd_text"><?php echo _('Webdesigner');?></td>
							  <td style="padding-left:20px">
									<select class="textbox" type="select" id="webdesigner_id" name="webdesigner_id">
									  <option value="-1">-- <?php echo _('Select Webdesigner'); ?> --</option>
									</select>
							  </td>
						   </tr>
						   <tr>
								<td width="33%" height="30" class="wd_text"><?php echo _('Photographer');?></td>
								<td style="padding-left:20px">
									<input type="text" size="20" class="textbox" id="photographer" name="photographer">
								</td>
						   </tr>
						   <tr>
								 <td width="33%" height="40">&nbsp;</td>
								 <td style="padding-left:20px">
									 <input type="button" onClick="return validate_data();" value="ADD " class="btnWhiteBack" id="btn_add_update2" name="btn_add_update2">
									 <input type="hidden" value="update_WDP" id="act" name="act">
									 <input type="hidden" value="" id="UID" name="UID">
						   		 </td>
						   </tr>
						   </tbody>
						   </table>
			            </td>
                        <td width="281">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding:0px 70px 0px 70px" colspan="4">&nbsp;</td>
                    </tr>
                    </tbody>
                    </table>
                    
					</form>
					
		         </td>
             </tr>
             
			 <tr>
                 <td style="padding:5px 0px 5px 0px" colspan="5">
				    <table width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
					<tr>
					  <td style="padding:0px 70px 0px 70px" colspan="4"><hr></td>
					</tr>
					<tr>
					  <td width="50">&nbsp;</td>
					  <td>
					      <table width="100%" cellspacing="0" cellpadding="0" border="0">
						  <tbody>
						  <tr>
							  <td height="30" align="center" colspan="2"><h3>Front End Iframe Links</h3></td>
						  </tr>
						  <tr>
							  <td width="20%" height="50" class="wd_text">Login Iframe :-</td>
							  <td><small>iframe name="iframe1" src="http://webilyst.com/projects/obs/front/index.php?view=loginframe&amp;code=" height="180"                                  width="100%" frameborder="0"</small></td>
						  </tr>
						  <tr>
							  <td height="50" class="wd_text">Main Iframe :-</td>
							  <td><small>iframe name="iframe2" src="http://webilyst.com/projects/obs/front/index.php?view=home&amp;code=" valign="top" height="100%" width="90%" hspace="10" vspace="10" align="middle" frameborder="0"</small></td>
					      </tr>
						  <tr>
							  <td height="50" class="wd_text">Shopping Cart Iframe :-</td>
							  <td><small>iframe name="iframe3" src="http://webilyst.com/projects/obs/front/index.php?view=addtocartframe&amp;code="                                     height="180" width="100%" frameborder="0"</small></td>
						  </tr>
						  </tbody>
						  </table>
					  </td>
					  <td width="50">&nbsp;</td>
				   </tr>
                   <tr>
                      <td style="padding:0px 70px 0px 70px" colspan="4">&nbsp;</td>
                   </tr>
                   </tbody>
                   </table>
			   </td>
           </tr><?php */?>
           </tbody>
           </table>
       </td>
  </tr>
  <tr>
     <td height="10">&nbsp;</td>
  </tr>
  </tbody>
  </table>
</td>
</tr>
</tbody>
</table>
<!-- end of main body -->
</div>
<div id="push"></div>
</div>
<script type="text/javascript">
jQuery(document).ready(function($)
{
  $(document).on('change','#type_id_cat', function(){
    var html = '<option value="-1">--Select Company Type--</option>';
    jQuery('#type_id').html('');
    jQuery.ajax({
      type : "POST",
      url : base_url+"mcp/companies/get_types",
      dataType :'json',
      data : { 'grp_id' : jQuery(this).val()},
      success : function(response){
        if(response.length > 0){
          jQuery.each(response, function(index, value) {
            html += '<option value="' + value.id + '">' + value.company_type_name + '</option>';
          });
        }
        jQuery('#type_id').append(html);
      }
    })
  });   

  $(document).on("click", "#mark_high_priority_client", function() {
    if (this.getAttribute('checked')) { // check the presence of checked attr
      jQuery(this).removeAttr('checked'); // if present remove that
      jQuery(this).val('0');
    } else {
      jQuery(this).attr('checked', true); // if not then make checked
      jQuery(this).val('1');
    }
  });
} );
</script>
