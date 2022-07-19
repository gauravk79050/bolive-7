<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<title><?php echo _('Online bestellen met OBS - bestelsysteem voor Bakkers | Broodjeszaken | Traiteurs | ... '); ?></title>

<meta content="OBS, online bestellen, bestellingssysteem, bestelsysteem, bakker, bakkerij, brood en banket, broodjeszaak, broodjeszaken, online, beheren, beheersysteem, goedkoop" name="keywords">

<meta content="Online bestellen met OBS (online bestelsysteem) voor bakkers / broodjeszaken /Traiteurs / Groente- en fruitwinkels / etc... Wees de eerste in uw buurt, verwen uw klanten!" name="description">

<meta content="8xMT5ro3-13nEZPiQ5gvi_CwjTc7kQeENeZlKT05aiE" name="google-site-verification">

<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/mcp/css/SpryMenuBarHorizontal.css">
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/mcp/css/style.css">
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/mcp/new_css/date_pic/jscal2.css">
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/mcp/new_css/date_pic/border-radius.css">
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/mcp/new_css/date_pic/steel/steel.css">

<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/mcp/css/jquery.ui.all.css">
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/mcp/css/demos.css">
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/mcp/css/jquery.ui.base.css">
<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/mcp/css/jquery.ui.theme.css">

<script src="<?php echo base_url();?>assets/mcp/new_js/jquery-1.4.2.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/mcp/js/jquery.ui.core.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/mcp/js/jquery.ui.datepicker.js" type="text/javascript"></script>

<script src="<?php echo base_url();?>assets/mcp/js/SpryMenuBar.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/mcp/js/general_functions.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/mcp/js/validator.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/mcp/new_js/jscal2.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/mcp/new_js/lang/en.js" type="text/javascript"></script>
</head>

<body>

<?php if(!empty($company)) { $company = $company[0]; } ?>

