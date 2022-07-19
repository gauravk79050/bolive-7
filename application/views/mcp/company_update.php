<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Online bestellen met OBS - bestelsysteem voor Bakkers | Broodjeszaken | Traiteurs | ... </title>
<meta name="keywords" content="OBS, online bestellen, bestellingssysteem, bestelsysteem, bakker, bakkerij, brood en banket, broodjeszaak, broodjeszaken, online, beheren, beheersysteem, goedkoop"  />
<meta name="description" content="Online bestellen met OBS (online bestelsysteem) voor bakkers / broodjeszaken /Traiteurs / Groente- en fruitwinkels / etc... Wees de eerste in uw buurt, verwen uw klanten!"  />
<link href="<?php echo base_url(); ?>assets/mcp/css/style.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/mcp/css/SpryMenuBarHorizontal.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/mcp/new_css/date_pic/jscal2.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/mcp/new_css/date_pic/border-radius.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/mcp/new_css/date_pic/steel/steel.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/mcp/new_css/colorbox.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/js/SpryMenuBar.js"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/js/general_functions.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/js/validator.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/new_js/jscal2.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/new_js/lang/en.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/new_js/jquery-1.4.2.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/new_js/jquery.colorbox.js"></script>
</head>
<body>
<div id="content">
  <style type="text/css">
