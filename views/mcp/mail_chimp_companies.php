<link rel="stylesheet" href="<?php echo base_url();?>assets/cp/js/thickbox/css/thickbox.css" type="text/css"/>
<script type="text/javascript" src="<?php echo base_url();?>assets/cp/js/thickbox/javascript/thickbox.js"></script>
<link href="<?php echo base_url();?>assets/css/data_table.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/jqtable.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function($) {

		$(".syn_mail").live('click', function(){
			var listId = $(this).val();
			var list_type = $(this).attr('data-list_type');
			var lang_id = $(this).attr('data-lang_id');
			$( document ).find( '#loadingmessage' ).show();
			var api_url = '<?php echo base_url();?>mcp/mail_manager/chimp_mail_api/'+listId+'/'+list_type+'/'+lang_id;
			jQuery.ajax({
				url: api_url,
				type:'POST',
				data:{},
				success: function(response){
					$( document ).find( '#loadingmessage' ).hide();
					if(response){
						alert("<?php echo _('Synchronized Successfully.');?> ");
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

.sync_btn {
	padding: 30px 0px 15px 10px;
	text-align: center;
}
.syn_labl_wrapper {
	display: flex;
	align-items: center;
	margin-bottom: 15px;
	justify-content: center;
}
.syn_labl_wrapper label {
	margin-right: 15px;
	width: 200px;
	text-align: left;
}
.syn_labl_wrapper input {
	margin-right: 15px;
	max-width: 250px;
	width: 100%;
}
.syn_labl_wrapper button {
	width: 120px;
	height: 33px;
}
.syn_labl_wrapper .api_space_btn {
	width: 121px;
}
#loadingmessage{
	display:none;
	margin: 0px;
	padding: 0px;
	position: fixed;
	right: 0px; 
	top: 0px;
	width: 100%;
	height: 100%;
	background-color: rgb(102, 102, 102);
	z-index: 30001; 
	opacity: 0.8;
}
</style>

<div id="main">
	<div id="content">
		<div id="content-container">
			<div class="box" style="width: 98%">
				<h3><?php echo _('Companies')?></h3>
				<div class="clear"></div>
				<div class="sync_btn">
					<div class="syn_labl_wrapper">
						<label><?php echo _('API Key');?></label>
						<input type="text" value='<?php echo $apiKey; ?>' readonly>
						<div class="api_space_btn"></div>
					</div>
					<div class="syn_labl_wrapper">
						<label><?php echo _('Healthcare French List Id');?></label>
						<input type="text" value='<?php echo $healthListId_fr; ?>' readonly>
						<button class="syn_mail" value='<?php echo $healthListId_fr;?>' data-lang_id='3' data-list_type = 'healthcare' ><?php echo _('Synchronize');?></button>
					</div>
					<div class="syn_labl_wrapper">
						<label><?php echo _('Healthcare Dutch List Id');?></label>
						<input type="text" value='<?php echo $healthListId_dch; ?>' readonly>
						<button class="syn_mail" value='<?php echo $healthListId_dch;?>' data-lang_id='2' data-list_type = 'healthcare' ><?php echo _('Synchronize');?></button>
					</div>
					<div class="syn_labl_wrapper">
						<label><?php echo _('Retail French List Id');?></label>
						<input type="text" value='<?php echo $retailListId_fr; ?>' readonly>
						<button class="syn_mail" value='<?php echo $retailListId_fr; ?>' data-lang_id='3' data-list_type = 'retail' ><?php echo _('Synchronize');?></button>
					</div>
					<div class="syn_labl_wrapper">
						<label><?php echo _('Retail Dutch List Id');?></label>
						<input type="text" value='<?php echo $retailListId_dch; ?>' readonly>
						<button class="syn_mail" value='<?php echo $retailListId_dch; ?>' data-lang_id='2' data-list_type = 'retail' ><?php echo _('Synchronize');?></button>
					</div>
				</div>
				<div id="loadingmessage">
					<img src="<?php echo base_url(''); ?>assets/cp/images/ajax-loading.gif" style="position: absolute; color: White; top: 50%; left: 45%;"/>
				</div>
			</div>
		</div>
	</div>
</div>
