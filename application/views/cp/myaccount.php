<!-- <link type="text/css" href="<?php echo base_url()?>assets/cp/css/ui-lightness/jquery-ui-1.8.16.custom.css" rel="stylesheet" /> -->		
<link href="<?php echo base_url()?>assets/cp/css/style.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url()?>assets/cp/new_css/style.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url()?>assets/cp/new_css/intro-upgrade.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url()?>assets/cp/new_css/main.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url()?>assets/cp/new_css/style_11.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo base_url()?>assets/cp/js/jquery-ui-1.8.16.custom.min.js"></script>

<!--  NEW PRICING TABLE -->
<link rel="stylesheet" href="<?php echo base_url()?>assets/cp/new_css/pricing_style.css" type="text/css" >

<!--[if (gte IE 6)&(lte IE 8)]>
  	<script src="<?php echo base_url()?>assets/cp/new_js/selectivizr.js"></script>
<![endif]-->

<!--[if IE]>
	<script src="<?php echo base_url()?>assets/cp/new_js/ie.js"></script>
<![endif]-->

<!--HTML5 Shiv-->
<!--[if lt IE 9]>
		<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<script type="text/javascript">		
	jQuery.noConflict();
	jQuery(document).ready(function($){
		//$('#tabs').tabs();	
	});

	jQuery('.sign_up').live('click',function(){
		jQuery.ajax({
			url: '<?php echo base_url();?>cp/cdashboard/change_account',
			data: {'account_type_id': jQuery(this).attr('rel')},
			type: 'POST',
			dataType: 'json',
			success: function(data){
				if(data.error){
					alert(data.message);
				}else{
					//alert(data.message);
					jQuery('#message').html(data.message);
					//jQuery('#message').css('display','block');
					jQuery('#message').toggle('slow');
					jQuery('#header').focus();
					//window.scrollTo(0,0);
					jQuery('body').animate({
						scrollTop: jQuery('body').position().top }, 'slow');
				}
			}
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
</style>

<div id="main" style="text-transform:none;">		
 <div id="main-header">
	<h2><?php echo _('My Account')?></h2>
      <p class="breadcrumb"><a href="<?php echo base_url()?>cp/cdashboard"><?php echo _('Home')?></a> &raquo; <?php echo _('My Account')?></p>
    </div>
	
	<?php $messages = $this->messages->get();?>
	<?php if($messages != array()):?>
		<?php foreach($messages as $key => $val):?>
			<?php foreach($val as $v):?>
				<div  class = "<?php echo $key;?>"><strong><?php echo ucfirst($key);?> : </strong><?php echo $v;?></div>
			<?php endforeach;?>	
		<?php endforeach;?>
    <?php endif;?>
	
	<div id="content">
      <div id="content-container">
        <p></p>
        <div id="tabs">
			
			<div id="message_div"><p id="message" style="display: none;"></p></div>
			
	        <!--TAB ///////////////// -->		
            
			<div style="text-align: center;">
				
				<div class="active-type">
				    <?php echo _('Your have'); ?>&nbsp;<span style="color:orange;font-weight:bold;"><?php echo _('PACKAGE').' '.strtoupper($curr_account_type->ac_title); ?></span>
					<br />
					 <?php $trail_date1= strtotime($trail_date);?>
					<?php if($ac_type_id=='2'|| $ac_type_id=='3'){if($company[0]->on_trial=='1'){echo _('Free Trial till'); ?>&nbsp;<span style="color:orange;font-weight:bold;"><?php if($ac_type_id=='2'||$ac_type_id=='3') echo date('d-m-Y',$trail_date1); ?></span><?php }else{} }?>
					<br />
					<?php echo _('Monthly Cost'); ?>&nbsp;:&nbsp;<b><?php echo $general_settings['0']->monthly_cost_addon; ?>&euro;</b> 
					<br /><br />
					<?php echo _('Registration Date'); ?>&nbsp;:&nbsp;<?php echo date('d/ m/ Y',strtotime($company[0]->registration_date)); ?>
					<br />
					<?php if($ac_type_id=='2'|| $ac_type_id=='3'){echo _('Due Date'); ?>&nbsp;:&nbsp;<?php echo date('d/ m/ Y',strtotime($company[0]->expiry_date));} ?>
				</div>
				<br /><br />
				<?php if($ac_type_id=='2'|| $ac_type_id=='3'){if($company[0]->on_trial=='1'){ if($general_settings[0]->hide_bp_intro == '1') {?>
                     <div style="background:#EBF7C5; padding:10px 8px; margin-bottom:20px; text-align:center; border:1px solid #ddd; width:650px; margin-left:170px;"><?php echo _('Test link: ');?><a href="#"> <?php echo 'http://www.bestelonline.nu/'.$type_slug.'/'.$company_slug;?> </a>    <?php echo _(' '.' (till date)').date('d-m-Y',$trail_date1);?>  </div>
	                   <?php } else {?>
					<div style="background:#EBF7C5; padding:10px 8px; margin-bottom:20px; text-align:center; border:1px solid #ddd; width:650px; margin-left:170px;"><?php echo _('Test link: ');?><a href="<?php echo 'http://www.bestelonline.nu/'.$type_slug.'/'.$company_slug;?>"> <?php echo 'http://www.bestelonline.nu/'.$type_slug.'/'.$company_slug;?> </a>    <?php echo _(' '.' (till date)').date('d-m-Y',$trail_date1);?>  </div>
						<?php } } else{}}?>
				
<?php /*?>				
				<div id="regi-medial-box-div" class="p_table">
		        
				<?php if(!empty($account_types)) { $count = 0; $row_style = 'row_style_3'; ?>
				<?php foreach($account_types as $at) { $count++; ?>
				
					<div class="column_<?php echo $count;?>">
						<!-- ribbon (optional) -->
						<?php if($at->ac_title == 'Free'){?>
						<div class="column_ribbon ribbon_style1_free"></div>
						<?php }if($at->ac_title == 'Pro'){?>
						<div class="column_ribbon ribbon_style1_sale"></div>
						<?php }?>
						
						<!-- /ribbon -->
						
						<ul>
							<!-- column header -->
							<li class="header_row_1 align_center"><h2 class="col1"><?php echo $at->ac_title;?></h2></li>
							<li class="header_row_2 align_center"><h1 class="col1">&euro;<span><?php echo $at->ac_price;?></span></h1><h3 class="col1"><?php echo $this->lang->line('per_month_txt');?></h3></li>
							<!-- data rows -->
							<?php if($row_style == 'row_style_3'){?>
							<li class="<?php echo $row_style;?> align_center"><span><?php echo $at->ac_description;?></span></li>
							<?php $row_style = 'row_style_4';?>
							<?php }else{ $row_style = 'row_style_4';?>
							<li class="<?php echo $row_style;?> align_center"><span><?php echo $at->ac_description;?></span></li>
							<?php $row_style = 'row_style_3';?>
							<?php }?>
							
							<!-- column footer -->
							<?php if($at->id  == $curr_account_type->id) { ?>
							  <li class="footer_row"><span class="current_package"><?php echo _('You have this package'); ?></span></li>
							  <?php } else { ?>
							  <li class="footer_row"><a rel="<?php echo $at->id;?>" href="javascript: void(0);" class="sign_up radius3"><?php echo _("Sign Up");?></a></li>
							<?php } ?>
							<!-- <li class="footer_row"><a href="<?php echo base_url();?>cp/cdashboard/upgrade/<?php echo urlencode($at->id);?>"  class="sign_up radius3"><?php echo _("Sign Up");?></a></li> -->
						</ul>
					</div>
				
				<?php } ?>
				<?php } ?>					
					
					<div style="display:none" id="div1">
					  <div style=" text-align:center"><span class="regibox1-heading1">0$</span> <span class="regibox1-heading2">/ maand</span></div>
					  <br>
					  <div style=" text-align:center" class="regibox1-heading3"><?php echo strtoupper('FREE PACKAGE'); ?></div>
					  <br>
					  <div style=" text-align:center" class="regi2-details2"><?php echo _('Meld uw zaak gratis aan bij KIKENBESTEL en uw klanten zullen u vlugger vinden via het internet.'); ?></div>
					  <br>
					  <div style="padding-left:200px">
						<ul class="regibox1_list">
						  <li>Gratis uw gegevens aanmelden</li>
						  <li>Hogere Score In Google rankings</li>
						  <li>Een eigen detailpagina op KEB</li>
						  <li>Gedetailleerde omschrijving</li>
						  <li>Contactgegeven(tel/fax)</li>
						  <li>Contactformulier</li>
						  <li>Openingsuren</li>
						  <li>Google Maps</li>
						  <li>Online bereikbaar 24/24u</li>
						</ul>
					  </div>
					  <div align="center" style="padding-top:20px;"><a href="index.php?view=company_register&amp;pkg=1"><img width="137" height="34" border="0" src="<?php echo base_url()?>assets/cp/new_css/images/gratis-aanmelden-bu.jpg"></a></div>
					</div>
					
					<div style="display:none" id="div2">
					  <div style=" text-align:center"><span class="regibox1-heading1">19$</span> <span class="regibox2-heading2">/ maand</span></div>
					  <br>
					  <div style="text-align:center" class="regibox1-heading3">BASIC PAKKET</div>
					  <br>
					  <div style="text-align:center" class="regi2-details2">Een eigen mini-website op KLIKENBESTEL inclusief een online bestelsysteem.</div>
					  <br>
					  <div style="padding-left:60px">
						<div style="width:50%; float:left">
						  <ul class="regibox2_list">
							<li>IDEM GRATIS PAKKET</li>
							<li>Overzichtelijk Controlepanel met Ontzettend veel mogelijkheden</li>
							<li>In 3 klikken bestellingen beheren</li>
							<li>Onbeperkt aantal bestellingen</li>
							<li>U verzameit automatisch alle gegevens van uw klanten</li>
						  </ul>
						</div>
						<div style="width:48%; float:left; padding-left:10px">
						  <ul class="regibox2_list">
							<li>Mailingsysteem</li>
							<li>Geen commissie (!)</li>
							<li>Onbeperkt aantal categorieen en subcategorieen</li>
							<li>Tot 250 producten</li>
							<li>Gratis updates / upgrades</li>
							<li>Dagelijkse backup van database</li>
						  </ul>
						</div>
					  </div>
					  <div style="text-align:center; padding-left:200px; text-decoration:underline" class="regibox2-bottom-heading"> 3 maanden gratis testen </div>
					  <div align="center" style="padding-top:20px;"><a href="index.php?view=company_register&amp;pkg=2"><img width="137" height="34" border="0" src="<?php echo base_url()?>assets/cp/new_css/images/gratis-aanmelden-bu.jpg"></a></div>
					</div>	
					
					
					<div style="display:none" id="div3">
					  <div style=" text-align:center"><span class="regibox3-heading1">29$</span> <span class="regibox3-heading2">/ maand</span></div>
					  <br>
					  <div style="text-align:center; color:#FF0000" class="regibox1-heading3">PRO PAKKET</div>
					  <br>
					  <div style="text-align:center" class="regi2-details2">Online bestelsysteem gemakkelijk integreerbaar in bestaande en/of nieuwe websites</div>
					  <br>
					  <div style="padding-left:60px">
						<div style="width:50%; float:left">
						  <ul class="regibox2_list">
							<li>IDEM BASIC PAKKET</li>
							<li>UNIEK en NIEUW bestelsysteem</li>
							<li>Speciaal gemaakt op maat voor de kleinhandel (alle types)</li>
							<li>Uw klanten kunnen bestellingen doorvoeren via KEB en uw websiite</li>
							<li>Zowel KEB als uw website worden geupdated bij een update</li>
						  </ul>
						</div>
						<div style="width:48%; float:left; padding-left:10px">
						  <ul class="regibox2_list">
							<li>Alle bestellingen gedaan via KEB of uw website kan u terugvinden in 1 plaats</li>
							<li>Nooit meer updates installeren</li>
							<li>Geen updatekosten</li>
							<li>GRATIS hosting tot 50MB</li>
							<li>GRATIS domeinnaam</li>
							<li>Support 7d/7 zeifs op zondagen</li>
						  </ul>
						</div>
					  </div>
					  <div align="center" style="clear:both;padding:25px 0px 10px 0px"><a style="color:#18517E" href="#">&lt;&lt; Klik hier voor een uitgebreidde omschrijving van het bestelsysteem &gt;&gt;</a></div>
					  <div align="center" style="clear:both;padding:25px 0px 10px 0px"><a href="index.php?view=company_register&amp;pkg=3"><img width="137" height="34" border="0" src="<?php echo base_url()?>assets/cp/new_css/images/gratis-aanmelden-bu.jpg"></a></div>
					</div>
					
				  </div>
<?php */?>				  
				  <div class="price-table-container" style="display: inline-block;">
			          <h4><?php echo $this->lang->line("no_account_signup_txt");?></h4>
			          <div id="feature-list" class="column">
			            <div class="blank-header"></div>
			            <ul>
			              	<li class="bold" >Setupkosten</li>
					        <li class="bold" >Vermelding op<br />Bestelonline</li>
					        <li class="bold" >Controlepaneel (CMS)</li>
					        <li class="bold" >Webwinkel op<br />Bestelonline</li>
					        <li class="bold" >Commissie 0%</li>
					        <li class="bold" >Mailingsysteem</li>
					        <li class="bold" >Online betalen</li>
					        <li class="bold" >Meerdere winkels</li>
					        <li class="bold" >Updates/Upgrades</li>
					        <li class="bold" >Fraudecontrole</li>
					        <li class="bold" >24/7 support</li>
					        <li class="bold" >Backups</li>
					        <li class="bold" >Webwinkel in Website</li>
					        <li class="bold" >Verschillende layouts</li>
					        <li class="bold" >Voorbeeld</li>
			            </ul>
			          </div>
			          <div id="starter" class="column gray">
			            <h1>FREE</h1>
			            <h2>0&euro;/mnd</h2>
			            <h3>Gratis reclame<br />voor uw winkel!</h3>
            			<ul>
					        <li data-feature="Setupkosten">Geen</li>
					        <li data-feature="Vermelding"><img src="<?php echo base_url();?>assets/images/check.png" width="28" height="26" /></li>
					        <li data-feature="controlepaneel"><img src="<?php echo base_url();?>assets/images/check.png" width="28" height="26" /></li>
					        <li data-feature="Webwinkelbo" class="desc-drop">(*) GRATIS éénvoudige bestelformulier dat u kan aan/afzetten in uw controlepaneel <br />(geen opvolging van deze bestellingen) </li>
					        <li data-feature="commissie"><img src="<?php echo base_url();?>assets/images/check.png" width="28" height="26" /> </li>
					        <li data-feature="Mailingsysteem"></li>
					        <li data-feature="Online betalen"></li>
                            <li data-feature="Meerdere winkels"></li>
					        <li data-feature="Updates"></li>
					        <li data-feature="Fraudcontrole"></li>
					        <li data-feature="support"></li>
					        <li data-feature="Backups"></li>
					        <li data-feature="Webwinkel"></li>
					        <li data-feature="layouts"></li>
					        <li data-feature="voorbeeld"><a href="http://www.bestelonline.nu/vishandel/al-vis-nv" target="_blank">Vermelding op<br />
Bestelonline.nu</a></li>
            			</ul>
			            <?php if($curr_account_type->id == 1) { ?>
						  <a href="javascript: void(0);"><?php echo _('You have this package'); ?></a>
						  <?php } else { ?>
						  <a rel="1" href="javascript: void(0);" class="sign_up"><?php echo _("Sign Up");?></a>
						<?php } ?>
						
			            </div>
			          <div id="personal" class="column gray">
			            <h1>BASIC</h1>
			            <h2>19&euro;/mnd</h2>
			            <h3>Idem FREE +<br />Webwinkel op Bestelonline.nu</h3>
            			<ul>
					        <li data-feature="Setupkosten">Geen</li>
					        <li data-feature="Vermelding"><img src="<?php echo base_url();?>assets/images/check.png" width="28" height="26" /></li>
					        <li data-feature="controlepaneel"><img src="<?php echo base_url();?>assets/images/check.png" width="28" height="26" /></li>
					        <li data-feature="Webwinkelbo"><img src="<?php echo base_url();?>assets/images/check.png" width="28" height="26" /></li>
					        <li data-feature="commissie"><img src="<?php echo base_url();?>assets/images/check.png" width="28" height="26" /></li>
					        <li data-feature="Mailingsysteem"><img src="<?php echo base_url();?>assets/images/check.png" width="28" height="26" /></li>
					        <li data-feature="Online betalen"><img src="<?php echo base_url();?>assets/images/check.png" width="28" height="26" /></li>
                            <li data-feature="Setupkosten"><img src="<?php echo base_url();?>assets/images/check.png" width="28" height="26" /></li>
					        <li data-feature="Updates">Gratis</li>
					        <li data-feature="Fraudcontrole"><img src="<?php echo base_url();?>assets/images/check.png" width="28" height="26" /></li>
					        <li data-feature="support"><img src="<?php echo base_url();?>assets/images/check.png" width="28" height="26" /></li>
					        <li data-feature="Backups"><img src="<?php echo base_url();?>assets/images/check.png" width="28" height="26" /></li>
					        <li data-feature="Webwinkel"></li>
					        <li data-feature="layouts"></li>
					        <li data-feature="voorbeeld"><a href="http://www.bestelonline.nu/chocolaterie/del-rey" target="_blank">Webwinkel op<br />
Bestelonline.nu</a></li>
            			</ul>
			            <?php if($curr_account_type->id == 2) { ?>
						  <a href="javascript: void(0);"><?php echo _('You have this package'); ?></a>
						  <?php } else { ?>
						  <a class="sign_up" href="javascript: void(0);" rel="2"><?php echo _('Upgrade Now'); ?></a>
						<?php } ?>
			            </div>
			          <div id="business" class="column gray">
			            <h1>PRO</h1>
			            <h2>29&euro;/mnd</h2>
			            <h3>Idem BASIC + <br />
						Webwinkel op uw website</h3>
            			<ul>
				          <li data-feature="Setupkosten">Geen</li>
					        <li data-feature="Vermelding"><img src="<?php echo base_url();?>assets/images/check.png" width="28" height="26" /></li>
					        <li data-feature="controlepaneel"><img src="<?php echo base_url();?>assets/images/check.png" width="28" height="26" /></li>
					        <li data-feature="Webwinkelbo"><img src="<?php echo base_url();?>assets/images/check.png" width="28" height="26" /></li>
					        <li data-feature="commissie"><img src="<?php echo base_url();?>assets/images/check.png" width="28" height="26" /></li>
					        <li data-feature="Mailingsysteem"><img src="<?php echo base_url();?>assets/images/check.png" width="28" height="26" /></li>
					        <li data-feature="Online betalen"><img src="<?php echo base_url();?>assets/images/check.png" width="28" height="26" /></li>
                            <li data-feature="Setupkosten"><img src="<?php echo base_url();?>assets/images/check.png" width="28" height="26" /></li>
					        <li data-feature="Updates">Gratis</li>
					        <li data-feature="Fraudcontrole"><img src="<?php echo base_url();?>assets/images/check.png" width="28" height="26" /></li>
					        <li data-feature="support"><img src="<?php echo base_url();?>assets/images/check.png" width="28" height="26" /></li>
					        <li data-feature="Backups"><img src="<?php echo base_url();?>assets/images/check.png" width="28" height="26" /></li>
					        <li data-feature="Webwinkel" class="desc-drop">Wij implementeren<div class="desc">de webwinkel in uw bestaande website en passen het uiterlijk aan aan uw huisstijl! Deze methode is UNIEK.</div></li>
					        <li data-feature="layouts"><img src="<?php echo base_url();?>assets/images/check.png" width="28" height="26" /></li>
					        <li data-feature="voorbeeld" class="desc-drop"><a href="http://www.delrey.be/new/nl/de-webshop/afhalen-verzending-be-nl/" target="_blank">Webwinkel<br />geïmplementeerd 
in website</a><br />
<a href="http://www.bestelonline.nu/chocolaterie/del-rey" target="_blank">Webwinkel op<br />
Bestelonline.nu</a></li>
            				</ul>
			            <?php if($curr_account_type->id == 3) { ?>
						  <a href="javascript: void(0);"><?php echo _('You have this package'); ?></a>
						  <?php } else { ?>
						  <a class="sign_up" href="javascript: void(0);" rel="3"><?php echo _('Upgrade Now'); ?></a>
						<?php } ?>
						</div>
						<div style="clear:both;"></div>
						<div id="infos" style="text-align: left;">
							(*) De klanten die bestellingen doorvoeren via deze éénvoudige formulier worden op de achtergrond gelinkt aan uw account.  Wanneer u upgrade naar een BASIC of PRO account kan u de gegevens van deze klanten raadplegen en eventueel mailen. U kan ten alle tijde dit bestelformulier aan- of uitschakelen in uw persoonlijk controlepaneel. Indien uitgeschakeld zullen uw klanten enkel de detailpagina zien en bestellingen kunnen doorgeven via nog via telefoon/fax.
						</div>
			        </div>
        
				  <div style="clear:both;"></div>
				  
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
                            <td colspan="2" style="text-align:center;"><input type="checkbox" name="agree" id="agree" value="1" checked="checked" />&nbsp;<?php echo _('I agree with the terms & conditions.'); ?></td>
                         </tr>
                         <tr><td colspan="2" style="text-align:center;"><input type="submit" name="upgrade_package" id="upgrade_package" class="pop-btn" value="<?php echo _('Order'); ?>" /></td></tr>
                      </table>
                      
                      </form>
					  
				  </div>
				  
				  <div style="clear:both;"></div>		  				  
		  
			</div>
			
		</div>
		</div>
		</div>