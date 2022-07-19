<!--carl css -->
<style type="text/css">
#sidebar, #sidebarSet{
	display:none !important;
}
div#content-container p{
	color:#000;
}
div.active-type {
    background: none repeat scroll 0 0 #EEEEEE;
    border: 1px dashed #666666;
    color: #333333;
    margin: 0 auto;
    padding: 20px;
    text-align: center;
    width: 300px;
}
div.inor-btn {
    background: none repeat scroll 0 0 #F89329;
    border: 1px solid #CA6A0A;
    height: 34px;
    line-height: 34px;
    width: 195px;
}
div.inor-btn a, div.inor-btn a:visited, div.inor-btn a:hover {
    border: 1px solid #F2BA82;
    color: #FFFFFF;
    font-weight: bold;
    padding: 8px 11px;
    text-decoration: none;
}
#pop-window {
    margin: 20px 20px 20px 0;
}
#pop-window td {
    border: 0 none;
    padding: 0 0 10px;
}
#pop-window td input, #pop-window td select {
    float: none;
}
#pop-window .pop-btn {
    background: none repeat scroll 0 0 #18517E;
    border: 0 none;
    color: #FFFFFF;
    font-weight: bold;
    padding: 5px 25px;
}
#terms {
	border: medium none;
    height: 250px;
    width: 420px;
}
</style>

<div id="main" style="text-transform:none;">		
 <div id="main-header">
	<h2><?php echo _('Page Not Active')?></h2>
 </div>
 
 <div id="content">
   <div id="content-container">
   		
   	  <?php $messages = $this->messages->get();?>
		<?php if($messages != array()):?>
			<?php foreach($messages as $key => $val):?>
				<?php foreach($val as $v):?>
					<div  class = "<?php echo $key;?>"><strong><?php echo ucfirst($key);?> : </strong><?php echo $v;?></div>
				<?php endforeach;?>	
			<?php endforeach;?>
	    <?php endif;?>
   
      <p>Deze pagina kan enkel bekeken en aangepast worden wanneer u uw account upgrade.<br />
<br />
</p>
      <p>In het menu (rechts bovenaan) ziet u "<a href="http://www.onlinebestelsysteem.net/obs/cp/cdashboard/myaccount" target="_blank">Mijn account</a>" waar u de verschillende pakketten kan vergelijken <br />
en een DEMO van 30 dagen aanvragen (of klik op de onderstaande knop).
     <br />
<br />
</p>
      <br />
      <p></p>
      <br /><br />
      <div class="active-type">
		<?php echo _('Your have'); ?>&nbsp;<span style="color:orange;font-weight:bold;"><?php echo _('PACKAGE').' '.strtoupper($curr_account_type->ac_title); ?></span>
        <br />
        <?php echo _('Annual Cost'); ?>&nbsp;:&nbsp;<b><?php echo $curr_account_type->ac_price; ?>&euro;</b> 
        <br /><br />
        <?php echo _('Registration Date'); ?>&nbsp;:&nbsp;<?php echo date('d/ m/ Y',strtotime($company[0]->registration_date)); ?>
        <?php if($this->company->ac_type_id != 1):?>
        <br />
        <?php echo _('Due Date'); ?>&nbsp;:&nbsp;<?php echo date('d/ m/ Y',strtotime($company[0]->expiry_date)); ?>
        <?php endif;?>
      </div>
      <div align="center" class="bp_link">
      <!--<?php
      	$account_type = "Bakker";
      	$account_type_id = explode("#",$company[0]->ac_type_id);
      	foreach($company_types as $types){
      		if($account_type_id[0] == $types['id']){
      			$account_type = $types['slug'];
      		}
      	}
      ?>
      <div class="bp_link"><a href="<?php echo $this->config->item("portal_url").$account_type.'/'.$company[0]->company_slug;?>" target="_blank"><?php echo $this->config->item("portal_url").$account_type.'/'.$company[0]->company_slug;?></a></div> -->
      <div align="center" style="padding:25px 0px 10px 0px;">
        <div class="inor-btn">
         <a title="Upgraden" onclick="tb_show('<?php echo _("Upgrade Now")?>','TB_inline?height=400&amp;width=450&amp;inlineId=upgrade',null);" href="javascript:void(0);"><?php echo _("Upgrade Now")?></a>
        </div><br />
<br />

      </div>
      
      <!-- Upgrade Hidden Div -->
				  
      <div id="upgrade" style="display:none;">
          
          <form method="post" action="<?php echo base_url(); ?>cp/cdashboard/upgrade" id="request_upg_form">
          <input type="hidden" name="company_id" id="company_id" value="<?php echo $company[0]->id; ?>" />
          <input type="hidden" name="current_ac_type_id" id="current_ac_type_id" value="<?php echo $curr_account_type->id; ?>" />
          <table cellspacing="8" id="pop-window">
             <tr>
                <td style="text-align:right;"><?php echo _('Please select a package'); ?></td>
                <td style="text-align:left;">
                    <?php if(!empty($account_types)) { ?>
                     <select name="requested_ac_type_id" id="requested_ac_type_id" class="required">
                     <?php foreach($account_types as $at) { ?>						 
                     <option value="<?php echo $at->id; ?>"><?php echo $at->ac_title.' '._('Package').' ('.$at->ac_price.'&euro;/'._('month').')'; ?></option>
                     <?php } ?>
                     </select>
                     <?php } ?>
                </td>
             </tr>
             <tr>
                <td colspan="2">
                	<iframe width="420px" id="terms" src="<?php echo base_url();?>terms_conditions" ></iframe>
                </td>
             </tr>
             <tr>
                <td colspan="2" style="text-align:center;"><input type="checkbox" name="agree" id="agree" value="1" checked="checked" />&nbsp;<?php echo _('I agree with the terms & conditions.'); ?></td>
             </tr>
             <tr><td colspan="2" style="text-align:center;"><input type="submit" name="upgrade_package" id="upgrade_package" onClick="return validation(this);" class="pop-btn" value="<?php echo _('Upgrade Now'); ?>" /></td></tr>
          </table>
          
          </form>
          
		  <script type="text/javascript">		
			var frmValidator = new Validator("request_upg_form");
			frmValidator.EnableMsgsTogether();
			frmValidator.addValidation("requested_ac_type_id","req","<?php echo _('Please select an account type to upgrade.');?>");
			frmValidator.addValidation("agree","req","<?php echo _('Please agree with our terms & conditions.');?>");

			function validation(obj){
				if(!jQuery(obj).closest('form').find('#agree').is(':checked')){
					alert("<?php echo _('Please agree with our terms & conditions.');?>");
					return false;
				}else{
					return true;
				}
			}
		  </script>
          
      </div>
      
      <div style="clear:both;"></div>
      
   </div>
 </div>