<script src="<?php echo base_url();?>assets/kcp/js/select2/select2.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/kcp/js/select2/select2.css" media="screen">
<script src="<?php echo base_url(); ?>assets/plugins/jquery-ui-1.12.0.custom/jquery-ui.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/ui/jquery.ui.theme.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/ui/jquery.ui.core.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/ui/jquery.ui.autocomplete.css">
<script type="text/javascript">
	var product_choose_txt = "<?php echo _('Choose a product to make it same.');?>";
	var rename = "<?php echo _("Rename");?>";
	var rename_pro = "<?php echo _("Rename product");?>";
	var add = "<?php echo _("Add");?>";
	var add_pro = "<?php echo _("Add producer");?>";
	var add_supplr = "<?php echo _('Add supplier');?>";
	var upload_sheet_txt = "<?php echo _('Upload sheet');?>";
	var assign = "<?php echo _('Assign');?>";
	var successfully_updated = "<?php echo _('successfully updated');?>";
	var remark = "<?php echo _('remark');?>";
	var none="<?php echo _("None");?>";
	var Supplier="<?php echo _("Supplier");?>";
	var Producer="<?php echo _("Producer");?>";
	var Artnbr="<?php echo _("Artnbr");?>";
	var Save="<?php echo _("Save");?>";
	var Cancel="<?php echo _("Cancel");?>";
	var update_failed = "<?php echo _("Update failed");?>";
	var avalable_tage = new Array();
	avalable_tage[0] = {'value':-1,'label':'<?php echo _('All products'); ?>'};
	<?php  $i = 1; ?>
	<?php if(!empty($products)): ?>
		<?php foreach($products as $pro): ?>
			avalable_tage[<?php echo $i;?>] = {'value':'<?php echo $pro->id;?>','label':'<?php echo $pro->proname; ?>'};
			<?php $i++; ?>
		<?php endforeach; ?>
	<?php endif; ?>
	$("supplier").select2("data", [], true);
