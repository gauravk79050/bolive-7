<link href="<?php echo base_url();?>assets/css/data_table.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/jqtable.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	var oTable = $('#records').dataTable({
		"sDom": '<"length"l>tp',
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

	$("#create").click(function(){
		window.location = "<?php echo base_url();?>cp/mail_manager/create_newsletters";
	});
});

function delete_newsLetter(id = null){
	if(id && confirm('<?php echo _("Do you really want to delete this News-Letter ?");?>')){
		window.location = '<?php echo base_url();?>cp/mail_manager/delete_new_letter/'+id;
	}
}

function send_me(newsletterId){
	//alert(newsletterId);
	$.post(
			base_url+'cp/mail_manager/send_newsletter_me',
			{'id':newsletterId},
			function(response){
				alert(response);
			}
		);
}

function send_newsletter(newsletterId){
	$.post(
			base_url+'cp/mail_manager/send_newsletter_all',
			{'id':newsletterId},
			function(response){
				alert(response);
			}
		);
}

</script>
<style>
#records th.no_sorting {
	background: none;
}
#records th.sort_70 {
	
}
#records th.sort_45 {
	background-position:50px 9px;
}

#records img {
	vertical-align: middle;
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
          <h3><?php echo _('Newsletters')?></h3>
          <div class="table">
            <table id="records" width="100%">
            	
				<thead>
					<tr>
						<th colspan="3" class="no_sorting">
							<input type="button" name="create" id="create" value="<?php echo _("Create Newsletters");?>"/>
						</th>
					</tr>
					<tr>
						<th width="1%" class="no_sorting">
							<input type="checkbox" name="check_all" id="check_all" value="all"/>
						</th>
						<th width="72%" class="sort_45">
							<?php echo _("Name");?>
						</th>
						<th width="27%" class="no_sorting">
							&nbsp;
						</th>
					</tr>
				</thead>
				<tbody>	
					<?php if(!empty($newsLetters)){?>
						<?php foreach($newsLetters as $key => $letters){?>
					<tr>
						<td width="1%">
							<input type="checkbox" name="row_delete[]" value="<?php echo $letters['id'];?>" />
						</td>
						<td width="72%">
							<?php echo $letters['name'];?>
						</td>
						<td width="27%">
							<a href="javascript:void(0);" onClick="send_me('<?php echo $letters['id'];?>')"><?php echo _("Mail Me"); ?></a>
							|
							<a href="javascript:void(0);" onClick="send_newsletter('<?php echo $letters['id'];?>')"><?php echo _("Send Newsletter"); ?></a>
							|
							<a href="<?php echo base_url();?>cp/mail_manager/create_newsletters/<?php echo $letters['id'];?>" ><img width="16" height="16" border="0" src="<?php echo base_url();?>assets/cp/images/edit.gif" alt="<?php echo _("Edit");?>"></a>
							|
							<a href="javascript:void(0);" onClick="delete_newsLetter('<?php echo $letters['id'];?>')"><img width="16" height="16" border="0" src="<?php echo base_url();?>assets/cp/images/delete.gif" alt="<?php echo _("Delete"); ?>"></a>
							|
							<a href="<?php echo base_url()?>cp/mail_manager/newsletter_clone/<?php echo $letters['id'];?>"><img width="16" height="16" border="0" src="<?php echo base_url();?>assets/cp/images/arrow-turn-180.png" alt="<?php echo _("Copy");?>"></a></td>
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
