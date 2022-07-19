<?php
$larr = localeconv();
$search = array(
		$larr['decimal_point'],
		$larr['mon_decimal_point'],
		$larr['thousands_sep'],
		$larr['mon_thousands_sep'],
		$larr['currency_symbol'],
		$larr['int_curr_symbol']
);
$replace = array('.', '.', '', '', '', '');
$sel_lang = get_lang($_COOKIE['locale']);
?>
<script type="text/javascript">
var multi_select = "<?php echo _("Multiselect Attributes");?>";
var drag_me = "<?php echo _("Add this");?>";
var drag_me_all = "<?php echo _("Add all of this product");?>";
var required_txt = "<?php echo _('Required'); ?>";
var shortname_sugg = "<?php echo _('Product Short name suggestion');?>";
var suggestion_send = "<?php echo _('Product short name send to Fooddesk admin, it will be automatic updated in your product after approval');?>";
var suggestion_not_send = "<?php echo _('could not be submitted product shortname');?>";

var plz_give_a_name_of_product_msg = "<?php echo _('Please give a name to product');?>";
var fill_producer_art_msg = "<?php echo addslashes( _('Please fill in the article number of producer so the producer knows exactly which product you need for this recipe')); ?>";
var fill_supplier_art_msg = "<?php echo addslashes(_('Please fill in the article number of supplier so the supplier knows exactly which product you need for this recipe')); ?>";
var fill_000_msg = "<?php echo _('If you can\'t find this article number then type 000');?>";
var note_msg = "<?php echo _('Note: If you don\'t know the article number then it will take more time to fix this product.');?>";
var do_u_mean_txt = "<?php echo _('Do you mean');?>";
var from_txt = "<?php echo _('from')?>";
var art_nbr_p = "<?php echo _('Article Number Producer')?>";
var art_nbr_s = "<?php echo _('Article Number Supplier')?>";
var producer_txt = "<?php echo _('Producer')?>";
var supplier_txt = "<?php echo _('Supplier')?>";
var no_product_added = "<?php echo _("No products added"); ?>";
var cant_be_zero_any_msg = "<?php echo _("No quantity field can be 0 or empty!"); ?>";

var prefix_text = "<?php echo _('prefix'); ?>";
var plz_select_cat_msg = "<?php echo _('Please select a category');?>";
var gm_str = "<?php echo _('gm');?>";
var qunat_greater_than_zero = "<?php echo _('Quantity of product must be greater than 0 gm.');?>";
var select_from_list = "<?php echo _('Please Add a product from suggestion first.');?>";

var ing_datas = new Array();
var allg_datas = new Array();
var traces_datas = new Array();

var fdd_url = "<?php echo $this->config->item('fdd_url'); ?>";
var plz_select_producer_msg = "<?php echo _('Please select a producer or supplier');?>";
</script>

<script src="<?php echo base_url();?>assets/kcp/js/select2/select2.min.js?version=<?php echo version;?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/kcp/js/select2/select2.css" media="screen">

<link rel="stylesheet" href="<?php echo base_url();?>assets/css/ui/jquery.ui.all.css">
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.core.js?version=<?php echo version;?>"></script>
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.widget.js?version=<?php echo version;?>"></script>
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.sortable.js?version=<?php echo version;?>"></script>
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.position.js?version=<?php echo version;?>"></script>
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.draggable.js?version=<?php echo version;?>"></script>
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.droppable.js?version=<?php echo version;?>"></script>

<script src="<?php echo base_url(); ?>assets/plugins/jquery-ui-1.11.2.custom/jquery-ui.min.js?version=<?php echo version;?>" type="text/javascript"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/jquery-ui-1.11.2.custom/jquery-ui.theme.min.css">

<style>
	.ui-autocomplete {
		max-height: 100px;
		overflow-y: auto;
		z-index:500;
	}
	.select2-container-multi .select2-choices{
    	min-height: 110px;
	}

	.save_b {
	    padding: 20px 60px 20px 20px;
	    text-align: right;
	}
</style>

<!-- -------------------------------------- CROPPING IMAGE ------------------------------------------------------ -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/cp/new_css/jquery.Jcrop.css" type="text/css" />
<style type="text/css">
.preview_title{
    display: block;
    font-size: 20px;
    font-weight: bold;
    margin: 10px auto;
    text-align: center;
    text-decoration: underline;
}

.jcrop-holder #preview-pane {
  display: block;
  position: absolute;
  /*z-index: 2000;*/
  top: -2px;
  right: -260px;
  padding: 6px;
  border: 1px rgba(0,0,0,.4) solid;
  background-color: white;

  -webkit-border-radius: 6px;
  -moz-border-radius: 6px;
  border-radius: 6px;

  -webkit-box-shadow: 1px 1px 5px 2px rgba(0, 0, 0, 0.2);
  -moz-box-shadow: 1px 1px 5px 2px rgba(0, 0, 0, 0.2);
  box-shadow: 1px 1px 5px 2px rgba(0, 0, 0, 0.2);
}

/* The Javascript code will set the aspect ratio of the crop
   area based on the size of the thumbnail preview,
   specified here */
#preview-pane .preview-container {
  width: 220px;
  height: 209px;
  overflow: hidden;
}
/*#TB_window{
	top: 80% !important;
	z-index: 999 !important;
}*/
#crop_button{
	background-color:#007a96;
    padding:12px 26px;
    color:#fff;
    font-size:14px;
    border-radius:2px;
    cursor:pointer;
    display:inline-block;
    line-height:1;
    border: none;
}
.crop_div{
	margin-top: 30px;
	text-align: center;
}

#GroupsTable input.medium, #GroupsPersonTable input.medium, #WGroupsTable input.medium{
	width: 100%;
}
.ing_pro_name_row td {
    padding: 10px 0;
}

.fc-first th {
    background: none repeat scroll 0 0 black !important;
    border: medium none !important;
}
</style>
<script src="<?php echo base_url();?>assets/cp/new_js/jquery.Jcrop.js?version=<?php echo version;?>"></script>
<!-- -------------------------------------------------------------------------------------------- -->
<style>
	/*#TB_window{
		margin-top: -270px !important;
	}*/
	.littletext {
		font-size: 10px;
	}
/* 	#TB_ajaxContent{ */
/* 		max-height: 400px !important; */
/* 	} */

	select {
	    margin-left: 0px;
	}
	.textlabel{
		width: 200px;
	}
	.ing_pro_name_row img {
    	cursor: pointer;
	}
.ui-state-hover, .ui-widget-content .ui-state-hover, .ui-widget-header .ui-state-hover, .ui-state-focus, .ui-widget-content .ui-state-focus, .ui-widget-header .ui-state-focus {
   border: 1px solid #fff;
   background: transparent!important;
   font-weight: bold;
   color: #eb8f00!important ;
}

