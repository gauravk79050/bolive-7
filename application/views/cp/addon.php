<!-- MAIN -->
<script>
	$(document).ready(function(){
		$(".activate").click(function(){
			$.ajax({
				url: '<?php echo base_url();?>cp/cdashboard/activate_deactivate_addon',
				data: {addon_id: $(this).attr('rel'), 'action': 'activate'},
				type: 'POST',
				success: function(data){
					if(data){
						alert("<?php echo _("Your request has been send to the Administration. You will be notify soon.");?>");
					}else{
						alert("<?php echo _("Some error occured while sending your request. Please try again.");?>");
					}
				}
			});
		});

		$(".deactivate").click(function(){
			$.ajax({
				url: '<?php echo base_url();?>cp/cdashboard/activate_deactivate_addon',
				data: {addon_id: $(this).attr('rel'), 'action': 'deactivate'},
				type: 'POST',
				success: function(data){
					if(data){
						alert("<?php echo _("Your request has been send to the Administration. You will be notify soon.");?>");
					}else{
						alert("<?php echo _("Some error occured while sending your request. Please try again.");?>");
					}
				}
			});
		});
	});
</script>
<div id="main">
	<div id="main-header">
    	<h2><?php echo _('Addon Managment')?></h2>
      	<span class="breadcrumb"><a href="<?php echo base_url()?>cp/cdashboard"><?php echo _('Home')?></a> &raquo; <?php echo _('Addons Managment')?></span> </div>
    <div id="content">
    	<div id="content-container">
        	<div class="box">
          		<h3><?php echo _('Addons')?></h3>
          		<div class="inside">
              		<p><?php echo _("Right here you can activate addons you wish. Click on the activate button and the feature activated will be available. If you are interested to have them all activated then ");?><a href="#"><?php echo _("notify us here");?></a><?php echo _(" and you will get all addons get active for just 49");?>&euro;<?php echo _("/mnth! If you want to set an addon inactive to reduce the monthly price then notify us.");?></p>
              		
              		<div class="addon_list">
              			<?php if(!empty($total_addons)){?>
              			<?php foreach($total_addons as $addons){?>
              			<div class="addon_inside">
              				<div class="addon_content">
              					<div class="addon_content_left">
              						<div class="addon_header">
		              					<h2><?php echo $addons->addon_title; ?>  <span>(<?php echo $addons->addon_price; ?>&euro;/mnth)</span></h2>
		              				</div>
              						<p><?php echo $addons->addon_description; ?></p>
              					</div>
              					<div class="addon_content_right">
              						<?php if(in_array($addons->addon_id,$activated_addons)){?>
              						<span class="active"><?php echo _("Active");?></span><br />
              						<a href="javascript: void(0);">
              							<span class="deactivate" rel="<?php echo $addons->addon_id?>"><?php echo _("Deactivate");?></span>
              							<div style="clear: both;"></div>
              						</a>
              						<?php }else{?>
              						<span class="inactive"><?php echo _("Not Active");?></span><br />
              						<a href="javascript: void(0);">
              							<span class="activate" rel="<?php echo $addons->addon_id?>"><?php echo _("Activate");?></span>
              							<div style="clear: both;"></div>
              						</a>
              						<?php }?>
              					</div>
              				</div>
              				<div style="clear: both;"></div>
              			</div>
              			<?php }?>
              			<?php }?>
              		</div>
          		</div>
        	</div>
      	</div>
    </div>
    <!-- /content -->