<!--
.style2 {color: #667297}
.style3 {color: #667297; font-size:14px}
.style4 {
	color:#CCC;
	font-size:12px;
	font-weight: normal;
	margin-right: 50px;
	float: right;
}
-->

</style>
  <div id="header">

    <div style="width:90%; height:30px; padding-left:5px">

      <h1><span class="style2"><?php echo _('OBSDEV')?></span>&nbsp;&nbsp;<span class="style3"><?php echo _('MASTER CP')?></span><span class="style4"><?php echo _('Server Date/Time &nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp; Monday , August 8, 2011, 6:15 am')?></span></h1>

    </div>

    <div style="width:100%; height:20px">

      <div style="width:30%; height:20px; float:left; padding-left:7px">

        <h3><?php echo _('Master Administrator Panel')?></h3>

      </div>
 <?php
		if(isset($hide_header_menu) && $hide_header_menu == true)
		{ 
		}
		else
		{
	  ?>
      <div style="width:30%; height:20px; float:right; text-align:right; padding-right:5px">
	  

        <h3><?php echo _('Welcome Admin')?> </h3>

      </div>

    </div>

    <div id="menu" style="width:100%; height:25px; background-color:#003366">
      
        <ul class="MenuBarHorizontal" id="MenuBar1">

          <li><?php echo anchor(base_url()."mcp/dashboard",_('HOME'),array('class'=>"MenuBarItemSubmenu"));?></li>

          <li><?php echo anchor(base_url()."mcp/companies",_('COMPANIES'),array('class'=>"MenuBarItemSubmenu")); ?></li>

          <li><?php echo anchor("javascript:void(0)",_('SETTINGS'),array('class'=>"MenuBarItemSubmenu"))?>

            <ul>

              <li><?php echo anchor(base_url()."mcp/country",_('COUNTRIES'))?></li>

              <li><?php echo anchor(base_url()."mcp/languages",_('LANGUAGES'))?></li>

              <li><?php echo anchor(base_url()."mcp/email_message",_('MAIL MESSAGES'))?></li>

              <li><?php echo anchor(base_url()."mcp/web_designers",_('WEB DESIGNERS'))?></li>

              <li><?php  echo anchor(base_url()."mcp/company_type",_('TYPE'))?></li>

              <li><?php  echo anchor(base_url()."mcp/ads",_('ADS'))?></li>

            </ul>

          </li>

          <li><?php echo anchor(base_url()."mcp/package" ,_('PACKAGE MANAGER'),array('class'=>"MenuBarItemSubmenu"))?></li>

          <li><?php echo anchor(prep_url('javascript:void(0)'),_('PHOTO SCRIPT'),array('class'=>"MenuBarItemSubmenu"))?>

            <ul>

              <li><?php echo anchor('',_('COMPANY TYPE'))?></li>

              <li><?php echo anchor('', _('CATEGORIES'))?></li>

              <li><?php echo anchor('',_('SUBCATEGORIES'))?></li>

              <li><?php echo anchor('',_('PRODUCT IMAGES'))?></li>

            </ul>

          </li>

          <li><?php echo anchor(base_url()."mcp/profile",_('ADMIN MANAGER'), array('class'=>"MenuBarItemSubmenu"))?></li>

          <li><?php echo anchor(base_url('')."mcp/mcplogin/logout",_('LOGOUT'),array('class'=>"MenuBarItemSubmenu"))?></li>

        </ul>

        <div class="clear_all"></div>   
    <?php 

		} 
		
	?>
    </div>

  </div>  <div style="width:100%">
  <script language="javascript" type="text/javascript">
  function confirm_delete(){
  	var did = '100';
	var answer = confirm("Sure, You want to DELETE this company and all its Records ?");
	if(answer){
		window.location = 'index.php?view=companies_add_edit&act=delete&DID='+did;
  	}
  }
  
  function showSubAdmin(chkbox){
  	var value = chkbox.checked;
	if(value){
		document.getElementById("superadmin").style.display = "block"; 
	}else{
		document.getElementById("superadmin").style.display = "none"; 
	} 
  }
  
  $(document).ready(function(){
	var $jq=jQuery.noConflict();
	//Examples of how to assign the ColorBox event to elements
	$jq(".sub").colorbox({
		width:'730px',
		height:'380px',
		scrolling:false
		});
	});
  </script>
    <!-- start of main body -->
    <table width="100%" border="0" cellspacing="0" cellpadding="0">

      <tr>
        <td align="center" valign="top"><table width="98%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td align="center" valign="top" style="border:#8F8F8F 1px solid"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td align="center" style="padding:15px 0px 5px 0px"><table class="page_caption" style="background:url(images/bg.jpg) left top repeat-x;" width="98%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td width="94%" align="left"><h3>
                              Edit                               Company</h3></td>

                          <td width="3%" align="right"></td>
                          <td width="3%" align="left"><div class="icon_button"> <img src="images/undo.jpg" alt="Go Back" title="Go Back"  width="16" height="16" border="0" onClick="javascript:history.back();" style="cursor:pointer" /> </div></td>
                        </tr>
                      </table></td>
                  </tr>
                  <tr>
                    <td width="100%" align="center" valign="top"><table width="98%" border="0" cellspacing="0" cellpadding="0" style="border:1px solid #003366; text-align:left">
                        <tr>

                          <td height="20" colspan="5" align="left" bgcolor="#003366" class="whiteSmallBold" style="padding-left:10px;">Company Information</td>
                        </tr>
                        <tr>
                          <td height="10" colspan="5">&nbsp;</td>
                        </tr>
						<tr>
                          <td height="10" colspan="5" align="center"><span id="dup_msg" style="color:#FF0000"></span></td>
                        </tr>

                        <tr>
                          <td width="10%">&nbsp;
                            <form name="frm_companies_add_edit" id="frm_companies_add_edit" method="post" enctype="multipart/form-data" action="index.php?view=companies_add_edit">
<input type="hidden" name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW" value="companies_add_edit">
</td>
                          <td width="37%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td height="30" class="wd_text">ID&nbsp;&nbsp;</td>
                                <td>100</td>

                              </tr>
                              <tr>
                                <td height="30" class="wd_text">Company Name<span class="red_star">*</span></td>
                                <td><input name="company_name" id="company_name" type="text" class="textbox" size="30" value="testcp" /></td>
                              </tr>
                              <tr>
                                <td height="30" class="wd_text">Company Type<span class="red_star">*</span></td>

                                <td><select name="type_id" id="type_id" type="select" class="textbox" style="width:215px" >
<option  value="-1">-- Select Company Type --</option>
<option  selected value="1">Brood en Banket</option>
<option  value="2">Broodjeszaak</option>
<option  value="3">Groente-fruitwinkel</option>
<option  value="7">Traiteur</option>
<option  value="8">Beenhouwers</option>
<option  value="9">Vishandel</option></select></td>
                              </tr>

                              <tr>
                                <td height="30" class="wd_text">First Name<span class="red_star">*</span></td>
                                <td><input name="first_name" id="first_name" type="text" class="textbox" size="30" value="test" /></td>
                              </tr>
                              <tr>
                                <td height="30" class="wd_text">Last Name<span class="red_star">*</span></td>
                                <td><input name="last_name" id="last_name" type="text" class="textbox" size="30" value="cp" /></td>

                              </tr>
                              <tr>
                                <td height="30" class="wd_text">Email<span class="red_star">*</span></td>
                                <td><input name="email" id="email" type="text" class="textbox" size="30" onchange="check_email(this.value);" value="prernagupta1989@gmail.com" /></td>
                              </tr>
                              <tr>
                                <td height="30" class="wd_text">Phone<span class="red_star">*</span></td>

                                <td><input name="phone" id="phone" type="text" class="textbox" size="30" value="768768" /></td>
                              </tr>
                              <tr>
                                <td height="30" class="wd_text">Website<span class="red_star">*</span></td>
                                <td><input name="website" id="website" type="text" class="textbox" size="30" value="www.testcp.com" /></td>
                              </tr>
                            </table></td>
                          <td width="2%">&nbsp;</td>

                          <td width="37%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td height="30" class="wd_text">&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td height="30" class="wd_text">Address<span class="red_star">*</span></td>
                                <td><input name="address" id="address" type="text" class="textbox" size="30" value="test" /></td>

                              </tr>
                              <tr>
                                <td height="30" class="wd_text">Zipcode<span class="red_star">*</span></td>
                                <td><input name="zipcode" id="zipcode" type="text" class="textbox" size="30" value="989789" /></td>
                              </tr>
                              <tr>
                                <td height="30" class="wd_text">City<span class="red_star">*</span></td>

                                <td><input name="city" id="city" type="text" class="textbox" size="30" value="testcity" /></td>
                              </tr>
                              <tr>
                                <td height="30" class="wd_text">Country<span class="red_star">*</span></td>
                                <td><select name="country_id" id="country_id" type="select" class="textbox" style="width:215px" >
<option  value="-1">-- Select Country --</option>
<option  value="21">BELGIUM</option>
<option  selected value="150">NEDERLAND</option>

<option  value="241">india</option></select></td>
                              </tr>
                              <tr>
                                <td height="30" class="wd_text">&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td height="30" class="wd_text">Username<span class="red_star">*</span></td>

                                <td><input name="username" id="username" type="text" class="textbox" size="30" onchange="check_username(this.value);" value="testcp" /></td>
                              </tr>
                              <tr>
                                <td height="30" class="wd_text">Password<span class="red_star">*</span></td>
                                <td><input name="password" id="password" type="text" class="textbox" size="30" value="testcp" /></td>
                              </tr>
                            </table></td>
                          <td width="10%">&nbsp;</td>

                        </tr>
                        <tr>
                          <td valign="middle" colspan="5"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td colspan="4" height="10">&nbsp;</td>
                              </tr>
                              <tr>
                                <td width="270" height="80">&nbsp;</td>
                                <td class="wd_text">Admin Remarks&nbsp;&nbsp;</td>

                                <td><textarea name="admin_remarks" id="admin_remarks" type="textarea" class="textbox" rows="12" cols="80">testing testing</textarea></td>
                                <td width="281">&nbsp;</td>
                              </tr>
                              <tr>
                                <td colspan="4" height="10">&nbsp;</td>
                              </tr>
							  <tr>
                                <td width="270" height="30">&nbsp;</td>

                                <td class="wd_text">Package Preferred&nbsp;&nbsp;</td>
                                <td><select name="packages_id" id="packages_id" type="select" class="textbox" style="width:250px" >
<option  value="0">-- Select Package --</option>
<option  selected value="7">dasdas (3423.00 &euro; )</option>
<option  value="1">STARTER Pakket (245.00 &euro; )</option>
<option  value="2">STARTER PLUS Pakket (550.00 &euro; )</option></select></td>

                                <td width="281">&nbsp;</td>
                              </tr>
							  <!--<tr>
                                <td width="270" height="30">&nbsp;</td>
                                <td class="wd_text">Invoice Made&nbsp;&nbsp;</td>
                                <td> $this->htmlBuilder->buildTag("input", array("type"=>"checkbox", "value"=>"1"), "invoice_made") </ td>
                                <td width="281">&nbsp;</td>
                              </tr>
							  <tr>
                                <td width="270" height="30">&nbsp;</td>
                                <td class="wd_text">Payment Received&nbsp;&nbsp;</td>
                                <td>/$this->htmlBuilder->buildTag("input", array("type"=>"checkbox", "value"=>"1"), "payment_received") </td>
                                <td width="281">&nbsp;</td>
                              </tr>-->
							  <tr>
                                <td width="270" height="30">&nbsp;</td>
                                <td class="wd_text">Email Ads&nbsp;&nbsp;</td>
                                <td><select name="email_ads" id="email_ads" type="select" >
<option  value="0">No</option>

<option  selected value="1">Yes</option></select></ td>
                                <td width="281">&nbsp;</td>
                              </tr>
							  <tr>
                                <td width="270" height="30">&nbsp;</td>
                                <td class="wd_text">Frontend Footer Text</td>
                                <td><select name="footer_text" id="footer_text" type="select" >
<option  value="0">No</option>

<option  selected value="1">Yes</option></select></td>
                                <td width="281">&nbsp;</td>
                              </tr>
							  <tr>
                                <td colspan="4" height="10">&nbsp;</td>
                              </tr>
							  <tr>
                                <td>&nbsp;</td>
                                <td height="30" class="wd_text">2 year Subscription</td>

                                <td><input name="5year_subscription" id="5year_subscription" type="checkbox" class="textbox" value="1" /></td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td>&nbsp;</td>
                                <td height="30" class="wd_text">Date Registration<span class="red_star">*</span></td>
                                <td><input name="registration_date" id="registration_date" type="text" class="textbox" size="10" value="20-10-2011" />									 <img border="0" src="<?php echo base_url(); ?>assets/mcp/images/cal.jpeg" width="30" height="30" name="date_picker" id="date_picker" style="vertical-align:bottom">

									 <script type="text/javascript">	
										var cal = Calendar.setup({
										onSelect: function(cal) { cal.hide() }
										});
										cal.manageFields("date_picker", "registration_date", "%d-%m-%Y");
									</script></td>
                                <td>&nbsp;</td>
                              </tr>
							  <tr>
                                <td>&nbsp;</td>
                                <td height="30" class="wd_text">Expiry Date (Every 1 Year)<!--<span class="red_star">*</span>--></td>
                                <td><input name="expiry_date" id="expiry_date" type="text" class="textbox" size="10" value="20-10-2012" readonly="readonly" />									 <!--<img border="0" src="images/cal.jpeg" width="30" height="30" name="date_picker1" id="date_picker1" style="vertical-align:bottom">
									 <script type="text/javascript">	
										var cal = Calendar.setup({
										onSelect: function(cal) { cal.hide() }
										});
										cal.manageFields("date_picker1", "expiry_date", "%d-%m-%Y");
									</script>--></td>

                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td>&nbsp;</td>
                                <td height="30" class="wd_text">Earnings/Year<span class="red_star">*</span></td>
                                <td><input name="earnings_year" id="earnings_year" type="text" class="textbox" size="10" value="800" />                                  &nbsp;&nbsp;<b>&euro;</b></td>
                                <td>&nbsp;</td>

                              </tr>
                              
							  <tr>
                                <td>&nbsp;</td>
                                <td height="30" class="wd_text" style="padding:20px"><input name="role" id="role" type="checkbox" value="super" onchange="showSubAdmin(this);" /></td>
                                <td style="padding-top:20px;padding-bottom:20px"><strong>Activate As 'SUPER ADMIN'</strong></td>
                                <td>&nbsp;</td>
                              </tr>
							                                <tr>

                                <td colspan="5" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                      <td width="270">&nbsp;</td>
									                                        <td height="60" valign="middle"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                          <tr>
                                            <td width="31%" align="right" ><input name="btn_add_update" id="btn_add_update" type="submit" class="btnWhiteBack" value="UPDATE " /><input name="act" id="act" type="hidden" value="add_edit" /><input name="ID" id="ID" type="hidden" value="100" /></td>
                                            <td style="padding-left:20px"><input name="delete" id="delete" type="button" class="btnWhiteBack" value="DELETE THIS COMPANY AND ALL SETTINGS FROM DB" onclick="confirm_delete();" /></td>
                                          </tr>
                                        </table></td>

									  	
                                      <td width="20%">
                                        </form></td>
                                    </tr>
                                  </table></td>
                              </tr>
                            </table></td>
                        </tr>
                        <tr>
                          <td colspan="5" style="padding:5px 0px 5px 0px"><form name="frm2_companies_add_edit" id="frm2_companies_add_edit" method="post" enctype="multipart/form-data" action="index.php?view=companies_add_edit">

<input type="hidden" name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW" value="companies_add_edit">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tr>
							  	<td colspan="4" style="padding:0px 70px 0px 70px"><hr></td>
							  </tr>
							  <tr>
                                <td width="270" >&nbsp;</td>
                                <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                     									<tr>

                                      <td height="30" width="33%" class="wd_text">Webdesigner</td>
                                      <td style="padding-left:20px"><select name="webdesigner_id" id="webdesigner_id" type="select" class="textbox" >
<option  value="-1">-- Select Webdesigner --</option></select></td>
                                    </tr>
                                    <tr>
                                      <td height="30" width="33%" class="wd_text">Photographer</td>
                                      <td style="padding-left:20px"><input name="photographer" id="photographer" type="text" class="textbox" size="20" value="" /></td>
                                    </tr>

                                    <tr>
                                      <td height="40" width="33%">&nbsp;</td>
                                      <td style="padding-left:20px"><input name="btn_add_update2" id="btn_add_update2" type="button" class="btnWhiteBack" value="UPDATE " onclick="return validate_data();" /><input name="act" id="act" type="hidden" value="update_WDP" /><input name="UID" id="UID" type="hidden" value="100" /></td>
                                    </tr>
                                  </table></td>
                                <td width="281">&nbsp;</td>
                              </tr>
							  <tr>
							  	<td colspan="4" style="padding:0px 70px 0px 70px">&nbsp;</td>

							  </tr>
                            </table>
                            </form></td>
                        </tr>
						<tr>
                          <td colspan="5" style="padding:5px 0px 5px 0px"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tr>
							  	<td colspan="4" style="padding:0px 70px 0px 70px"><hr></td>
							  </tr>

							  <tr>
                                <td width="50" >&nbsp;</td>
                                <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                      <td height="30" colspan="2" align="center"><h3>Front End Iframe Links</h3></td>
                                    </tr>
									<tr>
                                      <td height="50" class="wd_text" width="20%">Login Iframe :-</td>

									  <td><small>iframe name="iframe1" src="http://fooddesk.net/obs/front/index.php?view=loginframe&code=c76329" height="180" width="100%" frameborder="0"</small></td>
                                    </tr>
									<tr>
                                      <td height="50" class="wd_text">Main Iframe :-</td>
									  <td><small>iframe name="iframe2" src="http://fooddesk.net/obs/front/index.php?view=home&code=c76329" valign="top" height="100%" width="90%" hspace="10" vspace="10" align="middle" frameborder="0"</small></td>
                                    </tr>

                                    <tr>
                                      <td height="50" class="wd_text">Shopping Cart Iframe :-</td>
									  <td><small>iframe name="iframe3" src="http://fooddesk.net/obs/front/index.php?view=addtocartframe&code=c76329" height="180" width="100%" frameborder="0"</small></td>
                                    </tr>
                                  </table></td>
                                <td width="50">&nbsp;</td>
                              </tr>

							  <tr>
							  	<td colspan="4" style="padding:0px 70px 0px 70px">&nbsp;</td>
							  </tr>
                            </table></td>
                        </tr>
						 						<tr>
                          <td colspan="5" style="padding:5px 0px 5px 0px"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tr>
							  	<td colspan="4" style="padding:0px 70px 0px 70px"><hr></td>

							  </tr>
							  <tr>
                                <td width="50" >&nbsp;</td>
                                <td style="padding-left:50px"><table width="60%" border="0" cellspacing="0" cellpadding="0" style="text-align:left">
                                    <tr>
                                      <td height="50" colspan="3"><h3>Manage Front End LayOut (Css)</h3></td>
                                    </tr>
									<tr>

                                      <td height="50" class="wd_text" width="20%" style="text-align:left">Download Css </td>
									  <td width="25%"><form name="frm_download" id="frm_download" method="post" enctype="multipart/form-data" action="index.php?view=companies_add_edit">
<input type="hidden" name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW" value="companies_add_edit">
                					 	  <input name="img_save" id="img_save" src="images/downloadcss1.jpg" type="image" title="download Css" onmouseover="document.getElementById('img_save').src='images/downloadcss2.jpg'" onmouseout="document.getElementById('img_save').src='images/downloadcss1.jpg'" border="0" height="25px" /><input name="act" id="act" type="hidden" value="get_css_file" /><input name="CID" id="CID" type="hidden" value="100" /></form></td>
									   <td>&nbsp;</td> 
									</tr>
									<tr>
                                      <td height="50" class="wd_text" style="text-align:left">Upload Css </td>
									  <td width="25%">

									   <form name="frm_upload" id="frm_upload" method="post" enctype="multipart/form-data" action="index.php?view=companies_add_edit">
<input type="hidden" name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW" value="companies_add_edit">
<input name="upload_css" id="upload_css" type="file" />									  </td>
									  <td style="vertical-align:middle">&nbsp;&nbsp;<input name="img_upload" id="img_upload" src="images/uploadcss.jpg" type="image" title="Upload Css" border="0" height="25px" /><input name="act" id="act" type="hidden" value="upload_css_file" /><input name="CID" id="CID" type="hidden" value="100" /></form></td>
                                    </tr>
                                  </table></td>
                                <td width="50">&nbsp;</td>
                              </tr>
							  <tr>

							  	<td colspan="4" style="padding:0px 70px 0px 70px">&nbsp;</td>
							  </tr>
                            </table></td>
                        </tr>
						 
                         <tr>
                          <td colspan="5" style="padding:5px 0px 5px 0px"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tr>
							  	<td colspan="4" style="padding:0px 70px 0px 70px"><hr></td>
							  </tr>

							  <tr>
                                <td width="50" >&nbsp;</td>
                                <td style="padding-left:50px"><table width="60%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                      <td height="50" colspan="3" style="text-align:left"><h3>Import Data (CSV)</h3></td>
                                    </tr>									
									<tr>
                                      <td height="50" class="wd_text" style="text-align:left" width="20%">CSV (Product)</td>

									  <td width="25%">
									   <form name="frm_upload_csv" id="frm_upload_csv" method="post" enctype="multipart/form-data" action="index.php?view=companies_add_edit">
<input type="hidden" name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW" value="companies_add_edit">
<input name="upload_products_csv" id="upload_products_csv" type="file" />									  </td>
									  <td style="vertical-align:middle">&nbsp;&nbsp;<input name="img_upload" id="img_upload" src="images/uploadcss.jpg" type="image" title="Upload Csv" border="0" height="25px" /><input name="act" id="act" type="hidden" value="upload_csv" /><input name="CID" id="CID" type="hidden" value="100" /></form></td>
                                    </tr>
                                  </table></td>
                                <td width="50">&nbsp;</td>
                              </tr>

							  <tr>
							  	<td colspan="4" style="padding:0px 70px 0px 70px">&nbsp;</td>
							  </tr>
                            </table></td>
                        </tr>
                                              </table></td>
                  </tr>
                  <tr>
                    <td height="10">&nbsp;</td>

                  </tr>
                </table>
                                <script language="javascript" type="text/javascript">
					var frmValidator = new Validator("frm_companies_add_edit");
					frmValidator.EnableMsgsTogether();
					frmValidator.addValidation("company_name","req","Please enter the Company Name");
					frmValidator.addValidation("type_id","dontselect=-1","Please enter the Company Type");	
					frmValidator.addValidation("first_name","req","Please enter the First Name");	
					frmValidator.addValidation("last_name","req","Please enter the Last Name");	
					frmValidator.addValidation("email","req","Please enter the Email");	
					frmValidator.addValidation("email","email","Please enter a valid Email Address");	
					frmValidator.addValidation("phone","req","Please enter the Phone Number");
					frmValidator.addValidation("phone","num","Please enter the Phone Number in Digits");	
					frmValidator.addValidation("website","req","Please enter the Website");	
					frmValidator.addValidation("address","req","Please enter the Address");	
					frmValidator.addValidation("zipcode","req","Please enter the Zipcode");	
					frmValidator.addValidation("city","req","Please enter the City");	
					frmValidator.addValidation("country_id","dontselect=-1","Please Select Country");	
					frmValidator.addValidation("username","req","Please enter Username");	
					frmValidator.addValidation("password","req","Please enter Password");
					//frmValidator.addValidation("expiry_date","req","Please enter Date of Expiry");	
					frmValidator.addValidation("registration_date","req","Please enter Date of Registration");
					frmValidator.addValidation("earnings_year","req","Please enter Earnings/Year");	
					
					function validate_data(){
						var validate = true;
						var MSG = Array();
						field = "";
						var msg = "";	
						
						MSG[1] = "";
						if(document.getElementById("companyid") != null ){
							if(document.getElementById("companyid").value == '-1'){
								MSG[1] = "- Please select the Company \n";
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
							MSG[2] = "- Please select the Webdesigner \n";
							if(field == ""){
								field = "webdesigner_id";
								validate = false;
							}
						}else{
							MSG[2] = "";
						}
						
						MSG[3] = ""	;	
						if(document.getElementById("photographer").value == ""){
							MSG[3] = "- Please enter the Photographer Name \n";
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
          </table></td>
      </tr>
      <tr>

        <td height="10">&nbsp;</td>
      </tr>
    </table>
    <!-- end of main body -->
  </div>
  <div id="push"></div>
</div>
<div id="footer">
  <div style="width: 100%; height: 2px; background-color:#006600"><img src="images/spacer.gif" height="1" /></div>
  <div style="width: 90%; height: 15px; text-align:right; float:right; padding-right:2px" class="smallfont">Powered By: OBS BEstelsysteem - SiteMatic BVBA </div>

</div></body>
</html> 