<form action="<?php echo base_url().'mcp/dashboard/renewal_info/'.$company->id; ?>" enctype="multipart/form-data" method="post" id="frm_renewal_info" name="frm_renewal_info">
<input type="hidden" value="renewal_info" name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW">
<table width="100%" cellspacing="0" cellpadding="0" border="0">
  <tbody><tr>
    <td valign="top" align="center"><table width="100%" cellspacing="0" cellpadding="0" border="0">
        <tbody><tr>
          <td valign="top" align="center" style="border:#8F8F8F 1px solid"><table width="98%" cellspacing="0" cellpadding="0" border="0">
              <tbody><tr>
                <td width="100%" valign="top" align="center"><table width="98%" cellspacing="0" cellpadding="0" border="0" style="border:1px solid #003366">
                    <tbody><tr>
                      <td height="20" bgcolor="#003366" align="left" style="padding-left:10px;" class="whiteSmallBold">
					    <?php echo _('Invoice Information'); ?>
					  </td>
                    </tr>
                    <tr>
                      <td height="60">&nbsp;</td>
                    </tr>
                    <tr>
                      <td><table width="100%" cellspacing="0" cellpadding="0" border="0">
                          <tbody><tr>
                            <td width="50%" height="30" class="wd_text"><?php echo _('Last Expiry Date'); ?></td>
                            <td><?php echo date('d-m-Y',strtotime($company->expiry_date)); ?></td>
                          </tr>
                          <tr>									
						  
						    <script language="javascript" type="text/javascript">
														  
							    /*jQuery.noConflict();
								jQuery(document).ready(function($){
									$( "#registration_date" ).datepicker({
										dateFormat: "yy-mm-dd",
										prevText: "",
										nextText: "",
										numberOfMonths: 1,
										showButtonPanel: true,
										changeMonth: true,
										changeYear: true,
										showOn: "button",
								        buttonImage: "<?php //echo base_url('');?>assets/mcp/images/cal.jpeg",
								        buttonImageOnly: true,
									 });
								});*/

							</script>
                            <td height="30" class="wd_text"><?php echo _('New Registration Date'); ?></td>
                            <td>
							   <!--<input type="text" size="10" class="textbox" id="registration_date" name="registration_date">-->
							   <input name="registration_date" id="registration_date" type="text" class="textbox" size="10" value="" />
							   <img border="0" src="<?php echo base_url('');?>assets/mcp/images/cal.jpeg" width="30" height="30" name="date_picker1" id="date_picker1" style="vertical-align:bottom">
	
								<script type="text/javascript">	
									var cal = Calendar.setup({
									  onSelect: function(cal) { cal.hide() }
									});
									cal.manageFields("date_picker1", "registration_date", "%Y-%m-%d");
								</script>
							
							</td>
                          </tr>
                          <tr>
						    <script language="javascript" type="text/javascript">
							    
								/*jQuery.noConflict();
								jQuery(document).ready(function($){
									$( "#expiry_date" ).datepicker({
									    dateFormat: "yy-mm-dd",
										prevText: "",
										nextText: "",
										numberOfMonths: 1,
										showButtonPanel: true,
										changeMonth: true,
										changeYear: true,
										showOn: "button",
								        buttonImage: "<?php //echo base_url('');?>assets/mcp/images/cal.jpeg",
								        buttonImageOnly: true,
									 });
								});*/
								
							</script>
                            <td height="30" class="wd_text"><?php echo _('New Expiry Date'); ?></td>
                            <td>
							   <!--<input type="text" size="10" class="textbox" id="expiry_date" name="expiry_date">-->
							   <input name="expiry_date" id="expiry_date" type="text" class="textbox" size="10" value="" />
							   <img border="0" src="<?php echo base_url('');?>assets/mcp/images/cal.jpeg" width="30" height="30" name="date_picker2" id="date_picker2" style="vertical-align:bottom">
	
								<script type="text/javascript">	
									var cal = Calendar.setup({
									  onSelect: function(cal) { cal.hide() }
									});
									cal.manageFields("date_picker2", "expiry_date", "%Y-%m-%d");
								</script>
								
						    </td>
                          </tr>
                          <tr>
                            <td height="30" class="wd_text"><?php echo _('Invoice Made'); ?></td>
                            <td><input type="checkbox" value="1" class="textbox" id="invoice_made" name="invoice_made"></td>
                          </tr>
                          <tr>
						    <script language="javascript" type="text/javascript">
							  
							    /*jQuery.noConflict();
								jQuery(document).ready(function($){
									$( "#date_of_invoice" ).datepicker({
									    dateFormat: "yy-mm-dd",
										prevText: "",
										nextText: "",
										numberOfMonths: 1,
										showButtonPanel: true,
										changeMonth: true,
										changeYear: true,
										showOn: "button",
								        buttonImage: "<?php //echo base_url('');?>assets/mcp/images/cal.jpeg",
								        buttonImageOnly: true,
									 });
								});*/
							  
							</script>
                            <td height="30" class="wd_text"><?php echo _('Date of Invoice'); ?></td>
                            <td>
							    <!--<input type="text" size="10" class="textbox" id="date_of_invoice" name="date_of_invoice">-->
							    <input name="date_of_invoice" id="date_of_invoice" type="text" class="textbox" size="10" value="" />
							    <img border="0" src="<?php echo base_url('');?>assets/mcp/images/cal.jpeg" width="30" height="30" name="date_picker3" id="date_picker3" style="vertical-align:bottom">
	
								<script type="text/javascript">	
									var cal = Calendar.setup({
									  onSelect: function(cal) { cal.hide() }
									});
									cal.manageFields("date_picker3", "date_of_invoice", "%Y-%m-%d");
								</script>
								
                            </td>
                          </tr>
                          <tr>
                            <td height="20" colspan="2">&nbsp;</td>
                          </tr>
                          <tr>
                            <td align="center" colspan="2">
							
							<input type="submit" value="<?php echo _('SAVE'); ?>" class="btnWhiteBack" id="update" name="update">
							<input type="hidden" value="update_dates" id="act" name="act">
							<input type="hidden" value="<?php echo $company->id; ?>" id="UID" name="UID">
							
							</td>
                          </tr>
						  <tr>
                            <td height="30" colspan="2">&nbsp;</td>
                          </tr>
                        </tbody></table></td>
                    </tr>
                  </tbody></table></td>
              </tr>
              <tr>
                <td height="10">&nbsp;</td>
              </tr>
            </tbody></table></td>
        </tr>
      </tbody></table></td>
  </tr>
</tbody></table>
</form>

<script type="text/javascript" language="javascript">
	var frmValidator = new Validator("frm_renewal_info");
	frmValidator.EnableMsgsTogether();
	frmValidator.addValidation("registration_date","req","<?php echo _('Please enter the Registration Date'); ?>");
	frmValidator.addValidation("expiry_date","req","<?php echo _('Please enter the Expiry Date'); ?>");	
	frmValidator.addValidation("invoice_made","shouldselchk","<?php echo _('Please select the Checkbox.'); ?>");	
	frmValidator.addValidation("date_of_invoice","req","<?php echo _('Please enter the Date of Invoice'); ?>");	
</script>

 </body></html>