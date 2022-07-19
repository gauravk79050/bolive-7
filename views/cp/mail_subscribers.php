<link href="<?php echo base_url();?>assets/css/data_table.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/jqtable.js"></script>
<script type="text/javascript">
/* Custom filtering function which will filter data in column four between two values */
$(document).ready(function() {
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
		    				"<option value='2'>2</option>"+
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
		/*$.post(
				'<?php echo base_url();?>cp/mail_manager/get_mail_div',
				{'email':email},
				function(response){
					$("#send_mail_div").html(response);
					
				}
			);*/
		//tb_show("<?php echo _("Send quick mail")?>","#TB_inline?height=400&width=500&inlineId=send_mail_div","");
		tb_show("<?php echo _("Send quick mail")?>","#TB_inline?height=400&width=500&inlineId=send_mail_div","");
		//$("#to_id").html(email);
	//$("#to_id_hidden").val(email);
		
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
          <h3><?php echo _('Subscibers')?></h3>
          <div class="table" oncontextmenu="return false">
            <table id="records" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th width="1%" class="no_sorting">
							<input type="checkbox" name="check_all" id="check_all" value="all"/>
						</th>
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
					<?php if(!empty($subscribers)){?>
						<?php foreach($subscribers as $key => $values){?>
					<tr>
						<td width="1%">
							<input type="checkbox" name="row_delete[]" value="<?php echo $values['id'];?>" />
						</td>
						<td width="40%">
							<?php echo $values['lastname_c'].' '.$values['firstname_c'];?>
						</td>
						<td width="40%">
							<?php echo $values['email_c'];?>
						</td>
						<td width="19%">
							<a href="<?php echo base_url();?>cp/mail_manager/subscribers/<?php echo $values['id'];?>"><?php echo _("Edit");?></a>
							|
							<!-- <a href="javascript:void(0);" rel="<?php echo $values['email_c'];?>" class="send_mail"><?php echo _("Send Mail");?></a> -->
							<a href="<?php echo base_url();?>cp/mail_manager/get_mail_div/<?php echo urlencode($values['email_c']);?>?KeepThis=true&TB_iframe=true" class="send_mails thickbox"><?php echo _("Send Mail");?></a>
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