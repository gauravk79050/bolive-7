<style>
.inp_fld {
    background: #fcfcfc none repeat scroll 0 0;
    border: 1px solid #cccccc;
    color: #333333;
    display: inline;
    padding: 5px;
}
</style>
<script type="text/javascript">
	window.onload = function(){ document.getElementById("loading").style.display = "none"; }
</script>
<div id="main">
	<div id="main-header">
    	<h2><?php echo _('Products'); ?></h2>
      	<span class="breadcrumb"><a href="<?php echo base_url()?>cp/products"><?php echo _('Home')?></a> &raquo; <?php echo _('Products')?></span>
	</div>
	<!-- <div id="loading">
		<img src="<?php echo base_url(); ?>assets/cp/images/ajax-loading.gif"/><strong><?php echo _('Loading')?>...</strong>
	</div> -->
	<div id="content">
    	<div id="content-container">
        	<div class="box">
        		<?php $ingredient = (isset($ingre_name))?$ingre_name:'';?>
          		<h3><?php echo _('Products that contains').' '.$ingredient?></h3>
          		<div class="table">
          		<?php if(!empty($recipe_products)){?>
            		<table cellspacing="0">				
						<thead>                			
                			<tr>                				
                  				<th><?php echo _('Article No.'); ?></th>
                  				<th><?php echo _('Product Name'); ?></th>
                  				<th><?php echo _('Description'); ?></th>
                  				<th><?php echo _('Action'); ?></th>
                  			</tr>
						</thead>
              			<tbody>              			
              				<?php foreach ($recipe_products as $product){?>
              				<tr>              					
              					<td><input class="inp_fld" type="text" size="5" value="<?php echo $product['pro_art_num']?>" readonly></td>
              					<td><input type="text" value="<?php echo stripslashes($product['proname'])?>" size="20" class="inp_fld" /></td>
              					<td><input class="inp_fld" type="text" size="15" value="<?php echo stripslashes($product['prodescription'])?>" readonly></td>								
								<td>
									<a href="<?php echo base_url().'cp/products/products_addedit/product_id/'.$product['id']?>" class="edit"> <img width="16" height="16" border="0" alt="Edit" src="<?php echo base_url(); ?>assets/cp/images/edit.gif"></a>
								</td>
							</tr>
              			<?php }?>              			
						</tbody>
            		</table>
            	<?php }?>
            	<?php if(!empty($semi_products)){?>
            		<table cellspacing="0">				
						<thead>                			
                			<tr>
                				<th><?php echo _('Product Name'); ?></th>
                  				<th><?php echo _('Description'); ?></th>
                  				<th><?php echo _('Action'); ?></th>
                  			</tr>
						</thead>
              			<tbody>              			
              			<?php foreach ($semi_products as $product){?>
              				<tr>
              					<td><input type="text" value="<?php echo stripslashes($product['proname'])?>" size="20" class="inp_fld" /></td>
              					<td><input class="inp_fld" type="text" size="15" value="<?php echo stripslashes($product['prodescription'])?>" readonly></td>
								<td>
									<a href="<?php echo base_url().'cp/products/semi_product_addedit/product_id/'.$product['id']?>" class="edit"> <img width="16" height="16" border="0" alt="Edit" src="<?php echo base_url(); ?>assets/cp/images/edit.gif"></a>
								</td>
							</tr>
              			<?php }?>              			
						</tbody>
            		</table>
            	<?php }?>
          		</div><!-- /table -->
        	</div><!-- /box -->
      	</div><!-- /content-container -->
	</div><!-- /content -->