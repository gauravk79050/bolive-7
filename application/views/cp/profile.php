<!-- MAIN -->
<style> 
.success{
background-color:#00FF99;
color:#333;
}
.error{
background-color:#FF6699;
}
</style>

<div id="main">
	<div id="main-header">
		<h2><?php echo _('Edit Profile')?></h2>
  		<span class="breadcrumb"><a href="<?php echo base_url()?>cp/cdashboard"><?php echo _('Home'); ?></a> &raquo; <?php echo _('Profile'); ?></span>
	</div>
	
	<?php $trail_date1= strtotime($trail_date);?>
	<?php /*?>
	<div style="background:#EBF7C5; padding:10px 8px; margin-bottom:20px; text-align:center; border:1px solid #ddd; margin-right: 245px; margin-left:0px;">
		<span style="display: inline-block; text-align: left !important;">
		<?php echo _("Link to Bestelonline live (what visitor are seeing)");?> - <b><a target="_blank" href="<?php echo $this->config->item("portal_url").$type_slug.'/'.$company_slug;?>" target="_blank"><?php echo $this->config->item("portal_url").$type_slug.'/'.$company_slug;></a></b>
		
		<?php if( ($ac_type_id=='2' || $ac_type_id=='3') && $general_settings[0]->shop_testdrive && $on_trial=='1' && $general_settings[0]->hide_bp_intro != '1' ){ ?>
			<br />
			<br />
	   		<?php echo _("Link to your test environment (to do settings)");?> - <b><a target="_blank" href="<?php echo $this->config->item("portal_url").$type_slug.'/'.$company_slug.'/testdrive';?>"> <?php echo $this->config->item("portal_url").$type_slug.'/'.$company_slug.'/testdrive';?> </a>    <?php echo _(' '.' (till date)').date('d-m-Y',$trail_date1);?></b>
	 	<?php } ?>
	 	
	 	<?php if( $ac_type_id=='3' && $on_trial=='1' ){?>
	 		<br />
	 		<br />
	 		<?php echo _("Link to simulation OBS-Module (in your website)");?> - <b><a target="_blank" href="http://www.onlinebestelsysteem.net/testdrive/bestelonline.php?cid=<?php echo $this->company->id?>"> <?php echo _('Click here');?> </a></b>
	 	<?php }?>
	 	</span> 		
	</div> 
	<?php */?>
	<?php $messages = $this->messages->get();?>
	<?php if(is_array($messages)):?>
	<?php foreach($messages as $key=>$val):?>
		<?php if($val != array()):?>
		<div id="succeed_order_update" class="<?php echo $key;?>"><?php echo $val[0];?></div>
		<?php endif;?>
    <?php endforeach;?>
	<?php endif;?>
	<div id="content">
      <div id="content-container">
	  
       <div class="box">
          <h3><?php echo _('Codes for your Website')?></h3>
          <div class="table">
            <?php if($api_codes):?>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
              <tbody>
	              <tr>
	                <td class="wd_text" height="50" width="20%"><?php echo _('API Id')?>:</td>
	                <td><?php echo $api_codes[0]->api_id;?></td>
	              </tr>
	              <tr>
	                <td class="wd_text" height="50"><?php echo _('API Secret')?>:</td>
	                <td><?php echo $api_codes[0]->api_secret;?></td>
	              </tr>
	              <tr>
	                <td class="wd_text" height="50"><?php echo _('Domain')?>:</td>
	                <td><form id="upd_domain_form" name="upd_domain_form" action="" method="POST"><input name="act" id="act" value="upd_domain" type="hidden"><input type="text" class="text medium" name="domain" id="domain" value="<?php echo $api_codes[0]->domain;?>" /> <input type="submit" name="upd" id="upd" value="<?php echo _("UPDATE");?>" /></form></td>
	              </tr>
				  <tr>
	                <td class="wd_text" height="50"><?php echo _('Download Files')?>:</td>
	                <td>
					    <p>
				      <a href="<?php echo base_url()?>download.php?f=new-online-bestellen.rar">Online-Bestellen.rar (<?php echo _('NEW'); ?>)</a>&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;<a href="http://www.fooddesk.net/obs/Docs/API-README.docx" target="_blank">Instructies</a></p>
				     </td>
	              </tr>
	              <tr>
	              	<td colspan=2>
	              		<form id="send_files_form" name="send_files_form" action="" method="POST">
	              			<input name="act" id="act" value="send_files" type="hidden">
	              			<span><?php echo _("Enter email");?> : </span><input type="text" class="text medium" name="email_to_send" id="email_to_send" value="" /> <input type="submit" name="send" id="send" value="<?php echo _("Send");?>" />
	              		</form>
	              	</td>
	              </tr>
              </tbody>
			</table>
			<?php else:?>
			<table border="0" cellpadding="0" cellspacing="0" width="100%"><tbody><tr>
				<td><?php echo _('Your Api Has Not Been Generated Yet ');?>!!</td>
			</tr></tbody></table>
			<?php endif;?>
          </div>
        </div>
		
        <div class="box">
          <h3>
            <?php echo _('Company')?></h3>
          <div class="table">
            <form name="frm_company_edit" id="frm_company_edit" method="post" enctype="multipart/form-data" action="<?php echo base_url()?>cp/cdashboard/profile">
            <input name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW" value="edit_profile" type="hidden">
            <table border="0">
              <tbody>
              <tr>
              	 <td class="textlabel" width="170"><span style="padding-left:20px">
                  <?php echo _('Company Id')?></span></td>
                <td><input id="company_id" size="30" class="text medium" readonly value="<?php echo $company_profile[0]->id?>" type="text"></td>
              </tr>
              <tr>
                <td class="textlabel" width="170"><span style="padding-left:20px">
                  <?php echo _('Company Name')?></span></td>
                <td><input name="company_name" id="company_name" size="30" class="text medium" value="<?php echo $company_profile[0]->company_name?>" type="text"></td>
              </tr>
              <tr>
                <td class="textlabel"><span style="padding-left:20px">
                  <?php echo _('Company type')?></span></td>
                <td><select name="type_id" id="type_id" type="select" class="select" style="margin:0px;padding: 5px;min-width:20%;">
					<?php if($company_type):?>
					  
					  <?php foreach($company_type as $com_type):?>
						 <option <?php if($company_profile[0]->type_id==$com_type->id):?>selected="selected"<?php endif;?> value=<?php echo $com_type->id;?>><?php echo $com_type->company_type_name;?></option>
					  <?php endforeach;?>
					
					<?php else:?>
					     <option value="-2"><?php echo _('No company type available')?></option>  
					<?php endif;?>
					</select>
				</td>
              </tr>
              <tr>
                <td class="textlabel"><span style="padding-left:20px">
                  <?php echo _('first name')?></span></td>
                <td><input name="first_name" id="first_name" class="text short" value="<?php echo $company_profile[0]->first_name?>" type="text"></td>
              </tr>
              <tr>
                <td class="textlabel"><span style="padding-left:20px">
                 <?php echo _('Surname')?></span></td>
                <td><input name="last_name" id="last_name" size="30" class="text short" value="<?php echo $company_profile[0]->last_name?>" type="text"></td>
              </tr>
              <tr>
                <td class="textlabel"><span style="padding-left:20px">
                  <?php echo _('Email')?></span></td>
                <td><input name="email" id="email" size="30" class="text medium" value="<?php echo $company_profile[0]->email?>" type="text"></td>
              </tr>
              <tr>
                <td class="textlabel"><span style="padding-left:20px">
                  <?php echo _('Phone Number')?></span></td>
                <td><input name="phone" id="phone" size="30" class="text short" value="<?php echo $company_profile[0]->phone?>" type="text"></td>
              </tr>
              <tr>
                <td class="textlabel"><span style="padding-left:20px">
                  <?php echo _('Address')?></span></td>
                <td><input name="address" id="address" class="text medium" value="<?php echo $company_profile[0]->address?>" type="text"></td>
              </tr>
              <tr>
                <td class="textlabel"><span style="padding-left:20px">
                  <?php echo _('postal code')?></span></td>
                <td><input name="zipcode" id="zipcode" class="text short" value="<?php echo $company_profile[0]->zipcode?>" type="text"></td>
              </tr>
              <tr>
                <td class="textlabel"><span style="padding-left:20px">
                  <?php echo _('City')?></span></td>
                <td><input name="city" id="city" class="text short" value="<?php echo $company_profile[0]->city?>" type="text"></td>
              </tr>
              <tr>
                <td class="textlabel"><span style="padding-left:20px"><?php echo _('Country')?></span></td>
                <td><select name="country_id" id="country_id" type="select" class="select" style="margin:0px;padding:5px;min-width:20%;">
					<?php if ($country):?>
					<?php foreach($country as $country1):?>	
						<option <?php if($company_profile[0]->country_id==$country1->id):?>selected="selected"<?php endif;?> value="<?php echo $country1->id?>"><?php echo $country1->country_name?></option>
					<?php  endforeach;?>
					<?php else:?>         
						<option selected="selected" value="21"><?php echo _('NO COUNTRY AVAILABLE')?></option>	  
		   			<?php endif;?>
				</select></td>
			  </tr>
              <tr>
                <td class="textlabel"><span style="padding-left:20px">
                  <?php echo _('Vat No.')?></span></td>
                <td><input name="vat" id="vat" class="text short" value="<?php echo $company_profile[0]->vat?>" type="text"></td>
              </tr>
              <tr>
                <td class="textlabel"><span style="padding-left:20px">
                  <?php echo _('Username')?></span></td>
                <td><input name="username" id="username" class="text short" value="<?php echo $company_profile[0]->username?>" type="text"></td>
              </tr>
              <tr>
                <td class="textlabel"><span style="padding-left:20px">
                  <?php echo _('Password')?></span></td>
                <td><input name="password" id="password" class="text short" value="<?php echo $company_profile[0]->password?>" type="text"></td>
              </tr>
              
			  <!-- <tr>
                <td class="textlabel"><span style="padding-left:20px">
                  Fotograaf</span></td>
                <td><span style="padding-right:250px">
                  <input name="photographer" id="photographer" type="text" class="text medium" value="" />                  </span></td>
              </tr>
              <tr>
                <td class="textlabel"><span style="padding-left:20px">
                  Webdesigner                  </span></td>
                <td><span style="padding-right:250px">
                  <select name="webdesigner_id" id="webdesigner_id" type="select" class="select" >
