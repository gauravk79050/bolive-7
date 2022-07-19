<script type="text/javascript">
	jQuery(".make_order").live("click",function(){
		jQuery.post(
					'<?php echo base_url();?>cp/mail_manager/place_order',
					{'credits':jQuery(this).attr("rel")},
					function(response){
						alert(response);
					}
				);
	});

	jQuery(".make_order_monthly").live("click",function(){
		jQuery.post(
					'<?php echo base_url();?>cp/mail_manager/buy_monthly',
					{'credits':0},
					function(response){
						alert(response);
					}
				);
	});
	
</script>
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/cp/new_css/mail_dashboard.css">

<div id="main">
	<div id="main-header">
		<h2><?php echo _('Client Management')?></h2>
		<span class="breadcrumb"><a
			href="<?php echo base_url();?>cp/cdashboard"><?php echo _('Home')?></a> &raquo; <?php echo('Customer')?></span>
	</div>
		<?php $messages = $this->messages->get();?>
		<?php if(is_array($messages)):?>
		<?php foreach($messages as $key=>$val):?>
		<?php if($val != array()):?>
		<div id="succeed_order_update" class="<?php echo $key;?>"><?php echo $val[0];?></div>
		<?php endif;?>
		<?php endforeach;?>
		<?php endif;?>

		<?php if($this->session->flashdata('success')):?>
		<div id="succeed_order_update" class="success"><?php echo $this->session->flashdata('success');?></div>
		<?php endif;?>
		
		<?php if($this->session->flashdata('error')):?>
		<div id="succeed_order_update" class="error"><?php echo $this->session->flashdata('error');?></div>
		<?php endif;?>

	<div id="content">
		<div id="content-container">
			<div class="box">
				<h3><?php echo _("Overview"); ?></h3>
				<div class="inside table">
					<div class="box_wrap">
						<p><?php echo _("Do you want to keep your clients up-to-date on holidays,promotions etc? then use our advanced e-mailmarketing tool!"); ?></p>
						<p><a href='#TB_inline?height=155&width=300&inlineId=why_pay_div' class="pop_link thickbox" id="why_pay_a" ><input id="why_pay_button" type="button" value="<?php echo _("Why Pay?");?>"></a></p>
						<div class="mail_field">
							<h2 style="font-size: 17px; margin-bottom: 15px;"><?php echo _("Send unlimited mails to your first 20 clients for FREE");?></h2>
							<div class="left_up left_part">
								
								<table class="table_0" cellpadding="0" cellspacing="0">
									<thead>
										<tr>
											<td><?php echo _("Clients");?></td>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td><?php echo _("Price/month");?></td>
										</tr>
									</tbody>
								</table>
								
								<table class="table_1" cellpadding="0" cellspacing="0">
									<thead>
										<tr>
											<td><20</td>
											<td><500</td>
											<td><1000</td>
											<td><5000</td>
											<td><10000</td>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td><?php echo _("FREE");?></td>
											<td>10 &euro;</td>
											<td>20 &euro;</td>
											<td>50 &euro;</td>
											<td>75 &euro;</td>
										</tr>
										<tr>
											<td colspan="5"><a href="#TB_inline?height=155&width=300&inlineId=confirm_monthly" class="pop_link thickbox"><input type="button" value="<?php echo _("SELECT");?>"></a>
											</td>
										</tr>
									</tbody>
								</table>
								<div class="clear"></div>
							</div>
							<div class="clear"></div>
						</div>
						<hr>
						<div class="mail_field">
							<h2 style="font-size: 17px; margin-bottom: 15px;"><?php echo _("Use credits instead of per month basis");?></h2>
							<div class="content">
								<p><?php echo _("Are you not planning to sent mails on regular basis, then you can but credits. 1 credit is the same as sending 1 mail. Credits do not have due dates.");?></p>
							</div>
							<div class="left_down left_part">
								<table class="table_0" cellpadding="0" cellspacing="0">
									<thead>
										<tr>
											<td><?php echo _("Amount CREDITS");?></td>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td><?php echo _("Price");?></td>
										</tr>
									</tbody>
								</table>
								
								<table class="table_1" cellpadding="0" cellspacing="0">
									<thead>
										<tr>
											<td>1000</td>
											<td>5000</td>
											<td>10000</td>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>20 &euro;</td>
											<td>50 &euro;</td>
											<td>75 &euro;</td>
										</tr>
										<tr>
											<td><a href="#TB_inline?height=155&width=300&inlineId=credit_20" class="pop_link thickbox"><input type="button" value="<?php echo _("SELECT");?>"></a></td>
											<td><a href="#TB_inline?height=155&width=300&inlineId=credit_50" class="pop_link thickbox"><input type="button" value="<?php echo _("SELECT");?>"></a></td>
											<td><a href="#TB_inline?height=155&width=300&inlineId=credit_75" class="pop_link thickbox"><input type="button" value="<?php echo _("SELECT");?>"></a></td>
										</tr>
									</tbody>
								</table>
								<div class="clear"></div>
							</div>
							<div class="clear"></div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>

	<div class="right_down right_part">

		<div id="credit_20" class="pop_up_down popup_pay"
			style="display: none;">
			<h4><?php echo _("Are you sure you want to purchase this"); ?>?</h4>
			<p><?php echo _("We will be notified by mail and we will generate an invoice and send it to you");?></p>
			<p><?php echo _("We will update amount of credits after you clicked on the confirm button below");?></p>
			<p style="text-align: center;">
				<a href="javascript: void(0);" class="make_order" rel="1000"><?php echo _("confirm the order"); ?></a>
			</p>
		</div>
		<div id="credit_50" class="pop_up_down popup_pay"
			style="display: none;">
			<h4><?php echo _("Are you sure you want to purchase this"); ?>?</h4>
			<p><?php echo _("We will be notified by mail and we will generate an invoice and send it to you");?></p>
			<p><?php echo _("We will update amount of credits after you clicked on the confirm button below");?></p>
			<p style="text-align: center;">
				<a href="javascript: void(0);" class="make_order" rel="5000"><?php echo _("confirm the order"); ?></a>
			</p>
		</div>
		<div id="credit_75" class="pop_up_down popup_pay"
			style="display: none;">
			<h4><?php echo _("Are you sure you want to purchase this"); ?>?</h4>
			<p><?php echo _("We will be notified by mail and we will generate an invoice and send it to you");?></p>
			<p><?php echo _("We will update amount of credits after you clicked on the confirm button below");?></p>
			<p style="text-align: center;">
				<a href="javascript: void(0);" class="make_order" rel="10000"><?php echo _("confirm the order"); ?></a>
			</p>
		</div>

		<div id="why_pay_div" class="up_pop_up popup_pay"
			style="display: none;">
			<h4><?php echo _("why pay for this");?>?</h4>
			<p><?php echo _("As you may(be) know, some risks are involved when sending bulk mails like");?>:</p>
			<p>-<?php echo _("Your IP can be blocked from span detectors so all of your mails would instantly be marked as spam.");?></p>
			<p>-<?php echo _("Issues/Restrictions with your provider");?>,</p>
			<br />
			<p><?php echo _("As we use our own dynamic IPs, you never have to be worried again for blacklisting, etc. Obviously we have to maintain these IPs and we aswel have to pay money to maintain them... But don't wory as our margin here is nil. We can proudly say that we are one of the cheapest mail");?></p>
		</div>
		
		<div id="confirm_monthly" class="pop_up_down popup_pay"
			style="display: none;">
			<h4><?php echo _("Are you sure you want to purchase this"); ?>?</h4>
			<p><?php echo _("We will be notified by mail and we will generate an invoice and send it to you");?></p>
			<p><?php echo _("We will update amount of credits after you clicked on the confirm button below");?></p>
			<p style="text-align: center;">
				<a href="javascript: void(0);" class="make_order_monthly" rel="00"><?php echo _("confirm the order"); ?></a>
			</p>
		</div>
