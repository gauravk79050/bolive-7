<link href="<?php echo base_url()?>assets/cp/css/ui-lightness/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url()?>assets/cp/css/style.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url()?>assets/cp/new_css/style.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url()?>assets/cp/new_css/intro-upgrade.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo base_url()?>assets/cp/js/jquery-migrate-1.2.1.js"></script>

<style type="text/css">
	.mail_content_decision{
		width:50%;
		float:left;
		text-align: center;
	}
	.mail_content_decision span{
		display: block;
	}

	div#content-container p{
		color:#000;
	}
	div.active-type {
	    background: none repeat  0 0 #EEEEEE;
	    border: 1px dashed #666666;
	    color: #333333;
		margin: 20px auto 0;
	    padding: 20px;
	    text-align: center;
	    width: 300px;
	}
	div.inor-btn {
	    background: none repeat  0 0 #F89329;
	    height: 34px;
	    line-height: 34px;
	    width: 110px;
	}
	div.inor-btn a, div.inor-btn a:visited, div.inor-btn a:hover {
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
	    background: none repeat  0 0 #18517E;
	    border: 0 none;
	    color: #FFFFFF;
	    font-weight: bold;
	    padding: 5px 25px;
	}
	#terms {
		border: medium none;
	    height: 250px;
	    width: 480px;
	}

	/* FOR TSC PLAYER */
	@charset "utf-8";

	#tsc_player {
	   z-index: 9999;
	}

	.tscplayer_inline {
		position:static;
		/*margin: 30px;*/
		width: 100%;
		height: 94%;
		z-index:auto;
	}

	.tscplayer_fullframe {
		position:absolute;
		top: 0px;
		left: 0px;
		margin: 0px;
		padding: 0px;
		z-index: 9999;
	}

	@media screen and (max-width: 640px) {
	    .tscplayer_inline {
		width: 100%;
	    }
	}

	.video_open{
		margin: 10px;
	}

	.video_open img{
		width: 400px;
	}
	/* ----------------------------------- */

	.maininnermid{
		padding: 0;
	}

	.show_demo {
	    background: #d5d575 none repeat  0 0;
	    border: 1px solid #666666;
	    margin: 20px auto 0;
	    padding: 5px 15px;
	    text-align: center;
	    width: 120px;
	}
	.show_demo a {
	    color: #000000;
	    font-size: 15px;
	    font-weight: bold;
	    text-decoration: none;
	}
</style>
<script type="text/javascript" src="<?php echo base_url()?>assets/cp/js/jquery-ui-1.8.16.custom.min.js"></script>
<div id="mail_accept_que" style="display: none;">
	<p><?php echo _("Agree future mails from Bestelonline ??");?></p>
	<div class="mail_content" style="width:100%">
		<div class="mail_content_decision">
			<a href="javascript:;" id="accept">
				<img src="<?php echo base_url();?>assets/cp/images/mail_accept.png" alt="<?php echo _("Yes");?>" />
				<span><?php echo _("I accept");?></span>
			</a>
		</div>
		<div class="mail_content_decision">
			<a href="javascript:;" id="no_accept">
				<img src="<?php echo base_url();?>assets/cp/images/mail_reject.png" alt="<?php echo _("No");?>" />
				<span><?php echo _("Not interested.");?></span>
				<span><?php echo _("Please logged me out.");?></span>
			</a>
		</div>
		<div style="clear: both;"></div>
	</div>
</div>

