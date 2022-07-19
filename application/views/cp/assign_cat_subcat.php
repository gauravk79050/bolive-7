<script type="text/javascript" charset="utf8" src="<?php echo base_url();?>assets/cp/new_js/pace.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/cp/css/pace.css" type="text/css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/cp/css/jquery.dataTables.min.css" type="text/css"/>

<style>
.inp_fld, #product_table_filter >label>input {
    background: #fcfcfc none repeat scroll 0 0;
    border: 1px solid #cccccc;
    color: #333333;
    display: inline;
    padding: 5px;
}
#product_table thead >tr >td, tfoot >tr >td
{padding: 20px 0px !important;}
.va {
    float: left;
    margin-left: 365px;
    margin-top: 15px;
    width: 12%;
}
.vb {
    float: left;
    margin-left: 20px;
    margin-top: 15px;
    width: 12%;
}
</style>
<script type="text/javascript" charset="utf8" src="<?php echo base_url();?>assets/cp/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
/*jQuery(document).ready(function(){
	window.onload = function(){ document.getElementById("loading").style.display = "none"; }
});*/

jQuery(document).ready(function(){
   var filter_msg = "<?php echo _('Please filter products by category'); ?>";
   $('#product_table').DataTable({
    	"bLengthChange": false,
    	"iDisplayLength": -1,
    	"bPaginate": false,
      "oLanguage": {
        "sEmptyTable": filter_msg
      }, 	   
     	"bInfo": false,
     	"searching": true,
     	"bFilter":true,
     	stateSave:true,
//    	"aaSorting": [[1,'asc']],
//    	"oLanguage": {"sSearch": "<?php echo _('Search').":"?>"},
    	"aoColumnDefs": [{ 'bSortable': false, 'aTargets': [ 0 ] },{ 'bSortable': false, 'aTargets': [ 3 ] },{ 'bSortable': false, 'aTargets': [ 5 ] }]
    });
	$('#product_table_filter label').css('padding','10px 10px'); 
});

function category_change(cat_id,x){
	jQuery.post("<?php echo base_url()?>cp/products/get_sub_category",
		{'cat_id':cat_id},
    	function(data){    	
			jQuery(x).parent().next('td').find(".subcategories_id option").remove();//this is to remove the previous values of drop down menu //
			jQuery(x).parent().next('td').find(".subcategories_id").append("<option value='-1' selected='selected'>-- <?php echo _('Select Subcategory')?> --</option>");
			for(var i=0;i<data.length;i++){
				jQuery(x).parent().next('td').find(".subcategories_id").append(jQuery("<option></option>").val(data[i].id).html(data[i].subname));
			}
		},'json');
}
</script>
<div id="main">
	<div id="main-header">
    	<h2><?php echo _('Asssign Product to Category and Subcategory'); ?></h2>
      	<span class="breadcrumb"><a href="<?php echo base_url()?>cp/cdashboard"><?php echo _('Home')?></a> &raquo; <?php echo _('Products')?></span>
      	
      	<?php if($this->session->flashdata('cat_update')){?>
			<div id="succeed"><strong><?php echo $this->session->flashdata('cat_update')?></strong></div>
		<?php }?>
	</div>
	<!-- <div id="loading">
		<img src="<?php echo base_url(''); ?>assets/cp/images/ajax-loading.gif"/><strong><?php echo _('Loading...')?></strong>
	</div> -->  
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
              <div>
                <div class="va"><strong><?php echo _('Filter By Category')?></strong></div>
                <div class="vb">
                  <select onchange="inCategory(this.value);" class="select" type="select" id="categories_id" name="categories_id">
                    <option value="-1" selected="selected">-- <?php echo _('Select Category'); ?> --</option>
                    <?php foreach ($category_data as $category){ ?>
                      <option value="<?php echo $category->id?>" <?php if($category->id==end($this->uri->segments)){ ?>selected="selected"<?php } ?>>
                          <?php echo stripslashes($category->name); ?>
                      </option>
                    <?php } ?>
                  </select>
              </div>
            </div>       		
            		<table cellspacing="0" id="product_table" width="100%">
              			<thead>
                			<?php if(!empty($category_data)){?>
                			<tr>                			
                				<td width="10%" style="text-align:right">            			
									<strong><?php echo _('Assign Category')?></strong>
								</td>
								<td width="15%">
									<select name="cat-assign-up" onchange="category_change(this.value,this);">
										<option value="-1">-- <?php echo _('Select Category')?> --</option>
									<?php foreach ($category_data as $category){?>
										<option value="<?php echo $category->id?>"><?php echo stripslashes($category->name);?></option>
									<?php }?>
									</select>
								</td>
								<td width="20%">
									<select class="subcategories_id" name="subcat-assign-up">
										<option value="-1">-- <?php echo _('Select Subcategory')?> --</option>					
									</select>
								</td>
								<td width="25%">									
									<input type="submit" name="cat-assign-button-up" value="<?php echo _('Apply')?>">
								</td>
								<td width="15%"></td>	
								<td width="15%"></td>
																						
							</tr>
							<?php }?>
                			<tr>
                				<th class="id_th"></th>
                  				<th><?php echo _('Art. No.'); ?></th>
                  				<th><?php echo _('Product Name'); ?></th>
                  				<th><?php echo _('Description'); ?></th>
                  				<th><?php echo _('Category'); ?></th>
                  				<th><?php echo _('Subcategory'); ?></th>
                  			</tr>
						</thead>
              			<tbody>
              			<?php if(isset($products)){ ?>
              				<?php foreach ($products as $product){?>
              				<tr>
              					<td><input type="checkbox" name="checkbox[]" value="<?php echo $product['id']; ?>"></td>
              					<td><?php echo $product['pro_art_num'] ?></td>
              					<td><b><?php echo stripslashes($product['proname']);?></b></td>
              					<td><?php echo stripslashes($product['prodescription']); ?></td>
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
              				</tr>
              			<?php }}?>
						</tbody>
						<tfoot>
						<?php if(!empty($category_data)){?>
                			<tr>                			
                				<td style="text-align:right">            			
									<strong><?php echo _('Assign Category')?></strong>
								</td>
								<td>
									<select name="cat-assign-down" onchange="category_change(this.value,this);">
										<option value="-1">-- <?php echo _('Select Category')?> --</option>
									<?php foreach ($category_data as $category){?>
										<option value="<?php echo $category->id?>"><?php echo stripslashes($category->name);?></option>
									<?php }?>
									</select>
								</td>
								<td>
									<select class="subcategories_id" name="subcat-assign-down">
										<option value="-1">-- <?php echo _('Select Subcategory')?> --</option>					
									</select>
								</td>
								<td>
									<input type="submit" name="cat-assign-button-down" value="<?php echo _('Apply')?>">
								</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>	
								
							</tr>
							<?php }?>
						</tfoot>	
            		</table>            	
            	</form>
          		</div> -->
        	</div>
      	</div>
	</div>