<!--		<div id="confirm_monthly_500" class="pop_up_down popup_pay"
			style="display: none;">
			<h4><?php echo _("Are you sure you want to purchase this"); ?>?</h4>
			<p><?php echo _("We will be notified by mail and we will generate an invoice and send it to you");?></p>
			<p><?php echo _("We will update amount of credits after you clicked on the confirm button below");?></p>
			<p style="text-align: center;">
				<a href="javascript: void(0);" class="make_order_monthly" rel="500"><?php echo _("confirm the order"); ?></a>
			</p>
		</div>
		<div id="confirm_monthly_1000" class="pop_up_down popup_pay"
			style="display: none;">
			<h4><?php echo _("Are you sure you want to purchase this"); ?>?</h4>
			<p><?php echo _("We will be notified by mail and we will generate an invoice and send it to you");?></p>
			<p><?php echo _("We will update amount of credits after you clicked on the confirm button below");?></p>
			<p style="text-align: center;">
				<a href="javascript: void(0);" class="make_order_monthly" rel="1000"><?php echo _("confirm the order"); ?></a>
			</p>
		</div>
		<div id="confirm_monthly_5000" class="pop_up_down popup_pay"
			style="display: none;">
			<h4><?php echo _("Are you sure you want to purchase this"); ?>?</h4>
			<p><?php echo _("We will be notified by mail and we will generate an invoice and send it to you");?></p>
			<p><?php echo _("We will update amount of credits after you clicked on the confirm button below");?></p>
			<p style="text-align: center;">
				<a href="javascript: void(0);" class="make_order_monthly" rel="5000"><?php echo _("confirm the order"); ?></a>
			</p>
		</div>
		<div id="confirm_monthly_10000" class="pop_up_down popup_pay"
			style="display: none;">
			<h4><?php echo _("Are you sure you want to purchase this"); ?>?</h4>
			<p><?php echo _("We will be notified by mail and we will generate an invoice and send it to you");?></p>
			<p><?php echo _("We will update amount of credits after you clicked on the confirm button below");?></p>
			<p style="text-align: center;">
				<a href="javascript: void(0);" class="make_order_monthly" rel="10000"><?php echo _("confirm the order"); ?></a>
			</p>
		</div> -->

	</div>
	<!-- /content -->