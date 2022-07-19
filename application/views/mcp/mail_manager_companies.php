<link rel="stylesheet" href="<?php echo base_url();?>assets/cp/js/thickbox/css/thickbox.css" type="text/css"/>
<script type="text/javascript" src="<?php echo base_url();?>assets/cp/js/thickbox/javascript/thickbox.js"></script>
<link href="<?php echo base_url();?>assets/css/data_table.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/jqtable.js"></script>
<script type="text/javascript">
/* Custom filtering function which will filter data in column four between two values */
jQuery(document).ready(function($) {
	/* Initialise datatables */
	var oTable = $('#records').dataTable({
		"sDom": '<"l_f"lf>tp',
		"aaSorting": [[ 1, "asc" ]],
		"oLanguage": {
			"oPaginate": {
		        "sFirst": "<?php echo _('First page'); ?>",
		        "sLast": "<?php echo _('Last page'); ?>",
		        "sNext": "<?php echo _('Next page'); ?>",
		        "sPrevious": "<?php echo _('Previous page'); ?>"
		      },
		    "sSearch": "<?php echo _('Filter records'); ?>:",
		    "sLengthMenu": "<?php echo _('Records on Page'); ?><select style='display: inline;'>"+
					        "<option value='10'>10</option>"+
					        "<option value='20'>20</option>"+
					        "<option value='30'>30</option>"+
					        "<option value='40'>40</option>"+
					        "<option value='50'>50</option>"+
					        "<option value='-1'>All</option>"+
					        "</select>"
		    }
		});

	$(".send_mail").live('click', function(){
		var email = $(this).attr("rel");
	
		tb_show("<?php echo _("Send quick mail")?>","#TB_inline?height=400&width=500&inlineId=send_mail_div","");
		
		
	});

	$(".edit_subscriptions").live('click',function(){
		var company_id	= $(this).attr('rel');
		if(company_id){
			tb_show('Details','#TB_inline?height=290&width=400&inlineId=show_subscr_'+company_id,'');
		}
		return false;
	});

	$(".submit_subscr").live('click', function(){
		var company_id = $(this).parents('.show_subscr').attr('rel');
		var is_subscribed = $(this).parents('.show_subscr').find('.subscription').is(':checked')?'subscribed':'unsubscribed';
		jQuery.ajax({
			url: '<?php echo base_url();?>mcp/mail_manager/companies/subscribed/'+company_id,
			type:'POST',
			//dataType: 'json',
			data:{
					'is_subscribed': is_subscribed
				},
			success: function(response){
				if(response == 'success'){
					alert("<?php echo _('Record Updated Successfully.');?> ");
					window.location.reload();
				}
				else{
					alert("<?php echo _('Some error occurred. Please Try again.');?> ");
				}
			}
		});
	});
} );

</script>
<style>
#records th.no_sorting {
	background: none;
}
#records th.sort_70 {
	
}
#records th.sort_45 {
	
}
</style>
  <!-- MAIN -->
  <div id="main">
	<div id="content">
      <div id="content-container">
        <div class="box" style="width: 98%">
          <h3><?php echo _('Companies')?></h3>
          <div class="links">
          	<a href="<?php echo base_url().'mcp/mail_manager/companies/subscribed'?>"> <?php echo _('Subscribers');?> (<?php echo $count_subscribed?>)</a>
          	|
          	<a href="<?php echo base_url().'mcp/mail_manager/companies/unsubscribed'?>"> <?php echo _('Unsubscribers');?> (<?php echo $count_unsubscribed?>)</a>
          	|
          	<a href="<?php echo base_url().'mcp/mail_manager/companies/bounced'?>"> <?php echo _('Bounced');?> (<?php echo $count_bounced?>)</a>
          	<a style="float: right;" href="<?php echo base_url().'mcp/mail_manager/companies/subscription_list/'?>"> <?php echo _('Download Subscription List');?></a>|
          </div>
          <div class="clear"></div>
          <div class="table" oncontextmenu="return false">
            <table id="records" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th width="40%" class="sort_70">
							<?php echo _("Full Name");?>
						</th>
						<th width="40%" class="sort_45">
							<?php echo _("Email");?>
						</th>
						<th width="19%" class="no_sorting">
							<?php echo _("Action");?>
						</th>
					</tr>
				</thead>
				<tbody>	
					<?php if(!empty($companies)){?>
						<?php foreach($companies as $key => $values){?>
					<tr>
						<td width="40%">
							<?php echo $values['company_name'];?>
						</td>
						<td width="40%">
							<?php echo $values['email'];?>
						</td>
						<td width="19%">
							<a href="javascript:void(0);" class="edit_subscriptions" rel="<?php  echo $values['id']; ?>"><?php echo _("Edit");?></a>
							|
							<a href="<?php echo base_url();?>mcp/mail_manager/get_mail_div/<?php echo urlencode($values['email']);?>?KeepThis=true&TB_iframe=true" class="send_mails thickbox"><?php echo _("Send Mail");?></a>
							<div id="show_subscr_<?php echo $values['id']; ?>" style="display:none;">
								<table class="show_subscr" rel="<?php echo $values['id']; ?>" width="100%" cellspacing="8" cellpadding="0" border="0">
									<tbody>
									<tr>
										<td class="td_left">
											<strong><?php echo _('Subscribed'); ?></strong>
										</td>
										<td class="td_right"><input type="checkbox" name="subscription" class="subscription" <?php echo ($values['mail_subscription'] == 'subscribed'?'checked="checked"':'');?>></td>
										<td><input type="button" class="submit_subscr" name="submit_subscr" value="<?php echo _('Submit');?>" /></td>
									</tr>
									</tbody>
								</table>
								<div class="thickbox_footer">
									<div class="thickbox_footer_text">
									</div>
								</div>
							</div>
						</td>
					</tr>
						<?php }?>
					<?php }?>
				</tbody>
				
			</table>
          </div>
          
        </div>
      </div>
    </div>
    <!-- /content -->
<div id="send_mail_div"></div>