  <!-- MAIN -->
  <div id="main">
    <div id="main-header">
      <h2><?php echo _('Mail Manager')?></h2>
      <span class="breadcrumb"><a href="<?php echo base_url()?>cp/cdashboard"><?php echo _('Home')?></a> &raquo; <?php echo('Customer')?></span>
	</div>
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
          <h3><?php echo _("Subsciber's Details")?></h3>
          <div class="table">
            <table id="records" cellspacing="0" width="100%">
				<tbody>	
					<?php if(!empty($subscriber)){?>
					<tr>
						<td width="30%">
							<?php echo _("Full Name");?>
						</td>
						<td width="70%">
							<?php echo $subscriber['0']['lastname_c']." ".$subscriber['0']['firstname_c'];?>
						</td>
					</tr>
					<tr>
						<td width="30%">
							<?php echo _("Email");?>
						</td>
						<td width="70%">
							<?php echo $subscriber['0']['email_c'];?>
						</td>
					</tr>
					<tr>
						<td width="30%">
							<?php echo _("System Status");?>
						</td>
						<td width="70%">
							<?php echo ( ($subscriber['0']['newsletters'] == "subscribe")?_("Subscribe"):_("Unsubscribe"));?>
						</td>
					</tr>
					<?php }?>
				</tbody>
				
			</table>
			
          </div>
        </div>
      </div>
    </div>
    <!-- /content -->
