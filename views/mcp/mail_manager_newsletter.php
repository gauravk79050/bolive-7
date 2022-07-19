<link rel="stylesheet" href="<?php echo base_url();?>assets/cp/js/thickbox/css/thickbox.css" type="text/css"/>
<script type="text/javascript" src="<?php echo base_url();?>assets/cp/js/thickbox/javascript/thickbox.js"></script>
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
		window.location = "<?php echo base_url();?>mcp/mail_manager/create_newsletters";
	});
});

function delete_newsLetter(id = null){
	if(id && confirm('<?php echo _("Do you really want to delete this News-Letter ?");?>')){
		window.location = '<?php echo base_url();?>mcp/mail_manager/delete_new_letter/'+id;
	}
}

function send_me(newsletterId){
	//alert(newsletterId);
	jQuery.post(
			base_url+'mcp/mail_manager/send_newsletter_me',
			{'id':newsletterId},
			function(response){
				alert(response);
			}
		);
}

function send_newsletter(newsletterId){

	jQuery('#submit_select').attr('onClick','javascript: send_newsletter_all('+newsletterId+');');
	tb_show('<?php echo _('Select Company type');?>','#TB_inline?width=130&height=110&inlineId=select_company_type_div');
	
	/*jQuery.post(
			base_url+'mcp/mail_manager/send_newsletter_all',
			{'id':newsletterId},
			function(response){
				alert(response);
			}
		);*/
}

function send_newsletter_all(newsletterId){

	var selectedType = new Array();
	jQuery('.c_type').each(function(){
		if(jQuery(this).is(':checked')){
			selectedType.push(jQuery(this).val());
		}
	});

	if(selectedType.length){
		jQuery.post(
				base_url+'mcp/mail_manager/send_newsletter_all',
				{'id':newsletterId,'types':selectedType},
				function(response){
					alert(response);
				}
			);
	}else{
		alert("<?php echo _('Please choose any company type');?>");
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
						<td width="72%">
							<?php echo $letters['name'];?>
						</td>
						<td width="27%">
							<a href="javascript:void(0);" onClick="send_me('<?php echo $letters['id'];?>')"><?php echo _("Mail Me"); ?></a>
							|
							<a href="javascript:void(0);" onClick="send_newsletter('<?php echo $letters['id'];?>')"><?php echo _("Send Newsletter"); ?></a>
							|
							<a href="<?php echo base_url();?>mcp/mail_manager/create_newsletters/<?php echo $letters['id'];?>" ><img width="16" height="16" border="0" src="<?php echo base_url();?>assets/cp/images/edit.gif" alt="<?php echo _("Edit");?>"></a>
							|
							<a href="javascript:void(0);" onClick="delete_newsLetter('<?php echo $letters['id'];?>')"><img width="16" height="16" border="0" src="<?php echo base_url();?>assets/cp/images/delete.gif" alt="<?php echo _("Delete"); ?>"></a>
							|
							<a href="<?php echo base_url()?>mcp/mail_manager/newsletter_clone/<?php echo $letters['id'];?>"><img width="16" height="16" border="0" src="<?php echo base_url();?>assets/cp/images/arrow-turn-180.png" alt="<?php echo _("Copy");?>"></a></td>
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
    
    <!-- START: SELECT COMPANY TYPE DIV -->
    <div id="select_company_type_div" style="display: none;">
    	<div class="innner_div">
    		<p>
    			<input type="checkbox" value="1" id="c_type_free" name='c_type_free' class="c_type" /> <span class="c_type_txt"><?php echo _('Free');?></span>
    		</p>
    		<p>
    			<input type="checkbox" value="2" id="c_type_basic" name='c_type_basic' class="c_type" /> <span class="c_type_txt"><?php echo _('Basic');?></span>
    		</p>
    		<p>
    			<input type="checkbox" value="3" id="c_type_pro" name='c_type_pro' class="c_type" /> <span class="c_type_txt"><?php echo _('Pro');?></span>
    		</p>
    		<p>
    			<input type="button" id="submit_select" name="submit_select" value="<?php echo _('Submit');?>" />
    		</p>
    	</div>
    </div>
    <!-- END: SELECT COMPANY TYPE DIV -->
    <!-- /content -->