<script type="text/javascript">
	jQuery.noConflict();
	jQuery(document).ready(function($){
		<?php if($this->company->login_first_time == 1):?>

		tb_show('','#TB_inline?height=290&width=400&inlineId=mail_accept_que&modal=true','');
		 //window.stop();
		<?php endif;?>
		$("#TB_ajaxContent").find("#no_accept").on('click',function(){
			window.location = '<?php echo base_url().'cp/login/logout';?>';
		});

		$("#TB_ajaxContent").find("#accept").on('click',function(){
			$.post(
					base_url+'cp/cdashboard/accept_mail_from_bestelonline',
					{},
					function(response){
						self.parent.tb_remove();
					}
			);
		});

		$('#tabs').tabs();

		$('.video_open').click(function() {

			var TB_WIDTH = $(window).width() * 0.9,
	        TB_HEIGHT = $(window).height() * 0.9; // set the new width and height dimensions here..

		    /*var TB_WIDTH = 100,
		        TB_HEIGHT = 100; // set the new width and height dimensions here..*/
		    $("#TB_window").animate({
		        marginLeft: '-' + parseInt((TB_WIDTH / 2), 10) + 'px',
		        width: TB_WIDTH + 'px',
		        height: TB_HEIGHT + 'px',
		        marginTop: '-' + parseInt((TB_HEIGHT / 2), 10) + 'px'
		    });

		    $("#TB_iframeContent").animate({
		        //marginLeft: '-' + parseInt((TB_WIDTH / 2), 10) + 'px',
		        width: '100%',
		        height: '100%',
		        //marginTop: '-' + parseInt((TB_HEIGHT / 2), 10) + 'px'
		    });

		    $("#TB_ajaxContent").animate({
		        //marginLeft: '-' + parseInt((TB_WIDTH / 2), 10) + 'px',
		        width: '97.6%',
		        height: '100%',
		        overflow: 'hidden'
		        //marginTop: '-' + parseInt((TB_HEIGHT / 2), 10) + 'px'
		    });
		});
	});
</script>
<style type="text/css">
	#pop-window{
		margin: 20px 20px 20px 0;
	}

	#pop-window td{
		border:0px;
		padding: 0 0 10px;
	}

	#pop-window td input, #pop-window td select{
		float:none;
	}

	#pop-window .pop-btn{
		color:#fff;
		background:#18517E;
		padding: 5px 25px;
		border:0px;
		font-weight:bold;
	}
	#tabs{
		position:absolute;
	}
</style>
<div id="main" style="text-transform:none;">
	<div id="main-header">
		<h2><?php echo _('Intro')?></h2>
      	<p class="breadcrumb"><a href="<?php echo base_url()?>cp/cdashboard"><?php echo _('Home')?></a> &raquo; <?php echo _('intro')?></p>
    </div>

	<?php $messages = $this->messages->get();?>
	<?php if($messages != array()):?>
		<?php foreach($messages as $key => $val):?>
			<?php foreach($val as $v):?>
				<div  class = "<?php echo $key;?>"><strong><?php echo ucfirst($key);?> : </strong><?php echo $v;?></div>
			<?php endforeach;?>
		<?php endforeach;?>
    <?php endif;?>

	<?php
      	$account_type = "Bakker";
      	$account_type_id = explode("#",$company[0]->type_id);
      	foreach($company_types as $types){
      		if($account_type_id[0] == $types->id){
      			$account_type = $types->slug;
      		}
      	}
    ?>

	<div id="content">
    	<div id="content-container">
        	<p></p>
        	<div id="tabs">
				<ul>
					<!-- <li><a href="#tabs-0"><?php echo _('Status Account')?></a></li> -->
					<li><a href="#tabs-1"><?php echo _('What is the next step')?></a></li>
