<link rel="stylesheet" href="<?php echo base_url().'assets/mcp/thickbox/css/thickbox.css'?>" type="text/css"/>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/thickbox/javascript/thickbox.js"></script>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery(".show_thickbox").click(function(){
		var divId = jQuery(this).attr('rel');
		var subject = jQuery("subject_"+divId).html();
		tb_show( subject, "#TB_inline?height=500&amp;width=700&amp;inlineId=message_"+divId);
	});	
});

</script>

<style>
.stat_td{
	background-color: #F0F0F0;
    border-color: #003366;
    border-style: solid;
    border-width: 1px;
    font-family: Verdana,Arial,Helvetica,sans-serif;
    font-size: 12px;
    margin: 10px;
    /*width: 440px;*/
    padding: 5px;
}

.stat_td p{
	color: #003366;
	margin-bottom: 5px;
}

.stat_td .upper{
	margin-bottom: 15px;
	font-weight: bold;
}

.stat_td a {
	color: #003366;
	font-weight: bold;
}

.stat_td .link{
	margin: 10px 0 5px;
	text-align: right;
}

.stat_td .inline {
	display: inline-block;
	width: 140px;
}
</style>
<div style="width:100%">

<!-- start of main body -->

    <table width="100%" cellspacing="0" cellpadding="0" border="0">
	    <tr>
	       	<td valign="top" align="center">
	       
		       	<!-- Stats heading -->
		       	<table width="98%" cellspacing="0" cellpadding="0" border="2">            
					<tr>
			        	<td valign="top" align="center" style="border:#003366 1px solid; padding:15px 0px 0px 0px">
				 	    	<table width="98%" cellspacing="12" cellpadding="0" border="0">
				            	<tr>
				                	<td align="center" style="padding-bottom:10px" colspan="4">
										<table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/bg.jpg) left top repeat-x;" class="page_caption">
				                    		<tr>
				                            	<td height="30" align="left"><h3><?php echo _('Stats');?></h3></td>
				                        	</tr>
				                        </table>
									</td>
				            	</tr>

			 	        		<!-- START: Stats about Bestelonline.nu -->
			 	        		<tr>
				                	<td style="padding-bottom:10px" colspan="4">
										<table width="100%" cellspacing="0" cellpadding="0" border="0">
				                    		<tr>
				                            	<td height="30" align="left"><h3><?php echo _('BESTELONLINE.NU');?></h3></td>
				                        	</tr>
				                        </table>
									</td>
				            	</tr>
			                	<tr>
			                		<!-- LATEST FREE ORDER FROM BESTELONLINE.NU -->
			                    	<td class="stat_td" width="28%" valign="top">
								    	<?php if(!empty($latest_order_bo_free)){?>
								    	<?php $count = count($latest_order_bo_free); if($count > 10){unset($latest_order_bo_free[$count-1]); }?>
								     	<div class="upper"><?php echo _("Latest orders FREE");?></div>
								     	<table width="100%" cellspacing="5">
								     		<?php foreach ($latest_order_bo_free as $values){?>
								    		<tr>
								    			<td width="50%" valign="top">
								    				<span class="inline"><a href="<?php echo base_url();?>mcp/companies/update/<?php echo $values->company_id;?>"><?php echo $values->company_name;?></a></span>
								    				<br/>
								    				<span>(<?php echo date('M d', strtotime($values->created_date)).' '._("at")." ".date("H:i", strtotime($values->created_date))." "._("hr");?>)</span>
								    			</td> 
								    			<td width="18%" valign="top">
								    				<?php echo _("done by");?>
								    			</td>
								    			<td width="34%" valign="top">
								    				<?php echo $values->firstname_c.' '.$values->lastname_c;?>
									    		</td>
									    	</tr>
									    	<?php }?>
									    </table>
									    <?php if($count > 10){?>
									    <div class="link"><a href="<?php echo base_url();?>mcp/stats/orders_bo/free">...<?php echo _("more");?></a></div>
									    <?php }?>
									    <?php }else{?>
								    	<p class="empty"><?php echo _("No free orders from BESTELONLINE.NU yet!");?></p>
								    	<?php }?>
								 	</td>
								
									<!-- LATEST BASIC/PRO ORDER FROM BESTELONLINE.NU -->
								 	<td class="stat_td" width="28%" valign="top">
									    <?php if(!empty($latest_order_bo)){?>
									    <?php $count = count($latest_order_bo); if($count > 10){unset($latest_order_bo[$count-1]); }?>
									    <div class="upper"><?php echo _("Latest orders Basic/Pro");?></div>
									    <table width="100%" cellspacing="5">
									     	<?php foreach ($latest_order_bo as $values){?>
									    	<tr>
									    		<td width="50%" valign="top">
									    			<span class="inline"><a href="<?php echo base_url();?>mcp/companies/update/<?php echo $values->company_id;?>"><?php echo $values->company_name;?></a></span>
									    			<br/>
									    			<span>(<?php echo date('M d', strtotime($values->created_date)).' '._("at")." ".date("H:i", strtotime($values->created_date))." "._("hr");?>)</span>
									    		</td> 
									    		<td width="17%" valign="top">
									    			<?php echo _("done by");?>
									    		</td>
									    		<td width="34%" valign="top">
									    			<?php echo $values->firstname_c.' '.$values->lastname_c;?>
									    		</td>
									    	</tr>
									    	<?php }?>
									    </table>
									    <?php if($count > 10){?> 
									    <div class="link"><a href="<?php echo base_url();?>mcp/stats/orders_bo">...<?php echo _("more");?></a></div>
									    <?php }?>
									    <?php }else{?>
									    <p class="empty"><?php echo _("No orders from Bestelonline.nu yet!");?></p>
									    <?php }?>
								 	</td>
								
									<!-- LATEST MAIL SENT FROM BESTELONLINE.NU -->
								 	<td class="stat_td" width="24%" valign="top">
									    <?php if(!empty($latest_mails_sent_bo)){?>
									    <?php $count = count($latest_mails_sent_bo); if($count > 10){unset($latest_mails_sent_bo[$count-1]); }?>
									    <div class="upper"><?php echo _("Latest mails sent");?></div>
									    <table width="100%" cellspacing="5">
									     	<?php foreach ($latest_mails_sent_bo as $values){?>
									    	<tr>
									    		<td width="71%" valign="top">
									    			<span><?php echo _("To"); ?></span>
									    			<span> 
									    				<?php if($values->to_type == 'company'){ ?>
									    					<a href="<?php echo base_url();?>mcp/companies/update/<?php echo $values->company_id;?>"><?php echo $values->company_name;?></a>
									    				<?php }elseif($values->to_type == 'client'){ ?>
									    					<a href="javascript:void(0);"><?php echo $values->firstname_c.' '.$values->lastname_c;?></a>
									    				<?php }elseif($values->to_type == 'site_admin'){ ?>
									    					<a href="javascript:void(0);"><?php echo $this->config->item('site_admin_name');?></a>
									    				<?php }elseif($values->to_type == 'dep'){ ?>
									    					<a href="javascript:void(0);"><?php echo $values->dep_first_name.' '.$values->dep_last_name;?></a>
									    				<?php }elseif($values->to_type == 'partner'){ ?>
									    					<a href="javascript:void(0);"><?php echo $values->p_first_name.' '.$values->p_last_name;?></a>
									    				<?php }elseif($values->to_type == 'affiliate'){ ?>
									    					<a href="javascript:void(0);"><?php echo $values->a_first_name.' '.$values->a_last_name;?></a>
									    				<?php }?>
									    			</span>
									    			<br/>
									    			<span>(<?php echo date('M d', strtotime($values->datetime)).' '._("at")." ".date("H:i", strtotime($values->datetime))." "._("hr");?>)</span>
									    		</td> 
									    		<td width="27%" valign="top">
									    			-<a href="javascript:void(0);" rel="<?php echo $values->id;?>" class="show_thickbox"><?php echo _("see mail")?></a>
									    			<div id="subject_<?php echo $values->id;?>" style="display:none;"><?php echo $values->subject;?></div>
									    			<div id="message_<?php echo $values->id;?>" style="display:none;"><?php echo $values->message;?></div>
									    		</td>
									    	</tr>
									    	<?php }?>
									    </table>
									    <?php if($count > 10){?>
									    <div class="link"><a href="<?php echo base_url();?>mcp/stats/latest_mail_sent/bo">...<?php echo _("more");?></a></div>
									    <?php }?>
									    <?php }else{?>
									    <p class="empty"><?php echo _("No latest mail sent yet!");?></p>
									    <?php }?>
								 	</td>
								 	<td>
								 		&nbsp;
								 	</td>
		                		</tr>
		                		<!-- END: Stats about Bestelonline.nu -->
			 	        	
			 	        		<!-- START: Stats about OBS -->
			                	<tr>
				                	<td style="padding-bottom:10px" colspan="4">
										<table width="100%" cellspacing="0" cellpadding="0" border="0">
				                    		<tr>
				                            	<td height="30" align="left"><h3><?php echo _('OBS SHOPS');?></h3></td>
				                        	</tr>
				                        </table>
									</td>
				            	</tr>
			
			                 	<tr>
			                 		<!-- LATEST ORDER FROM OBS SHOPS -->
			                    	<td class="stat_td" width="28%" valign="top">
								    	<?php if(!empty($latest_order_obs)){?>
								    	<?php $count = count($latest_order_obs); if($count > 10){unset($latest_order_obs[$count-1]); }?>
								     	<div class="upper"><?php echo _("Latest orders only via Shops");?></div>
								     	<table width="100%" cellspacing="5">
								     		<?php foreach ($latest_order_obs as $values){?>
								    		<tr>
								    			<td width="50%" valign="top">
									    			<span class="inline"><a href="<?php echo base_url();?>mcp/companies/update/<?php echo $values->company_id;?>"><?php echo $values->company_name;?></a></span>
									    			<br/>
									    			<span>(<?php echo date('M d', strtotime($values->created_date)).' '._("at")." ".date("H:i", strtotime($values->created_date))." "._("hr");?>)</span>
									    		</td> 
									    		<td width="18%" valign="top">
									    			<?php echo _("done by");?>
									    		</td>
									    		<td width="34%" valign="top">
									    			<?php echo $values->firstname_c.' '.$values->lastname_c;?>
									    		</td>
									    	</tr>
								    		<?php }?>
								     	</table>
								     	<?php if($count > 10){?>
								    	<div class="link"><a href="<?php echo base_url();?>mcp/stats/orders_obs">...<?php echo _("more");?></a></div>
								    	<?php }?>
								    	<?php }else{?>
								    	<p class="empty"><?php echo _("No orders from OBSshop yet!");?></p>
								    	<?php }?>
								 	</td>
									
									<!-- LATEST MAILS SENT FROM OBS -->
								 	<td class="stat_td" width="24%" valign="top">
									    <?php if(!empty($latest_mails_sent)){?>
									    <?php $count = count($latest_mails_sent); if($count > 10){unset($latest_mails_sent[$count-1]); }?>
									    <div class="upper"><?php echo _("Latest mails sent");?></div>
									    <table width="100%" cellspacing="5">
									     	<?php foreach ($latest_mails_sent as $values){?>
									    	<tr>
									    		<td width="71%" valign="top">
									    			<span><?php echo _("To"); ?></span>
									    			<span> 
									    				<?php if($values->to_type == 'company'){ ?>
									    					<a href="<?php echo base_url();?>mcp/companies/update/<?php echo $values->company_id;?>"><?php echo $values->company_name;?></a>
									    				<?php }elseif($values->to_type == 'client'){ ?>
									    					<a href="javascript:void(0);"><?php echo $values->firstname_c.' '.$values->lastname_c;?></a>
									    				<?php }elseif($values->to_type == 'site_admin'){ ?>
									    					<a href="javascript:void(0);"><?php echo $this->config->item('site_admin_name');?></a>
									    				<?php }elseif($values->to_type == 'dep'){ ?>
									    					<a href="javascript:void(0);"><?php echo $values->dep_first_name.' '.$values->dep_last_name;?></a>
									    				<?php }elseif($values->to_type == 'partner'){ ?>
									    					<a href="javascript:void(0);"><?php echo $values->p_first_name.' '.$values->p_last_name;?></a>
									    				<?php }elseif($values->to_type == 'affiliate'){ ?>
									    					<a href="javascript:void(0);"><?php echo $values->a_first_name.' '.$values->a_last_name;?></a>
									    				<?php }?>
									    			</span>
									    			<br/>
									    			<span>(<?php echo date('M d', strtotime($values->datetime)).' '._("at")." ".date("H:i", strtotime($values->datetime))." "._("hr");?>)</span>
									    		</td> 
									    		<td width="27%" valign="top">
									    			<span><?php echo $values->to_type; ?></span>
									    			<br/>
									    			-<a href="javascript:void(0);" rel="<?php echo $values->id;?>" class="show_thickbox"><?php echo _("see mail")?></a>
									    			<div id="subject_<?php echo $values->id;?>" style="display:none;"><?php echo $values->subject;?></div>
									    			<div id="message_<?php echo $values->id;?>" style="display:none;"><?php echo $values->message;?></div>
									    		</td>
									    	</tr>
									    	<?php }?>
									    </table>
									    <?php if($count > 10){?>
									    <div class="link"><a href="<?php echo base_url();?>mcp/stats/latest_mail_sent">...<?php echo _("more");?></a></div>
									    <?php }?>
									    <?php }else{?>
									    <p class="empty"><?php echo _("No latest mail sent yet!");?></p>
									    <?php }?>
								 	</td>
								
									<!-- TOP ORDER COMPANIES LAST 30 DAYS -->
									<td class="stat_td" width="20%" valign="top">
										<?php if(!empty($top_order_company_last_30_days)){?>
										<?php $count = count($top_order_company_last_30_days); if($count > 10){unset($top_order_company_last_30_days[$count-1]); }?>
									    <div class="upper"><?php echo _("Latest top 30 days orders");?></div>
									    <table width="100%" cellspacing="5">
									     	<?php foreach ($top_order_company_last_30_days as $values){?>
									    	<tr>
									    		<td width="71%" valign="top">
									    			<span class="inline"><a href="<?php echo base_url();?>mcp/companies/update/<?php echo $values->company_id;?>"><?php echo $values->company_name;?></a></span>
									    		</td> 
									    		<td width="27%" valign="top">
									    			-- <?php echo $values->number;?>
									    		</td>
									    	</tr>
									    	<?php }?>
									    </table>
									    <?php if($count > 10){?>
									    <div class="link"><a href="<?php echo base_url();?>mcp/stats/top_order_company/30">...<?php echo _("more");?></a></div>
									    <?php }?>
									    <?php }else{?>
									    <p class="empty"><?php echo _("No record yet!");?></p>
									    <?php }?>
									</td>
									
									<!-- TOP ORDER COMPANIES WHOLE -->
									<td class="stat_td" width="20%" valign="top">
										<?php if(!empty($top_order_company)){?>
										<?php $count = count($top_order_company); if($count > 10){unset($top_order_company[$count-1]); }?>
									    <div class="upper"><?php echo _("Latest top orders");?></div>
									    <table width="100%" cellspacing="5">
									     	<?php foreach ($top_order_company as $values){?>
									    	<tr>
									    		<td width="71%" valign="top">
									    			<span class="inline"><a href="<?php echo base_url();?>mcp/companies/update/<?php echo $values->company_id;?>"><?php echo $values->company_name;?></a></span>
									    		</td> 
									    		<td width="27%" valign="top">
									    			-- <?php echo $values->number;?>
									    		</td>
									    	</tr>
									    	<?php }?>
									    </table>
									    <?php if($count > 10){?>
									    <div class="link"><a href="<?php echo base_url();?>mcp/stats/top_order_company">...<?php echo _("more");?></a></div>
									    <?php }?>
									    <?php }else{?>
									    <p class="empty"><?php echo _("No record yet!");?></p>
									    <?php }?>
									</td>
		                		</tr>
		                		<tr>
		                			<!-- LAST LOGIN COMAPNIES -->
		                			<td class="stat_td" width="20%" valign="top">
										<?php if(!empty($last_login_companies)){?>
										<?php $count = count($last_login_companies); if($count > 10){unset($last_login_companies[$count-1]); }?>
									    <div class="upper"><?php echo _("Latest login in CP");?></div>
									    <table width="100%" cellspacing="5">
									     	<?php foreach ($last_login_companies as $values){?>
									    	<tr>
									    		<td width="50%" valign="top">
									    			<span class="inline"><a href="<?php echo base_url();?>mcp/companies/update/<?php echo $values->company_id;?>"><?php echo $values->company_name;?></a></span>
									    		</td> 
									    		<td width="50%" valign="top">
									    			 <?php echo date('M d', strtotime($values->last_login)).' '._('at').' '.date('H:i', strtotime($values->last_login));?>  
									    		</td>
									    	</tr>
									    	<?php }?>
									    </table>
									    <?php if($count > 10){?>
									    <div class="link"><a href="<?php echo base_url();?>mcp/stats/last_login_company">...<?php echo _("more");?></a></div>
									    <?php }?>
									    <?php }else{?>
									    <p class="empty"><?php echo _("No record yet!");?></p>
									    <?php }?>
									</td>
									<td colspan="3" valign="top">
									    &nbsp;
									</td>
		                		</tr>
							</table>
					 	</td>
				  	</tr>
			  	</table>
		  	</td>
	  	</tr>
  	</table>
</div>              
<br />				