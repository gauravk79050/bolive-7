<script src="<?php echo base_url(); ?>assets/plugins/jquery-ui-1.12.0.custom/jquery-ui.min.js?version=<?php echo version;?>" type="text/javascript"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/jquery-ui-1.12.0.custom/jquery-ui.theme.min.css">
<script>
var availableTags2 = new Array();
var xhr;
var page_id = <?php echo $page_id;?>;
var type;
if(page_id == 1)
	type = 2;
else if(page_id == 2)
	type = 3;
var post_url = (page_id == 2) ? '<?php echo base_url();?>cp/products/get_semi_product_name/'+page_id:'<?php echo base_url();?>cp/products/get_semi_product_name';
$(function() {
/*	$.post(
		'<?php //echo base_url();?>cp/cdashboard/get_semi_product_name',
		{},
		function(response){
			availableTags = response;
			$( "#semisearch" ).autocomplete({
				minLength: 0,
				source: availableTags,
				focus: function( event, ui ) {
					$( "#semisearch" ).val( ui.item.label );
					return false;
				},
				select: function( event, ui ) {
					window.location = base_url+"cp/cdashboard/semi_product_addedit/product_id/"+ui.item.value;
					return false;
				}
			});
		},
		'json'
	); */

	$.post(
			post_url,
			{},
			function(response){
				availableTags = response;
				$( "#semisearch" ).autocomplete({
					minLength: 0,
					source: availableTags,
					focus: function( event, ui ) {
						$( "#semisearch" ).val( ui.item.label );
						return false;
					},
					select: function( event, ui ) {
						if(page_id == 1)
							window.location = base_url+"cp/products/semi_product_addedit/product_id/"+ui.item.value;
						else if(page_id == 2)
							window.location = base_url+"cp/products/semi_product_addedit_new/product_id/"+ui.item.value+"/2";
						return false;
					}
				});
			},
			'json'
		);
	$(".semireciname").keyup(function(e){
		if(e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40){
			show_semi_ingre_suggestion();
		}
	});
	if($("#serach_opt").val() == 1 )
		change_search_type(1);
	else if($("#serach_opt").val() == 2 )
		change_search_type(2);
});
function show_semi_ingre_suggestion(){
	availableTags = [];

	var search_str = $('#semisearchreci').val();
	if(search_str.length > 1){
		$('#loding_gif').show();
		if(xhr && xhr.readystate != 4){
			xhr.abort();
		}

		xhr = $.ajax({type:"POST",
				url: base_url+'cp/products/get_recipe_AjaxIngre/'+type,
				data: {
					'search_str': search_str
				},
				dataType: "json",
				success: function(response){
					$('#loding_gif').hide();
					availableTags2 = response;
					autocomplete_intializes();
				},
			});
	}else{
		autocomplete_intializes();
	}
}

function autocomplete_intializes(){
	$( "#semisearchreci" ).autocomplete({
		minLength: 0,
		appendTo: '#recipe_semi',
		source: availableTags2,
		focus: function( event, ui ) {
			//$( "#semisearchreci" ).val( ui.item.label );
			return false;
		},
		select: function( event, ui ) {
			window.location = base_url+"cp/products/semi_product_recipe/"+ui.item.value+"/"+type;
			return false;
		}
	})
	.data( "ui-autocomplete" )._renderItem = function( ul, item ) {
		return $( "<li></li>" )
		.data( "item.autocomplete", item )
		.append( item.label )
		.appendTo( ul );
	};

	$('#semisearchreci').autocomplete("search");
}
function print_these(){
	var pro_ids = '';

	jQuery("input[name='ids[]']").each(function(){
		//pro_ids.push(jQuery(this).val());
		pro_ids += '.'+jQuery(this).val();
	});

	var cat_id = jQuery('#categories_id').val();
	var subcat_id = jQuery('#subcategories_id').val();
	alert(cat_id);
	window.location = '<?php echo base_url(); ?>cp/products/print_product/print_these/'+cat_id+'/'+subcat_id+'/'+pro_ids;
}

function add_credit(crdt){
	jQuery.post(
		base_url+'cp/fooddesk/send_request_for_credit/'+crdt,
		{},
		function(data){
			alert(data.toSource());
		}
	);
}
function change_search_type(val){
	if(val == 1){
		$('#all_semi').show();
		$('#recipe_semi').hide();
	}else if(val == 2){
		$('#all_semi').hide();
		$('#recipe_semi').show();
	}
}
</script>
<style type="text/css">
.inp_fld{
	background: none repeat scroll 0 0 #FCFCFC;
    border: 1px solid #CCCCCC;
    color: #333333;
    display: inline;
    padding: 5px;
}
#fdd_credits {
    color: red;
    text-align: right;
}
.fc-first th {
    background: none repeat scroll 0 0 black !important;
    border: medium none !important;
}