<!--				<?php if($this->company->ac_type_id != 1){?>
				<li><a href="#tabs-2"><?php echo _('Setupcost')?></a></li>
				<?php }?>
 -->				<li><a href="#tabs-3"><?php echo _('STAPPENPLAN')?></a></li>
				<li><a href="#tabs-4"><?php echo _('Video-tutorials')?></a></li>
			</ul>

	        <!--TAB ///////////////// -->

			<div id="tabs-1">
				<div class="maininnermid" style="border:0px; width:100%;">
					<div class="billingmain" style="width:98%;">
					<!-- --------------- FREE ----------------------- -->
					<?php if($this->company->ac_type_id == 1){?>
					 <p class="vetenstreep"><?php echo _('Welcome at your own controlepanel')?></p>
                       <p><br />
                         Initieel werd dit controlepaneel gemaakt om bestellingen die online worden uitgevoerd te beheren, vandaar dat u nu bent ingelogd via 'onlinebestelsysteem'. Door de komst van de Europese ingredi&euml;nten wetgeving hebben we vorig jaar besloten om alsook een geavanceerde ingredi&euml;ntensysteem te implementeren in OBS dat gelinkt is aan de FoodDESK databank. We hebben dus 2 aparte systemen doen samensmelten tot 1 groot beheersysteem. In dit controlepaneel heeft u dus de keuze om enkel het ingredientensysteem te gebruiken of het online bestelsysteem of beide. <br />
De verschillende pakketten kan u terugvinden via <a href="<?php echo base_url();?>cp/cdashboard/myaccount"><?php echo _("My account");?></a>.<br />

<br />
<strong><br />
Wat is de volgende stap:</strong>

                       <li>Momenteel heeft u het FREE pakket wat betekent dat u enkel de tab Bestelonline.nu kan bewerken.</li>
	<li><?php echo _('Go to the tab ');?>&nbsp;<a href="http://www.onlinebestelsysteem.net/obs/cp/bestelonline" target="_blank">Bestelonline.nu</a> <?php echo _('and complete the information');?> (u kan kiezen om  bestellingen te aanvaarden via Bestelonline.nu of niet)</li>
                        <li>Bekijk verder het stappenplan (volgende tab) en de "Video Tutorials" (in constructies).</li>
                        <li>UPGRADE uw account door (gratis demo) door op onderstaande UPGRADE-knop te klikken.</li>
						<p>&nbsp;</p>
                        <p>Weet u niet welk pakket het beste bij u past? Vraag ons om raad! (0468 230 208 )</p>

      				<!-- --------------- BASIC ------------------ -->
					<?php }elseif($this->company->ac_type_id == 2){?>
                        <p class="vetenstreep"><?php echo _('Welcome at your own controlepanel')?></p>
                       <p><br />
                         Initieel werd dit controlepaneel gemaakt om bestellingen die online worden uitgevoerd te beheren, vandaar dat u nu bent ingelogd via 'onlinebestelsysteem'. Door de komst van de Europese ingredi&euml;nten wetgeving hebben we vorig jaar besloten om alsook een geavanceerde ingredi&euml;ntensysteem te implementeren in OBS dat gelinkt is aan de FoodDESK databank. We hebben dus 2 aparte systemen doen samensmelten tot 1 groot beheersysteem. In dit controlepaneel heeft u dus de keuze om enkel het ingredientensysteem te gebruiken of het online bestelsysteem of beide. <br />
De verschillende pakketten kan u terugvinden via <a href="<?php echo base_url();?>cp/cdashboard/myaccount"><?php echo _("My account");?></a>.<br />

<br />
<strong><br />
Wat is de volgende stap:</strong>
                        <ol>
                        	<li><?php echo _('Follow the steps in tab "Video Tutorials"');?> (in constructies)</li>
                        	<li><?php echo _('Go to the tab ');?>&nbsp;<a href="http://www.onlinebestelsysteem.net/obs/cp/bestelonline" target="_blank">Bestelonline.nu</a> <?php echo _('and complete the information');?>.</li>
                        	<li><?php echo _('You can test now your Controlepanel and view the results using the link');?>&nbsp;<a href="<?php echo $this->config->item("portal_url").$account_type.'/'.$company[0]->company_slug;?>" target="_blank">&nbsp;<?php echo $this->config->item("portal_url").$account_type.'/'.$company[0]->company_slug;?></a>. <?php echo _("Till in the bestelonline.nu - the status is not set to ACTIVE, your customers will not see any active shop on Bestelonline.nu (only the detailpage). In 'Test Drive' status you only can see the shop to test it out.");?></li>
                        	<li><?php echo _('Two days before the expiry of the trialperiod you will receive an email asking whether you want to proceed with our advanced ordersystem or not (without any obligation or cost afterwards)');?>.</li>
                            <li><?php echo _('You decide when you place the shop live');?>.</li>
                        </ol>
<p>&nbsp;</p>
                        <p><?php echo _("Want to let your client order online via your website for 10");?>&euro;/mnd? Klik op de knop "Upgraden".<!--<?php echo _('month. more? Check the ');?> <a href="<?php echo base_url()?>cp/cdashboard/myaccount">My Account</a> <?php echo _('page for more info');?> --></p>

      				<!-- ------------ PRO --------------------- -->
					<?php }elseif($this->company->ac_type_id == 3){?>
                        <p class="vetenstreep"><?php echo _('There are the steps to start your webshop')?>:</p>
                        <ol>
                        	<li><?php echo _('Follow the steps in tab "Video Tutorials"');?></li>
                        	<!--<li><?php echo _('Go to the tab ');?>&nbsp;<a href="http://www.onlinebestelsysteem.net/obs/cp/bestelonline" target="_blank">Bestelonline.nu</a> <?php echo _('and complete the information');?>.</li> -->
                        	<li><?php echo _('You can test now your Controlepanel and view the results using the link');?>&nbsp;<a href="http://www.onlinebestelsysteem.net/testdrive/bestelonline.php?cid=<?php echo $company[0]->id;?>" target="_blank"><?php echo _("hier");?></a>.<!-- <?php echo _("Till in the bestelonline.nu - the status is not set to ACTIVE, your customers will not see any active shop on Bestelonline.nu (only the detailpage). In 'Test Drive' status you only can see the shop to test it out.");?> --></li>
                        	<li><?php echo _('Two days before the expiry of the trialperiod you will receive an email asking whether you want to proceed with our advanced ordersystem or not (without any obligation or cost afterwards). After your approval we will implement the webshop into your existing website');?>.</li>
                            <li><?php echo _('You decide when you place the shop live (both shop at the same store on your website as on Bestelonline.nu)');?>.</li>
                        </ol>
                       <p></p> <p><?php echo _("Want something extra? Please visit our ADDONS page");?> <a href="<?php echo base_url();?>cp/cdashboard/addons"><?php echo _("ADDONS");?></a></p>
						<p>&nbsp;</p>
					<!-- ----------------- FDD BASIC ------------- -->
      				<?php }elseif($this->company->ac_type_id == 4){?>
                        <p class="vetenstreep"><?php echo _('There are the steps to start the allergen managment')?>:</p>
                        <ol>
                        	<li><?php echo _('You will be updated by mail about the status of your subscription.');?> </li>
                        	<li><?php echo _('In the meantime you can follow the steps in tab "Video Tutorials"');?> </li>

                        </ol>
                       <p></p>
						<p>&nbsp;</p>
					<!-- ----------------- FDD PRO ------------- -->
      				<?php }elseif($this->company->ac_type_id == 5){?>
                        <p class="vetenstreep"><?php echo _('There are the steps to start the allergen managment')?>:</p>
                        <ol>
                        	<li><?php echo _('You will be updated by mail about the status of your subscription.');?> </li>
                        	<li><?php echo _('In the meantime you can follow the steps in tab "Video Tutorials"');?> </li>
                        </ol>
                       <p></p>
						<p>&nbsp;</p>
					<!-- ----------------- FDD PREMIUM ------------- -->
      				<?php }elseif($this->company->ac_type_id == 6){?>
                        <p class="vetenstreep"><?php echo _('There are the steps to start the allergen managment')?>:</p>
                        <ol>
                        	<li><?php echo _('You will be updated by mail about the status of your subscription.');?> </li>
                        	<li><?php echo _('In the meantime you can follow the steps in tab "Video Tutorials"');?> </li>
                        	<li><?php echo _('After that the products are added by us can you can start adding prices and configure those products. Pls check the steps in tab "STEPS"');?> </li>

                        </ol>
                       <p></p>
						<p>&nbsp;</p>
						<p>&nbsp;</p>
					<?php }elseif($this->company->ac_type_id == 7){?>
	                        <p class="vetenstreep"><?php echo _('Welcome to your FoodDESK control panel')?>:</p>
	                        <ol>
	                        	<li><?php echo _('As you may have already noticed, we will keep you informed by mail of what we have already done and what exactly needs to be done"');?></li>
	                        	<li><?php echo _('Meanwhile, you can already watch the videos tab "Video Tutorials"')?></li>
	                        	<li><?php echo _('After the products you can start implemented by us with the configuration of these products (such as price, description, options, etc ..)');?></li>
								<li><?php echo _('Also check the roadmap for the shop in the tab "ROADMAP"');?></li>
	                        </ol>
	                       	<p></p>
							<p>&nbsp;</p>
						<?php }?>

						<div class="show_demo" <?php if (!$company[0]->show_demoshop_link){?>style="display:none;"<?php }?>>
							<a href="http://www.fooddesk.net/testdrive/bestelonline.php?cid=<?php echo $this->company_id?>" target="_blank"><?php echo _('Demo Webshop');?></a>
						</div>

	      				<div class="active-type">
								<?php echo _('Your have'); ?>&nbsp;<span style="color:orange;font-weight:bold;"><?php echo _('PACKAGE').' '.strtoupper($curr_account_type->ac_title); ?></span>
						        <!-- <br /> -->
						        <?php /*if($curr_account_type->id == 3 || $curr_account_type->id == 2){?>
							        <?php echo _('Annual Cost'); ?>&nbsp;:&nbsp;<b><?php echo ( (isset($general_settings) && $general_settings['0']->monthly_cost_addon)?($general_settings['0']->monthly_cost_addon):$curr_account_type->ac_price); ?>&euro;</b>
							        <?php if(isset($general_settings)){?>
							        	<?php $activated_addons = explode("#",$general_settings['0']->activated_addons);?>
							        	<?php if(isset($addons) && !empty($addons) && !empty($activated_addons)){?>
							        		<?php foreach ($addons as $addon){?>
							        			<?php if(in_array($addon->addon_id, $activated_addons)){?>
							        				<br /><?php echo $addon->addon_title; ?>&nbsp;(<?php echo $addon->addon_price; ?>&euro;)
							        			<?php }?>
							        		<?php }?>
							        	<?php }?>
							        <?php }?>
						        <?php }else{?>
						        	<?php echo _('Annual Cost'); ?>&nbsp;:&nbsp;<b><?php echo $curr_account_type->ac_price; ?>&euro;</b>
						        <?php }*/?>
						        <br /><br />
						        <?php echo _('Registration Date'); ?>&nbsp;:&nbsp;<?php echo date('d/ m/ Y',strtotime($company[0]->registration_date)); ?>
						        <?php if($this->company->ac_type_id != 1 && $this->company->on_trial == 1 && $this->company->trial != "0000:00:00"):?>
							        <br />
							        <?php echo _('Due Date'); ?>&nbsp;:&nbsp;<?php echo date('d/ m/ Y',strtotime($company[0]->trial)); ?>
						        <?php endif;?>
						</div>
						<div align="center" class="bp_link">

							<span style="display: inline-block; text-align: left !important;">
								<!--<?php echo _("Link to Bestelonline live (what visitor are seeing)");?> - <b><a href="<?php echo $this->config->item("portal_url").$account_type.'/'.$company[0]->company_slug;?>" target="_blank"><?php echo $this->config->item("portal_url").$account_type.'/'.$company[0]->company_slug;?></a></b> -->

								<?php if( ( $company[0]->ac_type_id == 2 ) && $general_settings[0]->shop_testdrive){?>
								<br />
								<br />
								<?php echo _("Link to your test environment (to do settings)");?> - <b><a href="<?php echo $this->config->item("portal_url").$account_type.'/'.$company[0]->company_slug.'/testdrive';?>" target="_blank"><?php echo $this->config->item("portal_url").$account_type.'/'.$company[0]->company_slug.'/testdrive';?></a></b>
								<?php }?>

								<?php if( $company[0]->ac_type_id == 3 ){?>
								<br />
								<br />
								<?php echo _("Link to simulation OBS-Module (in your website)");?> - <b><a href="http://www.onlinebestelsysteem.net/testdrive/bestelonline.php?cid=<?php echo $company[0]->id;?>" target="_blank"><?php echo _("Click here");?></a></b>
								<?php }?>
							</span>

							<?php if($company[0]->ac_type_id != 3){?>
							<div align="center" style="padding:25px 0px 10px 0px;">
						        <div class="inor-btn">
						        	<a title="Upgrade Now" onclick="tb_show('<?php echo _("Upgrade Now")?>','TB_inline?height=400&amp;width=500&amp;inlineId=upgrade',null);" href="javascript:void(0);">Upgraden</a>
						        </div>
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
						                     		<?php if($at->id > $company[0]->ac_type_id){?>
						                     	<option value=" <?php echo $at->id; ?>"><?php echo $at->ac_title.' '._('Package').' ('.$at->ac_price.'&euro;/'._('month').')'; ?></option>
						                     		<?php }
						                     		elseif(($company[0]->ac_type_id == 7) && ($at->id == 4 || $at->id == 5)){?>
						                     	<option value=" <?php echo $at->id; ?>"><?php echo $at->ac_title.' '._('Package').' ('.$at->ac_price.'&euro;/'._('month').')'; ?></option>
						                     		<?php }?>
						                     	<?php } ?>
						                     	</select>
						                     	<?php } ?>
						                	</td>
						             	</tr>
						             	<tr>
						                	<td colspan="2">
						                		<iframe id="terms" src="<?php echo base_url();?>terms_conditions" ></iframe>
						                	</td>
						             	</tr>
						             	<tr>
						                	<td colspan="2" style="text-align:center;"><input type="checkbox" name="agree" id="agree" value="1" checked="checked" />&nbsp;<?php echo _('I agree with the terms & conditions.'); ?></td>
						             	</tr>
						          		<tr><td colspan="2" style="text-align:center;"><input type="submit" name="upgrade_package" id="upgrade_package" onClick="return validation(this);" class="pop-btn" value="<?php echo _('Start 30 day trial'); ?>" /></td></tr>
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
						    <?php }?>
							<div style="clear:both;"></div>
						</div>
					</div>
    			</div>
                <div style="clear:both;"></div>
			</div>

	        <!--TAB /////////////////
            <?php if($this->company->ac_type_id != 1){?>
			<div id="tabs-2">
				<div class="maininnermid" style="border:0px; width:100%;">
      				<div class="billingmain" style="width:98%;">
						<p class="vetenstreep"><?php echo _('What is a setup cost')?></p>
        				<p><br><?php echo _('Setup cost is a one-time cost. You can choose from 4 different packages below:')?><br>
        				<br>
        				</p>
        				<table width="50%" style="border-style: none;">
         				 <tbody>
						 	<?php if($packages):foreach($packages as $package):?>
							<tr>
            					<td width="30%"><?php echo $package->package_name?></td>
           						<td width="70%"><span class="groen"><?php echo $package->package_price?></span>&nbsp; <?php echo _('VAT exlusive')?></td>
          					</tr>
							<?php endforeach;else:?>
							<tr>
            					<td colspan=2><?php echo _('No package available')?></td>

          					</tr>
							<?php endif;?>

        				</tbody></table>
      					<p>&nbsp;</p>
     				   <br>
     				   <form name="frm_order_package" id="frm_order_package" method="post" enctype="multipart/form-data" action="<?php echo base_url()?>cp/cdashboard/packages">
							<input name="OBS - SiteMatic BVBA_REF_VIEW" value="klant_setupkosten" type="hidden">


							<div class="form">
          					<table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color:#f2f2f2">
           						<tbody><tr>
              						<td align="center"><table width="70%" border="0" cellpadding="0" cellspacing="0">
                						<tbody><tr>
                    						<?php // print_r($company);?>
											<td valign="middle" width="35%"><?php echo _('Select Package'); ?> :</td>
                   							<td><select name="package_id" id="package_id" type="select" style="width:215px">
													<option value="0"><?php echo _('Select')?></option>
													<?php if($packages):foreach($packages as $package):?>
													<option value="<?php echo $package->id?>" <?php if($company[0]->packages_id == $package->id):?>selected="selected"<?php endif;?>><?php echo $package->package_name."&nbsp;(".$package->package_price." &#8364; )"?></option>
													<?php endforeach;else:?>
													<option value="-1"><?php echo _('No package available')?></option>
													<?php endif;?>
													</select></td>
                  							</tr>
                  							<tr>
                   							 <td style="padding-top:10px" colspan="2" valign="middle" width="35%"><div style="float:left; padding-right:10px">
                      						  <input name="agree" id="agree" value="1" type="checkbox">&nbsp;&nbsp;<a href="http://www.onlinebestelsysteem.net/site/gebruikersvoorwaarden.html" target="_blank" style="text-decoration:none"><?php echo _('I agree with the terms of use')?></a></div></td>
                   							 <td></td>
											</tr>
                 						 	<tr>
                    							<td colspan="2">&nbsp;</td>
                  							</tr>
                  							<tr>
                    							<td>&nbsp;</td>
                    							<td><input name="btn_register" id="btn_register" value="Order" class="send" style="margin-left:0px" type="submit">                      <input name="act" id="act" value="order_package" type="hidden"></td>
                  							</tr>
                						</tbody></table></td>
         						   </tr>
          					</tbody></table>
                    		<script language="javascript" type="text/javascript">
								var frmValidator = new Validator("frm_order_package");
								frmValidator.EnableMsgsTogether();
								frmValidator.addValidation("package_id","dontselect=0","<?php echo _('Please choose a package')?>");
								frmValidator.addValidation("agree","shouldselchk","<?php echo _('Please agree to the terms of use')?>");
		  					</script>
        				</div>
						</form>
						<br>
						<br>
          				<div align="left"><?php echo _('<img src="http://www.onlinebestelsysteem.net/obs/assets/cp/images/intro-pakketten.png"/>')?>

                        </div>
					</div>
				</div>

                <div style="clear:both;"></div>

			</div>
            <?php }?>
  -->

			<div id="tabs-3">
				<div class="maininnermid" style="border:0px; width:100%;">
      				<div class="billingmain" style="width:98%;">

                        <p class="vetenstreep">STAPPENPLAN voor FoodDESK pakketten - (FDD BASIC / FDD PRO / FDD PREMIUM)</p>

                        <ul>
                          <li>Vooreerst trachten wij u persoonlijk even te ontmoeten en meer duiding te geven over het systeem</li>
                          <li>U geeft aan in welk pakket u interesse heeft en wij sturen u een factuur voor de opstartkosten</li>
                          <li>Nadat we het bedrag hebben ontvangen activeren wij uw pakket en sturen wij u per direct een attest op dat u als bewijs kan gebruiken bij een eventuele controle </li>
                          <li>Wij implementeren al uw producten (digitaal aangeleverd) in uw controlepaneel zodat u dit niet zelf hoeft in te geven.</li>
                          <li> U verzamelt alle productenfiches (pdf) en wij gaan deze implementeren in de FoodDESK databank (Ondertussen wordt u via mail op de hoogte gehouden over de status van werken).</li>

                          <li>Nadien wordt er een afspraak gemaakt om samen het systeem te doorlopen en enkele recepturen samen in te geven.</li>
                          <li>Enkel bij PRO en PREMIUM gaan wij de producten die door u werden ingegeven linken aan de juiste productenfiches (op regelmatige tijdstippen)</li>
                          <li>Wanneer dit allemaal volbracht is gaan we - afhankelijk wat u gekozen heeft - de infobalie (of tablet) OF webwinkel installeren.