<option  selected value="0">-- Selecter Webdesigner --</option>
<option  value="1">Silicon Valley 1</option>
<option  value="2">Silicon Valley 2</option></select>                  </span></td>
              </tr>-->
			  
              <tr>
                <td class="textlabel" height="30"><span style="padding-left:20px">
                  <?php echo _('Registration Date')?></span></td>
                <td><span style="padding-right:250px">
                  <?php echo $company_profile[0]->registration_date?></span></td>
              </tr>
              <tr>
                <td class="textlabel" height="30"><span style="padding-left:20px">
                  <?php echo _('Expiration Date')?></span></td>
                <td><span style="padding-right:250px">
                  <?php echo $company_profile[0]->expiry_date?></span></td>
              </tr>
              
              <tr>
                <td class="textlabel" height="30"><span style="padding-left:20px">
                  <?php echo _('Notification')?></span></td>
                <td><span style="padding-right:250px">
                  
				  <input type="radio" name="client_register_notification" id="client_register_notification" value="1" <?php if( $company_profile[0]->client_register_notification == 1 ) { echo 'checked="checked"'; } ?> />&nbsp;<?php echo _('On')?><br />
                  <input type="radio" name="client_register_notification" id="client_register_notification" value="0" <?php if( $company_profile[0]->client_register_notification == 0 ) { echo 'checked="checked"'; } ?> />&nbsp;<?php echo _('Off')?><br />
                  
				  </span></td>
              </tr>
              
              <tr>
                <td colspan="2" style="padding-left:20px"><input name="btn_update" id="btn_update" class="submit" value="<?php echo _('UPDATE');?>" type="submit"><input name="act" id="act" value="update_profile" type="hidden"></td>
              </tr>
            </tbody></table>
            </form>
			<script language="javascript" type="text/javascript">
				var frmValidator = new Validator("frm_company_edit");
				frmValidator.EnableMsgsTogether();
				frmValidator.addValidation("company_name","req","<?php echo _('Please enter a company name.');?>");
				frmValidator.addValidation("type_id","dontselect=0","<?php echo _('Please define your industry.');?>");	
				frmValidator.addValidation("first_name","req","<?php echo _('Please give a name.');?>");	
				frmValidator.addValidation("last_name","req","<?php echo _('Please provide a surname.');?>");	
				frmValidator.addValidation("email","req","<?php echo _('Please enter a valid email address.');?>");	
				frmValidator.addValidation("email","email","<?php echo _('Please enter a valid email address.');?>");	
				frmValidator.addValidation("phone","req","<?php echo _('Please enter a valid phone.');?>");
				frmValidator.addValidation("phone","num","<?php echo _('Please enter a valid phone.');?>");	
				frmValidator.addValidation("address","req","<?php echo _('Please provide an address.');?>");	
				frmValidator.addValidation("city","req","<?php echo _('Please enter a city.');?>");	
				frmValidator.addValidation("country_id","dontselect=0","<?php echo _('Please select a country.');?>");	
				frmValidator.addValidation("zipcode","req","<?php echo _('Please give a postcode please.');?>");	
				frmValidator.addValidation("username","req","<?php echo _('Please enter a user name please give.')?>");
				frmValidator.addValidation("password","req","<?php echo _('Please enter a password.');?>");
				<!-- frmValidator.addValidation("photographer","req","<?php echo _('Geef een Fotograaf in');?>");	-->
				<!-- frmValidator.addValidation("webdesigner_id","dontselect=0","<?php echo _('Selecteer een Webdesigner');?>");	-->
			</script>
          </div>
        </div>
      </div>
    </div>
    <!-- /content -->
