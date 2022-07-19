<script type="text/javascript" charset="utf8" src="<?php echo base_url();?>assets/cp/new_js/pace.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/cp/css/pace.css" type="text/css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/cp/css/jquery.dataTables.min.css" type="text/css"/>
<script type="text/javascript" src="<?php echo base_url();?>assets/plugins/jquery-ui-1.11.2.custom/jquery-ui.min.js?version=<?php echo version;?>"></script>
<script type="text/javascript" charset="utf8" src="<?php echo base_url();?>assets/cp/js/jquery.dataTables.min.js?version=<?php echo version;?>"></script>

<style>
.inp_fld, #product_table_filter >label>input {
    background: #fcfcfc none repeat scroll 0 0;
    border: 1px solid #cccccc;
    color: #333333;
    display: inline;
    padding: 5px;
}
.my_fdd_img{
	margin-left: 18px!important;
	width: 15px;
	cursor: pointer;
}
.rem_com{
	margin-left: 10px!important;
}

.share_sheet_rec {
    margin-left: 0;
    width: 100%;
    height: 29px;
}
input.text, textarea {
    width: 100%;
}
#product_table thead >tr >td, tfoot >tr >td{
	padding: 20px 0px !important;
}
#share_prod {
    text-decoration: none;
}
#alert-msg >td{
	text-align:center;
	background-color:#EEEEEE;
	color:#FF0000;
	border-radius: 10px;
	padding: 9px !important;
}


#TB_window #TB_ajaxContent{
	height: 136px!important;
}
table tbody tr td{
position: relative;
}
table .ui-autocomplete {
  left: 10px;
  position: absolute !important;
  top: 34px;
}

</style>
<script type="text/javascript">
var select_from_list = "<?php echo _('Please Add company name from suggestion first.');?>";
var add_remark = "<?php echo _('Please Add remark.');?>";
var Remove = "<?php echo _('Remove');?>";
var Shared_with = "<?php echo _('Shared with');?>";
var pending = "<?php echo _("Pending");?>";
var approved = "<?php echo _("Approved");?>";
var rejected = "<?php echo _("Rejected");?>";
var share = "<?php echo _('Share')?>";
var sheet = "<?php echo _("Sheet");?>";
var recipe = "<?php echo _("Recipe");?>";
var with_txt = "<?php echo _("with")?>";

//window.onload = function(){ document.getElementById("loading").style.display = "none"; }