</script>
<script src="<?php echo base_url(); ?>assets/cp/new_js/pws_js.js" type="text/javascript"></script>
<style>
	.fc-first th {
	    background: none repeat scroll 0 0 black !important;
	    border: medium none !important;
	}

	#TB_window {
    margin-top: -200px !important;
	}
	.submit {
	-moz-box-shadow: 0px 1px 0px 0px #f0f7fa;
	-webkit-box-shadow: 0px 1px 0px 0px #f0f7fa;
	box-shadow: 0px 1px 0px 0px #f0f7fa;
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #33bdef), color-stop(1, #019ad2));
	background:-moz-linear-gradient(top, #33bdef 5%, #019ad2 100%);
	background:-webkit-linear-gradient(top, #33bdef 5%, #019ad2 100%);
	background:-o-linear-gradient(top, #33bdef 5%, #019ad2 100%);
	background:-ms-linear-gradient(top, #33bdef 5%, #019ad2 100%);
	background:linear-gradient(to bottom, #33bdef 5%, #019ad2 100%);
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#33bdef', endColorstr='#019ad2',GradientType=0);
	background-color:#33bdef;
	-moz-border-radius:4px;
	-webkit-border-radius:4px;
	border-radius:4px;
	border:1px solid #057fd0;
	display:inline-block;
	cursor:pointer;
	color:#ffffff;
	font-family:arial;
	font-size:13px;
	padding:3px 4px;
	text-decoration:none;
	text-shadow:0px -1px 0px #5b6178;
}
.submit:hover {
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #019ad2), color-stop(1, #33bdef));
	background:-moz-linear-gradient(top, #019ad2 5%, #33bdef 100%);
	background:-webkit-linear-gradient(top, #019ad2 5%, #33bdef 100%);
	background:-o-linear-gradient(top, #019ad2 5%, #33bdef 100%);
	background:-ms-linear-gradient(top, #019ad2 5%, #33bdef 100%);
	background:linear-gradient(to bottom, #019ad2 5%, #33bdef 100%);
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#019ad2', endColorstr='#33bdef',GradientType=0);
	background-color:#019ad2;
}
.submit:active {
	position:relative;
	top:1px;
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

.pws_excel{
	float: right;
	-moz-box-shadow: 0px 1px 0px 0px #f0f7fa;
	-webkit-box-shadow: 0px 1px 0px 0px #f0f7fa;
	box-shadow: 0px 1px 0px 0px #f0f7fa;
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #33bdef), color-stop(1, #019ad2));
	background:-moz-linear-gradient(top, #33bdef 5%, #019ad2 100%);
	background:-webkit-linear-gradient(top, #33bdef 5%, #019ad2 100%);
	background:-o-linear-gradient(top, #33bdef 5%, #019ad2 100%);
	background:-ms-linear-gradient(top, #33bdef 5%, #019ad2 100%);
	background:linear-gradient(to bottom, #33bdef 5%, #019ad2 100%);
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#33bdef', endColorstr='#019ad2',GradientType=0);
	background-color:#33bdef;
	-moz-border-radius:4px;
	-webkit-border-radius:4px;
	border-radius:4px;
	border:1px solid #057fd0;
	display:inline-block;
	cursor:pointer;
	color:#ffffff;
	font-family:arial;
	font-size:13px;
	padding:3px 4px;
	text-decoration:none;
	text-shadow:0px -1px 0px #5b6178;
}
.pws_excel:hover {
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #019ad2), color-stop(1, #33bdef));
	background:-moz-linear-gradient(top, #019ad2 5%, #33bdef 100%);
	background:-webkit-linear-gradient(top, #019ad2 5%, #33bdef 100%);
	background:-o-linear-gradient(top, #019ad2 5%, #33bdef 100%);
	background:-ms-linear-gradient(top, #019ad2 5%, #33bdef 100%);
	background:linear-gradient(to bottom, #019ad2 5%, #33bdef 100%);
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#019ad2', endColorstr='#33bdef',GradientType=0);
	background-color:#019ad2;
}
.pws_excel:active {
	position:relative;
	top:1px;
}
</style>
<!-- MAIN -->
<div id="main">
	<div id="main-header">
    	<h2><?php echo _('Manage Products with no ingredient'); ?></h2>
      	<span class="breadcrumb"><a href="<?php echo base_url()?>cp/cdashboard"><?php echo _('Home')?></a> &raquo; <?php echo _('Products')?></span>
	</div>

	<div id="content">
    	<div id="content-container">
        	<div class="box">
          		<h3><?php echo _('Products'); ?></h3>
          		<div class="table">
            		<table cellspacng="0">
            			<tbody>
            				 <tr id="notice"><td id="notice_msg"  colspan='2'><?php echo _("The section has been added in the new Controlepanel(2.0)" ); ?><?php echo "<br>"; ?><?php echo _("Please proceed with new Controlepanel(2.0)")?><?php echo "<br>"; ?><a target="_blank" style="color: #8c8c00;text-decoration: none;" href="<?php echo rtrim($this->config->item('new_obs_url'),'/') ?>"><?php echo rtrim($this->config->item('new_obs_url'),'/') ?></a></td></tr>
            			</tbody>
              			<!-- <thead>
                			<tr>
                  				<td class="notice_text" colspan="8" style="text-align:center">** <?php echo _('You can update product\'s ingredients by clicking on Product\'s name manually, if products name found exactly same as any FoodDESK product, then you can directly add ingredients, traces and allergence by clicking on that product.'); ?></td>
                			</tr>
                			<tr>
                				<td colspan="8">
                					<span><?php echo _("Search by product name");?></span>
                					<input id="search_product" type="text" class="text" style="width:20%">
                					<a href="<?php echo base_url()?>cp/products/pws_excel" class="pws_excel" ><?php echo _('Download excel of PWS'); ?></a>
                				</td>
                			</tr>
                			<tr>
                  				<th width="30%"><?php echo _('Product Name'); ?></th>
                  				<th width="15%"><?php echo _('Merk'); ?><br><?php echo _('Supplier'); ?></th>
                  				<th width="25%"><?php echo _('Remark by FOODDESK');?></th>
                  				<th width="15%"><?php echo _('Used in Recipe');?></th>
                  				<th width="10%"><?php echo _('Action');?></th>
                  				<?php if($this->session->userdata('login_via') == 'mcp' || $this->session->userdata('menu_type') != 'fdd_light'){ ?>
                  					<th width="5%"><?php echo _('View suggestions'); ?></th>
                  				<?php } ?>
                			</tr>
						</thead>
              			<tbody>
                		<?php if( sizeof($products) > 0 || sizeof($products_processing) > 0 ){ ?>
                			<div id="loadingmessage" style="display:none;margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;">
								<img src="<?php echo base_url(''); ?>assets/cp/images/ajax-loading.gif" style="position: absolute; color: White; top: 50%; left: 45%;"/>
							</div>
							<?php  if( !empty( $products ) ){
								foreach($products as $product){ ?>
								<tr id="tr_<?php echo $product->id; ?>">
									<td data-proid="<?php echo $product->id?>" data-field="proname" <?php if($product->refused){echo 'style="background:#FBE5E5;"';}?> >
										<input class="text edit_value" type="text" value="<?php echo stripslashes($product->proname); ?>"><a class="done" href="javascript:;"><img alt="update" src="<?php echo base_url()?>assets/cp/images/save.png"></a>
									</td>

									<td <?php if($product->refused){echo 'style="background:#FBE5E5;"';}?> >
										<a class="thickbox" title="<?php echo stripslashes($product->proname); ?>" href="<?php echo base_url().'cp/products/producer_consumer_box/'.$product->id.'/'.$product->refused.'?height=400&width=600'?>"><?php echo _("Open Window");?></a>


										<?php if (($product ->fdd_producer_id || $product ->fdd_supplier_id) && ($product ->fdd_prod_art_num || $product ->fdd_supp_art_num)){?>
												<img alt="" src="<?php echo  base_url();?>/assets/cp/images/chk_on.png">
										<?php }?>
									</td>

									<td <?php if($product->refused){echo 'style="background:#FBE5E5;"';}?> >
										<?php if ($product->remark_refused == "") echo _('-'); else echo $product->remark_refused ;?>
									</td>

									<td <?php if($product->refused){echo 'style="background:#FBE5E5;"';}?> >
										<?php if( $product->recipes == '' ) echo _('-');
										foreach ($product->recipes as $key => $value) {
											$len = count($product->recipes) - 1;
											echo $value['proname'];
											if( $key < $len ) {
												echo ' , ';
											}
										}
										?>
									</td>

									<td <?php if($product->refused){echo 'style="background:#FBE5E5;"';}?> >
									<?php if($product->prosheet_pws == '' && $product->exist_in_pending == 0){?>
										<a href="javascript:;" onclick="upload_sheet(<?php echo $product->id;?>);" class="upload_sheet"><?php echo _("Upload sheet");?></a>
									<?php }else{?>
										<a href="<?php echo base_url();?>assets/cp/fdd_pdf/download.php?f=<?php echo end(explode("##",$product->prosheet_pws));?>" class="get_uploaded"><?php echo _("UPLOADED");?></a>
									<?php }?>
									</td>

									<?php if($this->session->userdata('login_via') == 'mcp' || $this->session->userdata('menu_type') != 'fdd_light'){ ?>
									<td <?php if($product->refused){echo 'style="background:#FBE5E5;"';}?> >
										<input class="submit" type="button" value="<?php echo _('Check More suggestions')?>" onclick="check_more_suggestion(<?php echo $product->id; ?>)" />
									</td>
									<?php } ?>
		                		</tr>
							<?php }
							}
							if( !empty( $products_processing ) ){ ?>
								<tr>
									<td colspan="6">
										<p><b><?php echo _( "Processing" );?></b></p>
									</td>
								</tr>
							<?php foreach($products_processing as $product){ ?>
								<tr id="tr_<?php echo $product->id; ?>">
									<td data-proid="<?php echo $product->id?>" data-field="proname" <?php if($product->refused){echo 'style="background:#FBE5E5;"';}?> >
										<input class="text edit_value" readonly type="text" value="<?php echo stripslashes($product->proname); ?>"><img alt="update" src="<?php echo base_url()?>assets/cp/images/save.png">
									</td>

									<td <?php if($product->refused){echo 'style="background:#FBE5E5;"';}?> >
										<a class="thickbox" title="<?php echo stripslashes($product->proname); ?>" href="<?php echo base_url().'cp/products/producer_consumer_box/'.$product->id.'/'.$product->refused.'?height=400&width=600'?>"><?php echo _("Open Window");?></a>


										<?php if (($product ->fdd_producer_id || $product ->fdd_supplier_id) && ($product ->fdd_prod_art_num || $product ->fdd_supp_art_num)){?>
												<img alt="" src="<?php echo  base_url();?>/assets/cp/images/chk_on.png">
										<?php }?>
									</td>

									<td <?php if($product->refused){echo 'style="background:#FBE5E5;"';}?> >
										<?php if ($product->remark_refused == "") echo _('-'); else echo $product->remark_refused ;?>
									</td>

									<td <?php if($product->refused){echo 'style="background:#FBE5E5;"';}?> >
										<?php if( $product->recipes == '' ) echo _('-');
										foreach ($product->recipes as $key => $value) {
											$len = count($product->recipes) - 1;
											echo $value['proname'];
											if( $key < $len ) {
												echo ' , ';
											}
										}
										?>
									</td>

									<td <?php if($product->refused){echo 'style="background:#FBE5E5;"';}?> >
									<?php if ($product->exist_in_pending == 1){?>
										<a href="javascript:;"  class="upload_sheet"><?php echo _("Processing");?></a>
									<?php }?>
									</td>

									<?php if($this->session->userdata('login_via') == 'mcp' || $this->session->userdata('menu_type') != 'fdd_light'){ ?>
									<td <?php if($product->refused){echo 'style="background:#FBE5E5;"';}?> >
										<input class="submit" type="button" value="<?php echo _('Check More suggestions')?>" onclick="check_more_suggestion(<?php echo $product->id; ?>)" />
									</td>
									<?php } ?>
		                		</tr>
							<?php }
							}?>
						<?php }else{ ?>
							<tr>
								<td>
									<?php echo _('No product in fdd database.');?>
								</td>
							</tr>
						<?php } ?>
						</tbody> -->
            		</table>

          		</div><!-- /table -->
        	</div><!-- /box -->
        	<!-- box for gs1 products -->
        	<div class="box">
          		<h3><?php echo _('Unapproved GS1 Products'); ?></h3>
          		<div class="table">
          			<table cellspacing="0">
          				<tbody>
          					 <tr id="notice"><td id="notice_msg"  colspan='2'><?php echo _("The section has been added in the new Controlepanel(2.0)" ); ?><?php echo "<br>"; ?><?php echo _("Please proceed with new Controlepanel(2.0)")?><?php echo "<br>"; ?><a target="_blank" style="color: #8c8c00;text-decoration: none;" href="<?php echo rtrim($this->config->item('new_obs_url'),'/') ?>"><?php echo rtrim($this->config->item('new_obs_url'),'/') ?></a></td></tr>
          				</tbody>
              			<!-- <thead>
							<tr>
                  				<th width="30%"><?php echo _('Product Name'); ?></th>
                  				<th width="15%"><?php echo _('Used in Recipe');?></th>
                  				<th width="10%"><?php echo _('Action');?></th>
                			</tr>
						</thead>
              			<tbody>
              				<?php  if( !empty( $gs1_products ) ){
								foreach($gs1_products as $key => $value){ ?>
								<tr id="tr_<?php echo $value['p_id']; ?>">
								 	<td>
								 		<?php echo $value[ 'p_name' ];?>
								 	</td>
								 	<td>
								 		<?php echo $value[ 'in_recipe' ];?>
								 	</td>
								 	<td class="col-lg-2">
										<a href="javascript:;"  class="upload_sheet"><?php echo _("Processing");?></a>
									</td>
								</tr>
								<?php
								}
							} else {?>
								<tr>
									<td>
										<span class="error"><?php echo _('No Unapproved GS1 Products.');?></span>
									</td>
								</tr>
							<?php } ?>
              			</tbody> -->
          			</table>
          		</div>
      		</div>

      	</div><!-- /content-container -->
	</div><!-- /content -->

	<div id="my_tb_holder" style="display: none">

	</div>

	<div id="rename_master" style="display: none">

	</div>

	<div id="add_supplier" style="display: none">

	</div>

	<div id="add_real_supplier" style="display: none">

	</div>

	<div id="upload_sheet" style="display: none;">
		<form action="" enctype="multipart/form-data" method="POST" style="text-align:center;">
			<input type="file" name="sheet" /><br/><br/>
			<input type="hidden" name="pro_id" id="pro_id" value="" />
			<input type="submit" class="submit" name="upload" value="<?php echo _("Upload");?>" />
		</form>
	</div>

	<div id="producer_supplier_box" style="display: none;">

	</div>


	<div id="display_refuse_remark" style="display: none">

	</div>