</li>
                          <li>Wij geven u nog maanden bijstand via telefoon of teamviewer (afhankelijk van het gekozen pakket)</li>

                        </ul><br />
                        ------------------
                        <br /><br />
                        <p class="vetenstreep">STAPPENPLAN voor webwinkels</p>
Elk pakket of situatie heeft een eigen stappenplan. Dit stappenplan zullen wij u toezenden wanneer u heeft aangegeven onze webwinkel-module te willen gebruiken, maar hieronder reeds een algemene 'flow'.  <br /><br />


                        <ul>
                          <li>U kan gratis 1 maand lang het controlepaneel uittesten (demo winkel is voorzien)</li>
                          <li>De webwinkel wordt door ons geactiveerd en een opleiding wordt gegeven</li>
                          <li>Na een maand wordt er gevraagd of u 'fan' bent van het systeem of niet</li>
                          <li>Indien goedgekeurd sturen wij u een factuur en wanneer deze voldaan werd implementeren wij de webwinkel in uw (bestaande) website</li>
                          <li>Deze webwinkel wordt meestal aangemaakt op een aparte pagina dat niet door uw bezoekers zichtbaar is.</li>
                          <li>U beslist wanneer de webwinkel 'online' gaat</li>
                          <li>U krijgt 3 maanden gratis bijstand via telefoon of teamviewer nadat de webwinkel online staat</li>
                          <li>Activatie Mail2print: Bestellingen kunnen automatisch afgeprint worden via onze Mail2print app</li>
                          <li>U kan nu zorgeloos online bestellingen ontvangen (volledig automatische afhandeling) </li>


                        </ul>
