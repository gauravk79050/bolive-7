<link href="<?php echo base_url();?>assets/css/data_table.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/jqtable.js"></script>
<script type="text/javascript">
jQuery(document).ready(function($){
	var oTable = $('#records').dataTable({
		"sDom": '<"l_f"l>tp',
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
		window.location = "<?php echo base_url();?>mcp/mail_manager/create_templates";
	});
});

function delete_template(id = null){
	if(id && confirm('<?php echo _("Do you really want to delete this Template ?");?>')){
		window.location = '<?php echo base_url();?>mcp/mail_manager/delete_template/'+id;
	}
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
	<div id="content">
      <div id="content-container">
        <div class="box" style="width: 98%;">
          <h3><?php echo _('Templates')?></h3>
          <div class="table">
            <table id="records" width="100%">
            	
				<thead>
					<tr>
						<th colspan="3" class="no_sorting">
							<input type="button" name="create" id="create" value="<?php echo _("Create Templates");?>"/>
						</th>
					</tr>
					<tr>
						<th width="92%" class="sort_45">
							<?php echo _("Name");?>
						</th>
						<th width="7%" class="no_sorting">
							&nbsp;
						</th>
					</tr>
				</thead>
				<tbody>	
					<?php if(!empty($templates)){?>
						<?php foreach($templates as $key => $letters){?>
					<tr>
						<td width="92%">
							<?php echo $letters['name'];?>
						</td>
						<td width="7%">
							<a href="<?php echo base_url();?>mcp/mail_manager/create_templates/<?php echo $letters['id'];?>" ><img width="16" height="16" border="0" src="<?php echo base_url();?>assets/cp/images/edit.gif" alt="<?php echo _("Edit");?>"></a>
							|
							<a href="javascript: delete_template(<?php echo $letters['id'];?>);" ><img width="16" height="16" border="0" src="<?php echo base_url();?>assets/cp/images/delete.gif" alt="<?php echo _("Delete");?>"></a>
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