.ui-state-active{

    border:0 !important;
}
 .ui-widget-content {
    background: #ffffff none repeat scroll 0 0 !important;
}

.ui-autocomplete {
		max-height: 100px;
		overflow-y: auto;
		z-index:500;
	}
</style>
<!-- MAIN -->
<div id="main">
	<div id="main-header">
    	<h2><?php if($page_id == 2){echo _('Extra Semi-Products');} else {echo _('Semi-Products');}?></h2>
     	<span class="breadcrumb"><a href="<?php echo base_url();?>cp/products/"><?php echo _('Home')?></a> &raquo; <?php if($page_id == 2){echo _('Extra Semi-Products');} else {echo _('Semi-Products');}?></span>
		<?php $messages = $this->messages->get();?>
		<?php if(isset ($messages)){if($messages != array()): foreach($messages as $type => $message): ?>
			<?php if($type == 'success' && $message != array()):?>
				<div id="succeed"><?php echo $message[0];?></div>
			<?php elseif($type == 'error' && $message != array()):?>
				<div id="error"><strong><?php echo _('Error')?></strong> : <?php echo $message[0];?></div>
			<?php endif;?>
		<?php endforeach; endif;}?>
	</div>

    <div style="display:none" id="update_checkbox" class="notification"></div>
	<div style="display:none" id="succeed_status"><?php echo _('Status successfully updated.')?></div>
    <div style="display:none" id="error_status"><?php echo _('Error occurred while updating status')?></div>
    <div id="content">
		<div id="content-container">
        	<div class="box">
          		<h3><?php echo _('Product details')?></h3>
          		<div class="table">
          			<table>
          				<tbody>
          					 <tr id="notice"><td id="notice_msg"  colspan='2'><?php echo _("The section has been added in the new Controlepanel(2.0)" ); ?><?php echo "<br>"; ?><?php echo _("Please proceed with new Controlepanel(2.0)")?><?php echo "<br>"; ?><a target="_blank" style="color: #8c8c00;text-decoration: none;" href="<?php echo rtrim($this->config->item('new_obs_url'),'/') ?>"><?php echo rtrim($this->config->item('new_obs_url'),'/') ?></a></td></tr>
          				</tbody>
          			</table>
					<!-- <table>
						<tr>
							<td colspan="3" style="text-align:right">
								<select id="serach_opt" onchange="change_search_type(this.value)" style="display: inline !important;">
									<option value="1"><?php echo _('Search for keyword')?></option>
									<option value="2"><?php echo _('Search in recipe')?></option>
								</select>
							</td>
							<td id="all_semi" colspan="6">
								&nbsp;&nbsp;&nbsp;<input type="text" name="semisearch" id="semisearch" size="30" class="text" placeholder="<?php if($page_id == 2){echo _('Extra Semi-Product Name');}else{echo _('Semi-Product Name');}?>">
	                      	</td>
	                      	<td id="recipe_semi" colspan="6" style="display: none">
								&nbsp;&nbsp;&nbsp;<input type="text" id="semisearchreci" size="30" class="text semireciname" placeholder="<?php echo _('Ingredient')?>">
								<div style="float:right; width:25px;height:25px">
									<img id="loding_gif" alt="loading" src="<?php echo  base_url()."assets/images/loading2.gif"?>" style="display:none;width: 22px; margin-top: 2px;">
								</div>
	                      	</td>
	                  	</tr>
                  		<tr>
                    		<td colspan="9" style="text-align:right;"><a href="javascript:;" <?php if ($page_id == 1){?>onclick="window.location.assign('semi_product_addedit/add/<?php echo $page_id?>')"<?php }else {?>onclick="window.location.assign('semi_product_addedit_new/add/<?php echo $page_id?>')"<?php }?>><?php if($page_id == 2){echo _('Add Extra Semi-Product ');}else{echo _('Add Semi-Product ');}?></a></td>
                  		</tr>
                  		<tr>
                    		<td colspan="9" style="text-align:center"></td>
                  		</tr>
                   	</table> -->
                   	<!-- <form method="post" action="">
						<table cellspacing="0">
							<thead>
                  				<tr>
                    				<th><?php echo _('Product Name')?></th>
                    				<th><?php echo _('Description')?></th>
                    				<th><?php echo _('Action')?></th>
                  				</tr>
                  				<tr>
                  					<td colspan="9"></td>
                  				</tr>

                			</thead>
                			<tbody class="sortable">
			                <?php
			                 if(!$products):?>
								<tr>
                    				<td colspan="9"><span class="field-error"><?php echo _('Product list is empty.')?></span></td>
                  				</tr>
				  			<?php else:?>
				 				<?php foreach($products as $product):?>
				  				<tr>
                    				<td>
										<input type="hidden" name="ids[]" value="<?php echo $product->id; ?>" />
						    			<input type="text" name="proname_<?php echo $product->id; ?>" value="<?php echo stripslashes($product->proname);?>" size="20" class="inp_fld" />
						    		</td>
									<td>
										<input type="text" name="prodescription_<?php echo $product->id; ?>" value="<?php echo stripslashes($product->prodescription);?>" size="30" class="inp_fld" /></span>
									</td>
									<td width="90px" >
									<?php if ($page_id == 1){?>
									<?php if($product->direct_kcp == 0){?>
										<a href="javascript:;" class="edit" onClick="alert('<?php echo _('Recipe can only be edited on product level');?>');"> <img width="16" height="16" border="0" alt="Edit" src="<?php echo base_url(); ?>assets/cp/images/edit.gif"></a> |
									<?php }else{?>
										<a href="<?php echo base_url().'cp/products/semi_product_addedit/product_id/'.$product->id?>" class="edit"> <img width="16" height="16" border="0" alt="Edit" src="<?php echo base_url(); ?>assets/cp/images/edit.gif"></a> |
										<?php }?>
									<?php }else {?>
										<a href="<?php echo base_url().'cp/products/semi_product_addedit_new/product_id/'.$product->id.'/'.$page_id?>" class="edit"> <img width="16" height="16" border="0" alt="Edit" src="<?php echo base_url(); ?>assets/cp/images/edit.gif"></a> |
									<?php }?>
										<a onclick="return confirmation('<?php echo $product->id; ?>');" href="#" class="delete"><img width="16" height="16" border="0" alt="remove" src="<?php echo base_url(); ?>assets/cp/images/delete.gif"></a> |
										<a href="<?php if($page_id == 2){echo base_url().'cp/products/product_clone/'.$product->id.'/2';}else{echo base_url().'cp/products/product_clone/'.$product->id.'/1';} ?>" class="delete"><img width="16" height="16" border="0" alt="clone" src="<?php echo base_url(); ?>assets/cp/images/arrow-turn-180.png"></a>
										<?php if( isset($show_recipe) && $show_recipe == 1 ){ ?>
										|<a target="_blank" class="recipe_sheet" href="<?php echo base_url().'cp/fooddesk/recipe_sheet/'.$product->id;?>"><img src="<?php echo base_url();?>assets/cp/images/03-pdf.png" style="width: 15px;" /></a>
										<?php } ?>
									</td>
								</tr>
				  			<?php endforeach;?>
							<tr>
							    <td colspan="8" style="padding: 5px 20px 5px 5px;text-align: right;">
								  <input type="submit" name="save" id="save" value="<?php echo _('Update'); ?>" />
								</td>
							</tr>

							<tr>
							    <td colspan="8">
									<a href="javascript:;" onclick="print_these();"><?php echo _('Print these products');?></a>
									<br />
									<img id="print_load" src="<?php echo base_url();?>assets/cp/images/loading-circle.gif" style="display: none;" />
								</td>
							</tr>
							<?php endif;?>
	                    		<tr>
								    <td colspan="9">
									   <p id="fdd_credits"> <?php echo _('You have still ').$fdd_credits._(' FoodDESK credits left!');?></p>
									</td>
								</tr>
                		</tbody>
              		</table>
              		</form> -->
              		<!-- <ul class="pagination">
						<?php if($links):echo $links; endif;?>
              		</ul> -->
              		<div style="margin-left:50px"> </div>
				</div>
        	</div>
      	</div>
    </div>
    <div id="credit_require" style="display: none">
   		<p><?php echo _('Sorry! Currently You have no credit to use a FoodDESK product.');?></p>
   		<p><?php echo _('To buy credits, choose a package.');?></p>
   		<ul>
   			<li><a onclick="add_credit(100)" href="javascript:;"><?php echo _('100 products/credits for 10');?>&euro;</a></li>
   			<li><a onclick="add_credit(200)" href="javascript:;"><?php echo _('200 products/credits for 15');?>&euro;</a></li>
   		</ul>
    </div>
    <!-- /content -->