<script src="<?php echo base_url();?>assets/kcp/js/select2/select2.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/kcp/js/select2/select2.css" media="screen">

<script src="<?php echo base_url(); ?>assets/plugins/jquery-ui-1.11.2.custom/jquery-ui.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/jquery-ui-1.11.2.custom/jquery-ui.theme.min.css">

<script type="text/javascript">
	var avalable_tage = new Array();
	avalable_tage[0] = {'value':-1,'label':'<?php echo _('All products'); ?>'};
	<?php  $i = 1; ?>
	<?php if(!empty($products)): ?>
		<?php foreach($products as $pro): ?>
			avalable_tage[<?php echo $i;?>] = {'value':'<?php echo $pro['id'];?>','label':'<?php echo $pro['proname']; ?>'};
			<?php $i++; ?>
		<?php endforeach; ?>
	<?php endif; ?>





	
	$(document).ready(function(){
		

		$( "#search_product" ).autocomplete({
			minLength: 0,
			source: avalable_tage,
			focus: function( event, ui ) {
				$( "#search_product" ).val( ui.item.label );
				return false;
			},
			select: function( event, ui ) {
				window.location = base_url+'cp/cdashboard/custom_pending/'+ui.item.value;
				return false;
			}
		});

		
	})
	

</script>
<style>
	.fc-first th {
	    background: none repeat scroll 0 0 black !important;
	    border: medium none !important;
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
            		<table cellspacing="0">
              			<thead>
                			<tr>
                  				<td class="notice_text" colspan="8" style="text-align:center">** <?php echo _('These products are effected due to changes in added fixed products, Now you need to review it\'s Ingredients, allergence and traces.'); ?></td>
                			</tr>
                			<tr>
                				<td colspan="8">
                					<span>Search by product name</span>
                					<input id="search_product" type="text" class="text" style="width:20%">
                				</td>
                			</tr>
                			<tr>
                  				<th><?php echo _('Product Name'); ?></th>
                  				<th><?php echo _('Changed Product'); ?></th>
                  				
                			</tr>
						</thead>
              			<tbody>
                			<?php if(sizeof($products) > 0): ?>
								<?php foreach($products as $product): ?>
								<tr id="tr_<?php echo $product['id']; ?>">
									<td>
										<span  class="pro_name"><a href="<?php echo base_url().'cp/cdashboard/products_addedit/product_id/'.$product['id'];?>" ><?php echo stripslashes($product['proname']); ?></a></span>
									</td>
									<td>
									<span  class="pro_name"><?php if(isset($product['changed_product'])){echo $product['changed_product']; }else{ echo _("No Product"); } ?></span>
									</td>
									
	                			</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<td>
										<?php echo _('No product.');?>
									</td>
								</tr>			
							<?php endif; ?>
						</tbody>	
            		</table>
            		
          		</div><!-- /table -->
        	</div><!-- /box -->
      	</div><!-- /content-container -->
	</div><!-- /content -->
	
	
	<div id="my_tb_holder">
	
	</div>
	
	
	<div id="rename_master" style="display: none">
		
	</div>