</script>
<div id="main">
	<div id="main-header">
    	<h2><?php echo _('Share products'); ?></h2>
      	<span class="breadcrumb"><a href="<?php echo base_url()?>cp/cdashboard"><?php echo _('Home')?></a> &raquo; <?php echo _('Products')?></span>
      	
      	<?php if($this->session->flashdata('cat_update')){?>
			<div id="succeed"><strong><?php echo $this->session->flashdata('cat_update')?></strong></div>
		<?php }?>
	</div>
	<!-- <div id="loading">
		<img src="<?php //echo base_url(); ?>assets/cp/images/ajax-loading.gif"/><strong><?php //echo _('Loading...')?></strong>
	</div>   -->
	<div id="content">
    	<div id="content-container">
        	<div class="box">
          		<h3><?php echo _('Products'); ?></h3>
          		<table>
          			<tbody>
          				 <tr id="notice"><td id="notice_msg"  colspan='2'><?php echo _("The section has been added in the new Controlepanel(2.0)" ); ?><?php echo "<br>"; ?><?php echo _("Please proceed with new Controlepanel(2.0)")?><?php echo "<br>"; ?><a target="_blank" style="color: #8c8c00;text-decoration: none;" href="<?php echo rtrim($this->config->item('new_obs_url'),'/') ?>"><?php echo rtrim($this->config->item('new_obs_url'),'/') ?></a></td></tr>
          			</tbody>
          		</table>
          		<!-- <div class="table">
          		<form method="post" name="bulk-cat-change" action="">          		
            		<table cellspacing="0" id="product_table" width="100%">
              			<thead>
                			<?php if(!empty($category_data)){?>
                			<tr>
	                			<td id="filter_shared" colspan="3">&nbsp;<input type="checkbox">&nbsp;<?php echo _('Only Show Shared Products')?></td>
								<td><strong><?php echo _('Filter By Category')?></strong></td>	
								<td>
									<select onchange="inCategory(this.value);" class="select" type="select" id="categories_id" name="categories_id">
										<option value="0" selected="selected">-- <?php echo _('Select Category'); ?> --</option>
										<?php foreach ($category_data as $category){ ?>
											<option value="<?php echo $category->id?>" <?php if($category->id==end($this->uri->segments)){ ?>selected="selected"<?php } ?>>
														<?php echo stripslashes($category->name); ?>
												</option>
										<?php } ?>
									</select>
								</td>
							</tr>
							<?php }?>
                			<tr>
                  				<th><?php echo _('Art. No.'); ?></th>
                  				<th><?php echo _('Product Name'); ?></th>
                  				<th><?php echo _('Category'); ?></th>
                  				<th><?php echo _('Subcategory'); ?></th>
                  				<th><?php echo _('Shared'); ?></th>
                  				<th></th>
                  			</tr>
						</thead>
              			<tbody>
              			<?php if(isset($products)){ ?>
              				<?php foreach ($products as $product){?>
              				<tr id="<?php echo $product['id'];?>">
              					<td><?php echo $product['pro_art_num'] ?></td>
              					<td><b><?php echo stripslashes($product['proname']);?></b></td>
              					<td>
	              					<?php $is_cat = false;foreach($category_data as $category){?>
              							<?php if($category->id == $product['categories_id']){?>
              							<?php echo stripslashes($category->name)?>
              						<?php $is_cat = true;}}?>
              						<?php if(!$is_cat){?>
              						<strong><?php echo _('No category')?></strong>
              						<?php }?>              						
              					</td>
              					<td>
              						<?php $is_subcat = false;foreach($subcategory_data as $subcategory){?>
              							<?php if($subcategory['id'] == $product['subcategories_id']){ ?>
              								<?php echo $subcategory['subname']?>
              						<?php $is_subcat = true;}}?>
              						<?php if(!$is_subcat){?>
              						<strong><?php echo _('No subcategory')?></strong>
              						<?php }?>
              					</td>
              					<td><a href="#TB_inline?height=300&width=700&inlineId=share_product" title="<?php echo stripslashes($product['proname']);?>" class="thickbox share_pro_details"><?php echo _('Share');?></a></td>
              					<td><?php if (isset($product['shared_status']) && $product['shared_status'] == 1){?><a href="javascript:;" class="shared_pro_details" onclick="shared_pro_details(this)"><img alt="" src="<?php echo base_url().'assets/images/share-icon.png'?>"><?php }?></a></td>
              				</tr>
              			<?php }}?>
						</tbody>	
            		</table>            	
            	</form>
          	</div> -->
        </div>
    </div>
</div>
<div id="share_product" style="display: none">
	<table>
		<thead>
		</thead>
		<tbody id="shared_prod_comp">
			<tr>
				<td style="vertical-align: middle;">
					<span><?php echo _("Share")?></span>
				</td>
				<td style="width: 20%;vertical-align: top;">
					<select id="prod_sheet_rec" name="prod_sheet_rec" class="share_sheet_rec">
						<option value="sheet"><?php echo _("Sheet");?></option>
						<option value="recipe"><?php echo _("Recipe");?></option>
					</select>
				</td>
				<td>
					<span style="margin-left: 10px;"><?php echo _("with")?></span>
				</td>
				<td class="company_list_details">
					<input type="text" class="text comp_name" onkeyup="get_company_list(this,event)" id="share_comp" placeholder="<?php echo _("Search");?>">
				</td>
				<td>
					<input type="text" class="text rem_com remark_comp_prod" placeholder="<?php echo _("your remark");?>">
				</td>
				<td>
					<img class="my_fdd_img" onclick="add_shr_prod_row(this)" alt="<?php echo _("add");?>" src="<?php echo base_url().'assets/images/plus_btn.png';?>" >&nbsp;&nbsp;
				</td>
				<td>
					<img  onclick="remove_share_product_row(this);" height="16" width="16" border="0" src="<?php echo base_url()?>assets/cp/images/delete.gif" alt="remove" style="cursor: pointer;">
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="7">
					<input type="button" value="<?php echo _("Save");?>" class="text" style="width: 20%;float: right; background-color: #e1e1e1;" id="save_shared_detail" onclick="save_shared_det()">
				</td>
			</tr>
			<tr style="display:none" id="alert-msg">
				<td colspan="7"></td>
			</tr>
		</tfoot>
	</table>
</div>
<div id="shared_product" style="display: none;">
</div>
<input type="hidden" id="share_product_id" value="">
<script type="text/javascript" src="<?php echo base_url();?>assets/cp/js/share_pro.js?version=<?php echo version;?>"></script>