<p>&nbsp;</p>
                        <p>&nbsp;</p>

                        <p>&nbsp;</p>


      				</div>
    			</div>

			    <div style="clear:both;"></div>

			</div>

	<!--TAB ///////////////// -->

			<div id="tabs-4">
				<div class="maininnermid" style="border:0px; width:100%;">
					<?php //if($this->company_id == 87){?>
					<?php if($this->company->ac_type_id == 1){?>
                   	01 - <a href="#TB_inline?inlineId=free_v" class="thickbox video_open"><?php echo _("Videotutorial INTRO");?></a>
					<div id="free_v" style="display: none;"><iframe class="tscplayer_inline" name="tsc_player_free" src="<?php echo base_url();?>videos/01-Free/Free_player.html" scrolling="no" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>
                    <?php }else{?>
                    01 - <a target="_blank" href="<?php echo $this->config->item('video_url').'video/view/'.get_video_token().'/1'; ?>">Rondleiding</a>
                    <br />
                    <br />
                    02 - <a target="_blank" href="<?php echo $this->config->item('video_url').'video/view/'.get_video_token().'/2'; ?>">De eerste stappen</a>
                    <?php if($this->company->ac_type_id == 4 || $this->company->ac_type_id == 5 || $this->company->ac_type_id == 6){?>
		            <br />
                    <br />
                    03 - <a target="_blank" href="<?php echo $this->config->item('video_url').'video/view/'.get_video_token().'/3'; ?>">Hoe recepturen toevoegen</a>
                    <br />
                    <br />
                    04 - <a target="_blank" href="<?php echo $this->config->item('video_url').'video/view/'.get_video_token().'/4'; ?>">Webwinkel algemeen</a>
                    <br />
                    <br />
                    05 - <a target="_blank" href="<?php echo $this->config->item('video_url').'video/view/'.get_video_token().'/5'; ?>">Webwinkel configuratie</a>
		            <?php }?>
                    <?php }?>
    			</div>

               <div style="clear:both;"></div>

			</div>

		</div>
		</div>
		</div>