.unapproved td:nth-child(3) input{
	background: salmon;
}
</style>
<link href="<?php echo base_url()?>assets/cp/css/qtip.css" rel="stylesheet" type="text/css">
<script src="<?php echo base_url()?>assets/cp/js/jquery.tooltip.js?version=<?php echo version;?>"></script>
<script type="text/javascript">
var myEvent = window.attachEvent || window.addEventListener;
var chkevent = window.attachEvent ? 'onbeforeunload' : 'beforeunload'; /// make IE7, IE8 compitable

myEvent(chkevent, function(e) { // For >=IE7, Chrome, Firefox
	if (needToConfirm){
    var confirmationMessage = "<?php echo _('If you leave before saving, your changes will be lost.');?>";  // a space
        (e || window.event).returnValue = confirmationMessage;
        return confirmationMessage;
	}
});
</script>
<?php if( $this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium'){?>
	<script src="<?php echo base_url()?>assets/cp/new_js/kcp_prod_new.js?version=<?php echo version;?>"></script>
<?php }?>
<!-- MAIN -->
<div id="main">
<div id="main-header">
  <h2>
    <?php if($product_information): echo _('UPDATE EXTRA SEMI-PRODUCT');else: echo _('ADD EXTRA SEMI-PRODUCT'); endif;?>
  </h2>
  <span class="breadcrumb"><a href="<?php echo base_url()?>cp/cdashboard/"><?php echo _('Home')?></a> &raquo; <a href="<?php echo base_url()?>cp/products/lijst"><?php echo _(' Products')?></a> &raquo;
  <?php if($product_information): echo _('update product');else: echo _('add product'); endif;?>
  </span>
</div>
<div id="content">
	<div id="content-container">
	    <div class="box">
			<h3> <?php echo _('Product information ')?></h3>
			<div class="table">
		        <form action="<?php echo base_url();?>cp/products/products_addedit" enctype="multipart/form-data" method="post" id="frm_products_addedit" name="frm_products_addedit">
					<?php if($product_information){?>
					<input type="hidden" value="<?php echo $product_information['0']->id?>" name="prod_id" class="prod_id">
					<input type="hidden" value="<?php echo $product_information['0']->direct_kcp?>" name="direct_kcp">
					<?php } else { ?>
					<input type="hidden" value="1" name="direct_kcp">
					<?php }?>
					<table border="0">
			       		<tbody>
			              <tr>
			                <td class="textlabel"><?php echo _('Product Name')?></td>
			                <td style="padding-right:250px"><input type="text" class="text medium" size="30" id="proname" name="proname" <?php if($product_information):?>value="<?php echo stripslashes($product_information['0']->proname)?>"<?php endif;?>></td>
			              </tr>

			              <tr>
			                <td class="textlabel"><?php echo _('Description')?></td>
			                <td style="padding-right:250px">
			                	<textarea rows="5" cols="50" type="textarea" id="prodescription" name="prodescription"><?php if($product_information):echo trim(stripslashes($product_information['0']->prodescription));endif;?></textarea>
							</td>
			              </tr>

			              <?php if( $this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium'){?>
				              <tr id="recipe_heading_tr" >
				              	<td class="textlabel" colspan="2" style="font-size: 20px;text-align:center">
				              		<?php echo _('Your Recipe')?><br/>
				              	</td>
				              </tr>

				              <tr id="recipe_weight_tr">
				                <td class="textlabel">
				                	<?php echo _('Weight for recipe in Kg')?><br/>
				                </td>
				                <td style="padding-right:100px">
				                	<input id="recipe_weight" name="recipe_weight" type="hidden" step=".1" value="<?php if(isset($product_information) && !empty($product_information)){ echo  $product_information[0]->recipe_weight; } ?>" min="0" class="text" style="width: 20%;" onchange="" onblur="">
				                	<strong><span id="recipe_weight_span"> <?php if(isset($product_information) && !empty($product_information)){ echo  $product_information[0]->recipe_weight; }else{ echo "0";} ?></span></strong>
				                	<strong><?php echo _("Kg");?></strong>

				                	<?php if(isset($product_information) && !empty($product_information)){ ?>
				                		&nbsp;&nbsp;&nbsp;
				                		<img src="<?php echo base_url().'assets/cp/images/select_list_h.gif'; ?>" onclick="reset_wt()">
				                	<?php } ?>

				                	&nbsp;&nbsp;&nbsp;
				                	<span><?=_('Important: only with hot dishes, the weight must be weighted when it\'s hot (not cold)'); ?></span>
				                	<?php //if(!isset($product_information) || empty($product_information) || (isset($product_information) && !empty($product_information) && $product_information['0']->direct_kcp == 0)) {  ?>



				              		<div id="fdd_tools" <?php if(isset($product_information) && !empty($product_information)){ }else{ }?>>
			              				<br/>

					              		<?php if($fdd_credits > 0){?>

						              		<?php if(isset($product_information) && !empty($product_information)){?>
						              			<a href="<?php echo base_url().'cp/fooddesk/fdd_own_products/'.$product_information['0']->id.'/'.$page_id.'?height=300&width=900'?>" title="<?php echo _('Add Recipe of PRODUCT ');  echo $product_information['0']->proname; ?>" class="thickbox"><?php echo _('FoodDESK/Own products')?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						              		<?php }else{?>
						              			<a href="<?php echo base_url().'cp/fooddesk/fdd_own_products/0/'.$page_id.'?height=300&width=900'?>" title="<?php echo _('Add FoodDESK or Own products');?>" class="thickbox"><?php echo _('FoodDESK/Own products')?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						              		<?php }?>

					              		<?php }else{?>
					              			<a href="#TB_inline?height=300&width=500&inlineId=credit_require" title="<?php echo _('No credit left!');?>" class="thickbox"><?php echo _('FoodDESK/Own products')?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					              		<?php }?>
				              		</div>

			              		<?php // }?>


				                </td>
				              </tr>

				              <tr id="recipe_method_tr">
				                  <td class="textlabel">
				                	<?php echo _('How to make')?> :
				                  </td>
				               	  <td>
				               	  	<textarea id="recipe_method_txt" name="recipe_method_txt" rows="5" cols="80"><?php if($product_information){ echo $product_information['0']->recipe_method; };?></textarea>
				               	  </td>
				               </tr>

			              <?php }?>


			              <tr id="ing_container"  >
			              	<td class="textlabel">
			              		<?php echo _("Ingredients");?> :
			              	</td>
			              	<td>
			              		<?php if( $this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium'){?>

			              		<div style="width: 100%; <?php if(isset($product_information)){if(!empty($product_information)){ if($product_information['0']->direct_kcp == 1 && ($product_information['0']->fdd_producer_id != 0 || $product_information['0']->fdd_supplier_id != 0)) { echo 'display:none;';  }}} ?>" >
			              			<table border="0" class="override" >
	                                	<tbody id="kp_ing" class="">
	                                	<?php if(isset($used_fdd_pro_info)){?>
	                                		<?php foreach ($used_fdd_pro_info as $fdd_info){?>

	                                			<?php $this_pro_name = '';
		                                		if(strlen($fdd_info['p_name'.$sel_lang.'']) > 23){
													$this_pro_name = substr($fdd_info['p_name'.$sel_lang.''], 0,23).'...';
												}else{
													$this_pro_name = $fdd_info['p_name'.$sel_lang.''];
												}
												?>
		                                		<?php if( $fdd_info[ 'approval_status' ] == 0 ){ ?>
		                                			<tr id="ing_sub_row_<?php echo $fdd_info['fdd_pro_id']; ?>" class="ing_pro_name_row unapproved" rel="0">
		                                		<?php }else{ ?>
		                                			<tr id="ing_sub_row_<?php echo $fdd_info['fdd_pro_id']; ?>" class="ing_pro_name_row" rel="0">
		                                		<?php } ?>
		                                			<td width="7%" >
		                                				<!-- <div style="border: 1px solid rgb(204, 204, 204); box-sizing: border-box; padding: 1px 4px 0px;background:#eee;">
		                                				<img onclick="toggle_ing(<?php echo $fdd_info['fdd_pro_id']; ?>)" src="<?php echo base_url();?>assets/images/icon-plus-minus.png" style="width: 22px; display: inline-block; padding-top: 1px;" class="close_img"/>
		                                				<img onclick="toggle_ing(<?php echo $fdd_info['fdd_pro_id']; ?>)" src="<?php echo base_url();?>assets/images/icon-plus-minus1.png" style="width: 22px; display: inline-block; padding-top: 1px;display:none" class="open_img" />

		                                				<strong style="display: inline-block;font-size: 11px;margin-left: 5px;padding-top: 6px;vertical-align: top;" title="<?php echo $fdd_info['p_name_dch']; ?>"> <?php echo stripslashes($this_pro_name);?></strong>
		                                				</div> -->
		                                				<?php if(!empty($product_ingredients)){?>
							                    			<?php foreach ($product_ingredients as $product_ingredient){?>
							                    				<?php if($product_ingredient->kp_id == $fdd_info['fdd_pro_id'] && $product_ingredient->ki_id == 0 && $product_ingredient->ki_name != '(' && $product_ingredient->ki_name != ')' ){?>
							                    						<input type="text" style="width:100%" class="text pro_prefix" onkeyup="pro_prefix_change(this)" value="<?php echo $product_ingredient->prefix; ?>" placeholder="<?php echo _('prefix');?>" >
							                    						<?php BREAK; ?>
							                    				<?php }?>
							                    			<?php }?>
						                    			<?php }?>

		                                			</td>
		                                			<td width="3%" ></td>
		                                			<td width="30%">
		                                				<?php if ($fdd_info['product_type'] == 1){
		                                					if( $fdd_info['approval_status'] == 1 ){?>
		                                						<input type="text" style="width:100%;" class="text product_name_text" value="<?php echo stripslashes($fdd_info['p_name'.$sel_lang.'']); ?>" disabled>
	                                						<?php } else {?>
		                                						<input type="text" style="width:100%;background-color:pink" class="text product_name_text" value="<?php echo stripslashes($fdd_info['p_name'.$sel_lang.'']); ?>" disabled>
	                                						<?php } ?>
		                                				<?php }else{ ?>
		                                					<input type="text" style="width:100%" class="text product_name_text" value="<?php echo stripslashes($fdd_info['p_name'.$sel_lang.'']); ?>" disabled>
		                                				<?php } ?>
		                                			</td>
		                                			<td width="3%" ></td>
		                                			<td width="12%">
		                                				<?php

// 			                                			if($fdd_info['quantity'] > 1){
// 															$fdd_quant = round($fdd_info['quantity'],0);
// 														}else{
															$fdd_quant = str_replace($search, $replace,round($fdd_info['quantity'],2));
//														}
			                                			?>
		                                				<input type="text" class="text fdd_product_quants" value="<?php echo $fdd_quant;?>" onkeyup="quant_change(this)" style="width: 60%; padding-bottom: 7px; margin-top: 5px;"><strong> <?php echo $fdd_info['unit'];?> </strong>
		                                				<input type="hidden" class="fdd_product_hidden_id" value="<?php echo $fdd_info['fdd_pro_id'];?>" >
		                                				<input type="hidden" class="ing_pro_name" value="<?php echo $fdd_info['p_name'.$sel_lang.''].'--'.$fdd_info['s_name'].'--EAN:'.$fdd_info['barcode'].'--PLU:'.$fdd_info['plu'];?>" >
		                                			</td>
		                                			<td width="4%" style="vertical-align: sub;">
														<img src="<?php echo base_url();?>assets/cp/images/Delete.gif" style="width: 20px; margin-top: 7px;" onclick="remove_this_fdd_pro(<?php echo $fdd_info['fdd_pro_id'];?>)" />
													</td>
													<?php if ($fdd_info['product_type'] == 1){?>
														<td style="color: blue;"><?php echo "GS1";?></td>
														<td width="4%">
															<?php if( in_array( $fdd_info['fdd_pro_id'], $fdd_pro_fav ) ){?>
																	<img src="<?php echo base_url(); ?>/assets/images/greenstar.png" data-status="marked" class="mark_fav prod_status"  data-prod_id="<?php echo $fdd_info[ 'fdd_pro_id' ]; ?>" > 
																<?php }else{ ?>
																	<img src="<?php echo base_url(); ?>/assets/images/star.jpg" data-status="unmarked" class="mark_fav prod_status"  data-prod_id="<?php echo $fdd_info[ 'fdd_pro_id' ]; ?>" >
																<?php } ?>
														</td>
													<?php }else{?>
														<td width="4%" style="vertical-align: sub;">
															<?php if($fdd_info['data_sheet'] != '' && $fdd_info['data_sheet'] != NULL){?>
																<a target="_blank" href="<?php echo $this->config->item('fdd_url').'assets/cp/uploads/'.$fdd_info['data_sheet']; ?>" >
																	<img src="<?php echo base_url();?>assets/images/pdf2.jpeg" style="width: 20px;" />
																</a>
															<?php }else{?>
																<img src="<?php echo base_url();?>assets/images/pdf1.jpeg" style="width: 20px;" />
															<?php }?>
			                                			</td>
			                                			<td width="5%">
															<?php if( in_array( $fdd_info['fdd_pro_id'], $fdd_pro_fav ) ){?>
																	<img src="<?php echo base_url(); ?>/assets/images/greenstar.png" data-status="marked" class="mark_fav prod_status"  data-prod_id="<?php echo $fdd_info[ 'fdd_pro_id' ]; ?>" >
																<?php 
																}else{ ?>
																	<img src="<?php echo base_url(); ?>/assets/images/star.jpg" data-status="unmarked" class="mark_fav prod_status"  data-prod_id="<?php echo $fdd_info[ 'fdd_pro_id' ]; ?>" >
																<?php } ?>
														</td>
			                                		<?php }?>
		                                			<td width="32%" style="vertical-align: sub;">
		                                				<?php if(isset($product_information)){if(!empty($product_information)){ if($product_information['0']->is_custom_pending == 1 && $product_information['0']->changed_fixed_product_id == $fdd_info['fdd_pro_id'] ) {  ?>
															<img src="<?php echo base_url();?>assets/images/new-icon.png" style="width: 20px;"  />
														<?php }}}?>
														<?php if( $fdd_info['approval_status'] == 0 ){?>
															<span style="color: #777">(<?php echo _('Is being reviewed'); ?>)
			                                				</span>
			                                			<?php } ?>
													</td>
		                                		</tr>

		                    					<?php }?>
	                    					<?php }?>
	                    					<?php if(!empty($product_ingredients)){?>
						                    	<?php foreach ($product_ingredients as $key => $product_ingredient){?>
						                    		<script>
						                    			<?php if(!$product_ingredient->kp_id){?>
							                    			<?php if($product_ingredient->ki_name == '('){ ?>
							                    				$('#lp_count').val(parseInt($('#lp_count').val()) + 1);
							                    				ing_datas.push({id:'lp#'+parseInt($('#lp_count').val()),text:'. ( .'});
							                    			<?php }else if($product_ingredient->ki_name == ')'){ ?>
							                    				$('#rp_count').val(parseInt($('#rp_count').val()) + 1);
							                    				ing_datas.push({id:'rp#'+parseInt($('#rp_count').val()),text:'. ) .'});
							                    			<?php }else{ ?>
							                    				ing_datas.push({id:"<?php echo $product_ingredient->ki_name; ?>",text:"<?php echo stripslashes($product_ingredient->ki_name); ?>"});
							                    			<?php }?>

						                    			<?php
						                    				}
						                    				else{
						                    					if( $_COOKIE['locale'] == 'en_US' ){
						                    						$aller_type = $product_ingredient->aller_type;
						                    						$allergence = $product_ingredient->allergence;
						                    						$sub_allergence = $product_ingredient->sub_allergence;
						                    						$new_allergence = $product_ingredient->new_allergence;
						                    					}
						                    					if( $_COOKIE['locale'] == 'nl_NL' ){
						                    						$aller_type = $product_ingredient->aller_type_dch;
						                    						$allergence = $product_ingredient->allergence_dch;
						                    						$sub_allergence = $product_ingredient->sub_allergence_dch;
						                    						$new_allergence = $product_ingredient->new_allergence_dch;
						                    					}
						                    					if( $_COOKIE['locale'] == 'fr_FR' ){
						                    						$aller_type = $product_ingredient->aller_type_fr;
						                    						$allergence = $product_ingredient->allergence_fr;
						                    						$sub_allergence = $product_ingredient->sub_allergence_fr;
						                    						$new_allergence = $product_ingredient->new_allergence_fr;
						                    					}
						                    			?>
						                    				var str = "<?php if($product_ingredient->prefix == ''){ echo $product_ingredient->ki_name; }else{ echo $product_ingredient->ki_name.' ('.$product_ingredient->prefix.')';};?>";
							                    			var combine_id = "<?php echo $product_ingredient->prefix.'#'.$product_ingredient->ki_name.'#'.$product_ingredient->ki_id.'#'.$product_ingredient->kp_id.'#'.$product_ingredient->is_obs_ing.'#'.$key.'#'.$aller_type.'#'.$allergence.'#'.$sub_allergence.'#'.$new_allergence; ?>";
							                    			ing_datas.push({id:combine_id,text:stripslashes(str)});
						                    			<?php }?>
						                    		</script>
						                    	<?php }?>
						                    <?php }?>
						                    <?php if(!empty($product_ingredients_vetten)){?>
						                    	<?php foreach ($product_ingredients_vetten as $vetten){?>
						                    		<script>
						                    			var str = "<?php echo $vetten->ki_name;?>";
							                    		var combine_id = "<?php echo '#'.$vetten->ki_name.'#'.$vetten->ki_id.'#'.$vetten->kp_id.'#2'; ?>";
							                    		ing_datas.push({id:combine_id,text:stripslashes(str)});
						                    		</script>
						                    	<?php }?>
						                    <?php }?>
						                    <?php if(!empty($product_additives)){?>
						                    	<?php foreach ($product_additives as $add){?>
						                    		<script>
						                    			var str = "<?php echo $add['ki_name'];?>";
							                    		var combine_id = "<?php echo $add['add_id'].'#'.$add['ki_name'].'#'.$add['ki_id'].'#'.$add['kp_id'].'#3'; ?>";
							                    		if(str != ""){
							                    			ing_datas.push({id:combine_id,text:stripslashes(str)});
							                    		}
						                    		</script>
						                    	<?php }?>
						                    <?php }?>
	                                    </tbody>
	                                </table>
			              		</div>

			              		<div style="width: 100%; <?php if(isset($product_information)){if(!empty($product_information)){ if($product_information['0']->direct_kcp == 1 && ($product_information['0']->fdd_producer_id != 0 || $product_information['0']->fdd_supplier_id != 0)) { echo 'display:none;';  }}} ?>" >
			              			<table border="0" class="override" >
	                                	<tbody id="kp_ing_own" class="">
	                                	<?php if(isset($used_own_pro_info)){?>
	                                		<?php foreach ($used_own_pro_info as $own_pro_info){?>

	                                		<?php $this_pro_name = '';
	                                		if(strlen($own_pro_info['proname']) > 23){
												$this_pro_name = substr($own_pro_info['proname'], 0,23).'...';
											}else{
												$this_pro_name = $own_pro_info['proname'];
											}
	                                		?>
		                                		<tr id="ing_sub_row_<?php echo $own_pro_info['fdd_pro_id']; ?>" class="ing_pro_name_row" rel="0">
		                                			<td width="7%" >
		                                				<?php if(!empty($product_ingredients)){?>
							                    			<?php foreach ($product_ingredients as $product_ingredient){?>
							                    				<?php if($product_ingredient->kp_id == $own_pro_info['fdd_pro_id'] && $product_ingredient->ki_id == 0 && $product_ingredient->ki_name != '(' && $product_ingredient->ki_name != ')' ){?>
							                    						<input type="text" style="width:100%" class="text pro_prefix" onkeyup="pro_prefix_change(this)" value="<?php echo $product_ingredient->prefix; ?>" placeholder="<?php echo _('prefix');?>">
							                    						<?php BREAK; ?>
							                    				<?php }?>
							                    			<?php }?>
						                    			<?php }?>
		                                			</td>
		                                			<td width="3%" ></td>
		                                			<td width="30%" >
		                                				<input type="text" style="width:100%;background:pink" class="text product_name_text" value="<?php echo stripslashes($own_pro_info['proname']); ?>" disabled>
		                                			</td>
		                                			<td width="3%" ></td>
		                                			<td width="12%">
		                                				<?php

// 			                                			if($own_pro_info['quantity'] > 1){
// 															$own_quant1 = round($own_pro_info['quantity'],0);
// 														}else{
															$own_quant1 = str_replace($search, $replace,round($own_pro_info['quantity'],2));
//														}
			                                			?>
		                                				<input type="text" class="text own_product_quants" value="<?php echo $own_quant1;?>" onkeyup="quant_change(this)" style="width: 60%; padding-bottom: 7px; margin-top: 5px;"><strong> <?php echo $own_pro_info['unit'];?> </strong>
		                                				<input type="hidden" class="fdd_product_hidden_id" value="<?php echo $own_pro_info['fdd_pro_id'];?>" >
		                                				<input type="hidden" class="ing_pro_name" value="<?php echo $own_pro_info['proname'].' '.'--'.' '.$own_pro_info['s_name'];?>" >
		                                			</td>
		                                			<td width="4%" style="vertical-align: sub;">
														<img src="<?php echo base_url();?>assets/cp/images/Delete.gif" style="width: 20px; margin-top: 7px;" onclick="remove_this_fdd_pro(<?php echo $own_pro_info['fdd_pro_id'];?>)" />
													</td>
													<td width="4%" style="vertical-align: sub;">

		                                			</td>
		                                			<td width="5%" style="vertical-align: sub;">

		                                			</td>
		                                			<td width="32%" style="vertical-align: sub;">

		                                			</td>
		                                		</tr>

		                    					<?php }?>
	                    					<?php }?>
	                                    </tbody>
	                                 <!-- <tfoot>
	                                    	<tr id="fdd_total_tr" style="display:none" ><td colspan="5" style="text-align:right"><strong id="total_fdd_pro_quants_container" ><span>Total </span><span id="total_fdd_pro_quants">00</span><span> / </span><span id="total_recipe_wt"><?php if(isset($product_information) && !empty($product_information)){ echo  $product_information[0]->recipe_weight * 1000; } ?></span><span> gm</span></strong></td></tr>
	                                    </tfoot>
	                                 -->
	                                </table>
			              		</div>

			              		<input class="text small" style="width: 600px; height: 100px;" id="ingredients" name="ingredients" value="<?php if($product_information && $product_information['0']->ingredients){ echo $product_information['0']->ingredients ; }?>" />
			              		<?php }else{ ?>
			              			<textarea class="text small" rows="8" style="width: 425px;" id="ingredient" name="ingredients" <?php if($this->company->k_assoc){?>placeholder="<?php echo _('Please add ingredients separated with comma(,).');?>"<?php }?> ><?php if($product_information && $product_information['0']->ingredients){ echo $product_information['0']->ingredients ; }?></textarea>
			              		<?php } ?>
			              	</td>
			              </tr>

			              <!-- ALLERGENCE -->
			              <tr id="all_container" >
			              	<td class="textlabel">
			              		<?php echo _("Allergence");?> :
			              	</td>
			              	<td>
			              		<?php if( $this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium' ){?>
			              		<input class="text small" style="width: 600px; height: 100px;" id="allergence" name="allergence" value="<?php if($product_information && $product_information['0']->allergence){ echo $product_information['0']->allergence ; }?>" />
			              		<input type="hidden" id="rp_count_allg" value="0" />
			              		<input type="hidden" id="lp_count_allg" value="0" />
			              		<!-- <input type="button" name="" id="" value="(" onclick="add_symbol('(','allergence');" />
			              		<input type="button" name="" id="" value=")" onclick="add_symbol(')','allergence');" /> -->

			              		<div style="width: 50%;">
			              			<table border="0" class="override" >
	                                	<tbody id="kp_allergence" class="" style="display: none">
	                    					<?php if(!empty($product_allergences)){?>
	                    					<tr><td colspan="3">&nbsp;</td></tr>
	                    					<?php $conuter = 0; ?>
	                    					<?php foreach ($product_allergences as $product_allergence){?>
			                    				<?php if($product_allergence->kp_id){?>
			                    					<?php if($product_allergence->ka_id){?>
			                    					<tr id="allg_<?php echo $product_allergence->kp_id;?>_<?php echo $product_allergence->ka_id;?>">
			                    					<?php }/*else{?>
			                    					<tr><td colspan="3">&nbsp;</td></tr>
			                    					<tr id="pro_a_<?php echo $product_allergence->kp_id;?>">
			                    					<?php }*/?>
														<td width="70%">
															<p class="draggabled">
																<input type="text" name="kp_a_names_prefix[]" class="text short prefix" value="<?php echo $product_allergence->prefix;?>" />
																<input type="text" name="kp_allg_names[]" class="text medium name" value="<?php echo $product_allergence->ka_name;?>" style="width:70%;<?php if(!$product_allergence->ka_id){?>font-weight:bold;<?php }?>" disabled="disabled"  />
																<input type="hidden" class="kp_allg_ids" name="kp_allg_ids[]" value="<?php echo $product_allergence->ka_id; ?>" />
																<input type="hidden" class="kp_allg_pid" name="kp_allg_pid[]" value="<?php echo $product_allergence->kp_id; ?>" />
															</p>
														</td>
														<td width="10%">
															<!-- <img width="18" border="0" onClick="javascript:deleteIngredients(this);" src="<?php echo base_url();?>assets/cp/images/delete.gif" /> -->
														</td>
														<td width="5%" style="text-align:right">
															<!-- <img width="18" border="0" class="handle draggable_allg" src="<?php echo base_url();?>assets/cp/images/move.png" style="cursor: pointer;" title="<?php echo _("Drag me");?>" /> -->
															<?php if($product_allergence->ka_id){?>
																<img width="18" border="0" class="handle" src="<?php echo base_url();?>assets/cp/images/upload.png" style="cursor: pointer;" title="<?php echo _("Drag me");?>" onclick="drop_me('allg_<?php echo $product_allergence->kp_id;?>_<?php echo $product_allergence->ka_id;?>','allergence');" />
															<?php } ?>
														</td>
														<td width="10%">
														</td>
														<td width="5%" style="text-align:right">
															<?php if(!($conuter)){?>
																<img width="18" border="0" class="handle" src="<?php echo base_url();?>assets/cp/images/upload_all.png" style="cursor: pointer;" title="<?php echo _("Add All");?>" onclick="drop_me_all('pro_<?php echo $product_allergence->kp_id;?>','allergence');" />
															<?php }?>
														</td>
													</tr>
													<?php }?>
													<script>
			                    					<?php if(!$product_allergence->kp_id){?>
				                    					<?php if($product_allergence->ka_name == '('){ ?>
				                    						$('#lp_count_allg').val(parseInt($('#lp_count_allg').val()) + 1);
				                    						allg_datas.push({id:'lp#'+parseInt($('#lp_count_allg').val()),text:'. ( .'});
				                    					<?php }else if($product_allergence->ka_name == ')'){ ?>
				                    						$('#rp_count_allg').val(parseInt($('#rp_count_allg').val()) + 1);
				                    						allg_datas.push({id:'rp#'+parseInt($('#rp_count_allg').val()),text:'. ) .'});
				                    					<?php }else{ ?>
				                    						allg_datas.push({id:"<?php echo $product_allergence->ka_name; ?>",text:"<?php echo stripslashes($product_allergence->ka_name); ?>"});
				                    					<?php }?>

			                    					<?php }else{?>
			                    						var str = "<?php if($product_allergence->prefix == ''){ echo $product_allergence->ka_name; }else{ echo $product_allergence->ka_name.' ('.$product_allergence->prefix.')';};?>";
						                    			var combine_id = "<?php echo $product_allergence->prefix.'#'.$product_allergence->ka_name.'#'.$product_allergence->ka_id.'#'.$product_allergence->kp_id.'#0';?>";
				                    					allg_datas.push({id:combine_id,text:stripslashes(str)});
				                    					<?php if(($product_allergence->ka_id == 1) || ($product_allergence->ka_id == 8)){?>
						                    				var str = '<?php echo '(';?>';
			                    							var combine_id = '<?php echo '#(#0#'.$product_allergence->kp_id.'#'.$product_allergence->ka_id;?>';
						                    				allg_datas.push({id:combine_id,text:stripslashes(str)});
						                    				<?php if(!empty($product_sub_allergences)){?>
								                    			<?php foreach ($product_sub_allergences as $product_sub_allergence){?>
					                    						<?php if(($product_sub_allergence->kp_id == $product_allergence->kp_id) && ($product_allergence->ka_id == $product_sub_allergence->parent_ka_id)){?>
								                    					var str = "<?php echo $product_sub_allergence->sub_ka_name;?>";
								                    					var combine_id = "<?php echo '#'.$product_sub_allergence->sub_ka_name.'#'.$product_sub_allergence->sub_ka_id.'#'.$product_sub_allergence->kp_id.'#'.$product_sub_allergence->parent_ka_id;?>";
								                    					allg_datas.push({id:combine_id,text:stripslashes(str)});
						                    				<?php }}}?>
						                    				var str = '<?php echo ')';?>';
			                    							var combine_id = '<?php echo '#)#0#'.$product_allergence->kp_id.'#'.$product_allergence->ka_id;?>';
						                    				allg_datas.push({id:combine_id,text:stripslashes(str)});
			                    						<?php }?>
					                    			<?php }?>
			                    					</script>

			                    				<?php $conuter++; }?>
	                    					<?php }?>
	                                    </tbody>
	                                </table>
			              		</div>

			              		<?php }else{?>
			              		<textarea class="text small" rows="8" style="width: 425px;" id="all" name="allergence" <?php if($this->company->k_assoc){?>placeholder="<?php echo _('Please add allergence separated with comma(,).');?>"<?php }?> ><?php if($product_information && $product_information['0']->allergence){ echo $product_information['0']->allergence; }?></textarea>
			              		<?php }?>


			              	</td>
			              </tr>

			              <tr id="trace_container" >
			              	<td class="textlabel">
			              		<?php echo _("Can contain traces");?> :
			              	</td>
			              	<td>
			              		<?php if( $this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium'){?>
			              		<input class="text small" style="width: 600px; height: 100px;" id="traces_of" name="traces_of" value="<?php if($product_information && $product_information['0']->allergence){ echo $product_information['0']->allergence ; }?>" />
			              		<input type="hidden" id="rp_count_t" value="0" />
			              		<input type="hidden" id="lp_count_t" value="0" />
			              		<!-- <input type="button" name="" id="" value="(" onclick="add_symbol('(','traces_of');" />
			              		<input type="button" name="" id="" value=")" onclick="add_symbol(')','traces_of');" /> -->
			              		<div style="width: 50%;">
			              			<table border="0" class="override" >
	                                	<tbody id="kp_traces" class="" style="display: none">
	                    					<?php if(!empty($product_traces)){?>
	                    					<tr><td colspan="3">&nbsp;</td></tr>
	                    					<?php  $conuter = 0;?>
	                    					<?php foreach ($product_traces as $product_trace){?>
			                    					<?php if($product_trace->kp_id){?>
			                    					<tr id="traces_<?php echo $product_trace->kp_id;?>_<?php echo $product_trace->kt_id;?>">
														<td width="70%">
															<p class="draggabled">
																<input type="text" name="kp_t_names_prefix[]" class="text short prefix" value="<?php echo $product_trace->prefix;?>" />
																<input type="text" name="kp_traces_names[]" class="text medium name" value="<?php echo $product_trace->kt_name;?>" style="width:70%;<?php if(!$product_trace->kt_id){?>font-weight:bold;<?php }?>"s disabled="disabled" />
																<input type="hidden" class="kp_traces_ids" name="kp_traces_ids[]" value="<?php echo $product_trace->kt_id; ?>" />
																<input type="hidden" class="kp_traces_pid" name="kp_traces_pid[]" value="<?php echo $product_trace->kp_id; ?>" />
															</p>
														</td>
														<td width="10%">
															<!-- <img width="18" border="0" onClick="javascript:deleteIngredients(this);" src="<?php echo base_url();?>assets/cp/images/delete.gif" /> -->
														</td>
														<td width="5%" style="text-align:right">
															<!-- <img width="18" border="0" class="handle draggable_t" src="<?php echo base_url();?>assets/cp/images/move.png" style="cursor: pointer;" title="<?php echo _("Drag me");?>" /> -->
															<img width="18" border="0" class="handle" src="<?php echo base_url();?>assets/cp/images/upload.png" style="cursor: pointer;" title="<?php echo _("Drag me");?>" onclick="drop_me('traces_<?php echo $product_trace->kp_id;?>_<?php echo $product_trace->kt_id;?>','traces_of');" />
														</td>
														<td width="10%">
														</td>
														<td width="5%" style="text-align:right">
															<?php if(!($conuter)){?>
																<img width="18" border="0" class="handle" src="<?php echo base_url();?>assets/cp/images/upload_all.png" style="cursor: pointer;" title="<?php echo _("Add All");?>" onclick="drop_me_all('pro_<?php echo $product_trace->kp_id;?>','traces_of');" />
															<?php }?>
														</td>
													</tr>
													<?php }?>


													<script>
			                    					<?php if(!$product_trace->kp_id){?>
				                    					<?php if($product_trace->kt_name == '('){ ?>
				                    						$('#lp_count_t').val(parseInt($('#lp_count_t').val()) + 1);
				                    						traces_datas.push({id:'lp#'+parseInt($('#lp_count_t').val()),text:'. ( .'});
				                    					<?php }else if($product_trace->kt_name == ')'){ ?>
				                    						$('#rp_count_t').val(parseInt($('#rp_count_t').val()) + 1);
				                    						traces_datas.push({id:'rp#'+parseInt($('#rp_count_t').val()),text:'. ) .'});
				                    					<?php }else{ ?>
				                    						traces_datas.push({id:"<?php echo $product_trace->kt_name; ?>",text:"<?php echo stripslashes($product_trace->kt_name); ?>"});
				                    					<?php }?>

			                    					<?php }else{?>
			                    						var str = "<?php if($product_trace->prefix == ''){ echo $product_trace->kt_name; }else{ echo $product_trace->ka_name.' ('.$product_trace->prefix.')';};?>";
				                    					var combine_id = "<?php echo $product_trace->prefix.'#'.$product_trace->kt_name.'#'.$product_trace->kt_id.'#'.$product_trace->kp_id;?>";
				                    					traces_datas.push({id:combine_id,text:stripslashes(str)});
			                    					<?php }?>

			                    					</script>

			                    				<?php $conuter++; } ?>
	                    					<?php }?>
	                                    </tbody>
	                                </table>
			              		</div>
			              		<?php }else{?>
			              			<textarea class="text small" rows="8" style="width: 425px;" id="traces" name="traces_of" <?php if($this->company->k_assoc){?>placeholder="<?php echo _('Please add traces separated with comma(,).');?>"<?php }?> ><?php if($product_information && $product_information['0']->traces_of){ echo $product_information['0']->traces_of ; }?></textarea>
			              		<?php }?>

			              	</td>
			              </tr>

			              <?php if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium'){?>
			           	  <?php if(isset($product_information) && !empty($product_information)){ $recipe_wt =  $product_information[0]->recipe_weight*1000; }else{ $recipe_wt = 0; } ?>
			              <tr id="nutri_values" style="display: <?php if(isset($nutri_values) && !empty($nutri_values)){?><?php }else{?>none;<?php }?>">
			              	<td class="textlabel">
			              		<?php echo _("Nutrition Values");?> :
			              	</td>
			              	<td>
			              		<table>
			              			<tr>
			              				<td><strong><?php echo _("Nutritional Information");?></strong></td>
			              				<td><strong><?php echo _("In 100g of product");?></strong></td>
			              				<td><strong id="_x"><?php echo "per ".$recipe_wt."g";?></strong></td>
			              			</tr>
			              			<tr>
			              				<td><?php echo _("Energy Value (Kcal)");?></td>
			              				<td id="e_val_1"><?php if(isset($nutri_values) && !empty($nutri_values)){ echo defined_money_format($nutri_values['e_val_1'],0); } ?></td>
			              				<td id="e_val_1_x"><?php if(isset($nutri_values) && !empty($nutri_values)){ echo defined_money_format($nutri_values['e_val_1']/100*$recipe_wt,0); } ?></td>
			              			</tr>
			              			<tr>
			              				<td><?php echo _("Energy Value (KJ)");?></td>
			              				<td id="e_val_2"><?php if(isset($nutri_values) && !empty($nutri_values)){ echo defined_money_format($nutri_values['e_val_2'],0); } ?></td>
			              				<td id="e_val_2_x"><?php if(isset($nutri_values) && !empty($nutri_values)){ echo defined_money_format($nutri_values['e_val_2']/100*$recipe_wt,0); } ?></td>
			              			</tr>
			              			<tr>
			              				<td><?php echo _("Proteins (gm)");?></td>
			              				<td id="proteins"><?php if(isset($nutri_values) && !empty($nutri_values)){ echo defined_money_format($nutri_values['protiens'],1); } ?></td>
			              				<td id="proteins_x"><?php if(isset($nutri_values) && !empty($nutri_values)){ echo defined_money_format($nutri_values['protiens']/100*$recipe_wt,1); } ?></td>
			              			</tr>
			              			<tr>
			              				<td>
			              					<p><?php echo _("Carbohydrates (gm)");?></p></br>
			              					<p>&nbsp;&nbsp;&nbsp;<?php echo '-'._("Sugar (gm)");?></p></br>
			              					<p>&nbsp;&nbsp;&nbsp;<?php echo '-'._("Polyolen (gm)");?></p></br>
			              					<p>&nbsp;&nbsp;&nbsp;<?php echo '-'._("Farina (gm)");?></p>
			              				</td>
			              				<td >
			              					<p id="carbo"><?php if(isset($nutri_values) && !empty($nutri_values)){ echo defined_money_format($nutri_values['carbo'],1); } ?></p></br>
			              					<p id="sugar"><?php if(isset($nutri_values) && !empty($nutri_values)){ echo defined_money_format($nutri_values['sugar'],1); } ?></p></br>
			              					<p id="poly"><?php if(isset($nutri_values) && !empty($nutri_values)){ echo defined_money_format($nutri_values['poly'],1); } ?></p></br>
			              					<p id="farina"><?php if(isset($nutri_values) && !empty($nutri_values)){ echo defined_money_format($nutri_values['farina'],1); } ?></p>
			              				</td>
			              				<td>
			              					<p id="carbo_x"><?php if(isset($nutri_values) && !empty($nutri_values)){ echo defined_money_format($nutri_values['carbo']/100*$recipe_wt,1); } ?></p></br>
			              					<p id="sugar_x"><?php if(isset($nutri_values) && !empty($nutri_values)){ echo defined_money_format($nutri_values['sugar']/100*$recipe_wt,1); } ?></p></br>
			              					<p id="poly_x"><?php if(isset($nutri_values) && !empty($nutri_values)){ echo defined_money_format($nutri_values['poly']/100*$recipe_wt,1); } ?></p></br>
			              					<p id="farina_x"><?php if(isset($nutri_values) && !empty($nutri_values)){ echo defined_money_format($nutri_values['farina']/100*$recipe_wt,1); } ?></p>
			              				</td>
			              			</tr>
			              			<tr>
			              				<td>
			              					<p><?php echo _("Fats (gm)");?></p></br>
			              					<p>&nbsp;&nbsp;&nbsp;<?php echo '-'._("Saturated Fats (gm)");?></p></br>
			              					<p>&nbsp;&nbsp;&nbsp;<?php echo '-'._("Single Unsaturated Fats (gm)");?></p></br>
			              					<p>&nbsp;&nbsp;&nbsp;<?php echo '-'._("Multi Unsaturated Fats (gm)");?></p>
			              				</td>
			              				<td >
			              					<p id="fats"><?php if(isset($nutri_values) && !empty($nutri_values)){  echo defined_money_format($nutri_values['fats'],1); } ?></p></br>
			              					<p id="sat_fats"><?php if(isset($nutri_values) && !empty($nutri_values)){  echo defined_money_format($nutri_values['sat_fats'],1); } ?></p></br>
			              					<p id="single_fats"><?php if(isset($nutri_values) && !empty($nutri_values)){  echo defined_money_format($nutri_values['single_fats'],1); } ?></p></br>
			              					<p id="multi_fats"><?php if(isset($nutri_values) && !empty($nutri_values)){  echo defined_money_format($nutri_values['multi_fats'],1); } ?></p>
			              				</td>
			              				<td >
			              					<p id="fats_x"><?php if(isset($nutri_values) && !empty($nutri_values)){  echo defined_money_format($nutri_values['fats']/100*$recipe_wt,1); } ?></p></br>
			              					<p id="sat_fats_x"><?php if(isset($nutri_values) && !empty($nutri_values)){  echo defined_money_format($nutri_values['sat_fats']/100*$recipe_wt,1); } ?></p></br>
			              					<p id="single_fats_x"><?php if(isset($nutri_values) && !empty($nutri_values)){  echo defined_money_format($nutri_values['single_fats']/100*$recipe_wt,1); } ?></p></br>
			              					<p id="multi_fats_x"><?php if(isset($nutri_values) && !empty($nutri_values)){  echo defined_money_format($nutri_values['multi_fats']/100*$recipe_wt,1); } ?></p>
			              				</td>
			              			</tr>
			              			<tr>
			              				<td><?php echo _("Salt (gm)");?></td>
			              				<td id="salt"><?php if(isset($nutri_values) && !empty($nutri_values)){ echo defined_money_format($nutri_values['salt'],1); } ?></td>
			              				<td id="salt_x"><?php if(isset($nutri_values) && !empty($nutri_values)){ echo defined_money_format($nutri_values['salt']/100*$recipe_wt,1); } ?></td>
			              			</tr>
			              			<tr>
			              				<td><?php echo _("Fibers (gm)");?></td>
			              				<td id="fibers"><?php if(isset($nutri_values) && !empty($nutri_values)){ echo defined_money_format($nutri_values['fibers'],1); } ?></td>
			              				<td id="fibers_x"><?php if(isset($nutri_values) && !empty($nutri_values)){ echo defined_money_format($nutri_values['fibers']/100*$recipe_wt,1); } ?></td>
			              			</tr>
			              		</table>

			              	</td>
			              </tr>

			              <tr id="nutrition_loading" style="display: none">
			             	<td class="textlabel">
			              		<?php echo _("Nutrition Values");?> :
			              	</td>
			              	<td>
			              		<img alt="" src="<?php echo base_url().'assets/images/loading2.gif';?>" style="margin: 30px; width: 40px;">
			              	</td>
			              </tr>
			              <?php }?>

			              <?php if(isset($fixed_pdf)){?>
			              <tr>
			              	<td class="textlabel"><?php echo _("Product Sheet");?></td>
			              	<td id="fats"><img src="<?php echo base_url();?>assets/images/pdf2.jpeg"><a href="<?php echo  $this->config->item('fdd_url').'assets/cp/uploads/'.$fixed_pdf;?>"> <?php echo $fixed_pdf;?></a></td>
			              </tr>
			              <?php }?>
			              <?php if($product_information):?>
			              <tr>
			                <td class="save_b" colspan="2">
			                	<!-- <input type="submit" value="<?php echo _("Update");?>" class="submit" id="update" name="update" onclick="return form_validate();"> -->
			                	<input type="button" value="<?php echo _("Update");?>" class="submit" id="update" name="update">
			                  	<input type="hidden" value="add_edit" id="act" name="act">
			                  	<input type="hidden" value="update" id="add_update" name="add_update">
			                </td>
			              </tr>
			              <?php else:?>
			              <tr>
			                <td class="save_b" colspan="2">
			                	<!-- <input type="submit" value="<?php echo _('Send')?>" class="submit" id="add" name="add" onclick="return form_validate();"> -->
			                	<input type="button" value="<?php echo _('Send')?>" class="submit" id="add" name="add">
			                	<input type="hidden" value="add" id="add_update" name="add_update">
			                </td>
			              </tr>
			              <?php endif;?>
			           	</tbody>
		          </table>
		          <input type="hidden" value="" id="hidden_fdds_quantity" name="hidden_fdds_quantity">
		          <input type="hidden" value="" id="hidden_own_pro_quantity" name="hidden_own_pro_quantity">
		          <input type="hidden" value="0" id="hidden_fdd_total" name="hidden_fdd_total">
		          <input type="hidden" value="0" id="hidden_own_total" name="hidden_own_total">
		          <input type="hidden" value="2" id="semi_products" name="semi_products">
		        </form>
		        <script language="javascript" type="text/javascript">
		        	$('form#frm_products_addedit #add,form#frm_products_addedit #update').click(function(){
		        		if(form_validate()){
		        			needToConfirm = false;
							$('form#frm_products_addedit').submit();
			        	}
			        });

		        	function form_validate(){
			        	// checking product name
			        	if($("#proname").val() == ""){
				        	alert("<?php echo _('please give the product name.');?>");
				        	$('#proname').focus();
				        	return false;
				        }
			        	return true;
		        	}

					function reset_wt(){
						tb_show("<?php echo _('New weight');?>", '#TB_inline?height=200&amp;width=300&amp;inlineId=wt_resetter');
						$('#TB_ajaxContent #insert_new_wt').val($("#recipe_weight").val());
					}

					function reset_wt_submit(){
						var old_wt = $("#recipe_weight").val();
						var new_wt = $("#TB_ajaxContent #insert_new_wt").val();
						var ratio = new_wt/old_wt;

						$(".own_product_quants,.fdd_product_quants").each(function(){
							var cur_val = parseFloat($(this).val());
							$(this).val((cur_val*ratio).toFixed(0));
						});

						$("#recipe_weight").val(new_wt);
						$("#recipe_weight_span").html(new_wt);

						quant_change();
						tb_remove();

// 						alert(old_wt);
// 						alert(new_wt);
// 						alert(ratio);
					}

				</script>
	      </div>
	    </div>
  	</div>
</div>
<!-- /content -->

<div id="credit_require" style="display: none">
   	<p><?php echo _('Sorry! Currently You have no credit to use a FoodDESK product.');?></p>
   	<p><?php echo _('To buy credits, choose a package.');?></p>
   	<ul>
   		<li><a onclick="add_credit(100)" href="javascript:;"><?php echo _('100 products/credits for 10');?>&euro;</a></li>
   		<li><a onclick="add_credit(200)" href="javascript:;"><?php echo _('200 products/credits for 15');?>&euro;</a></li>
   	</ul>
</div>

<div id="wt_resetter" style="display: none">
	<input type="number" style="width:100px" class="text" id="insert_new_wt" value="" min="0" step=".001">
	<button onclick="reset_wt_submit()"><?php echo _('Submit'); ?></button>
</div>

<div id="shortname_renamer" style="display: none">
	<input type="text" style="width:200px" class="text" id="rename_it" value="" >
	<input type="hidden" style="width:200px" class="text" id="rename_hidden" value="" >
	<button onclick="do_rename()"><?php echo _('Submit'); ?></button>
</div>