<html>
	<head>
	</head>
	
	<body>
		<div>
			<table>
			<tr><td><input type="hidden" id="obs_pro_id" value="<?php echo $obs_pro_id; ?>" /></td></tr>
				<tr>
					<td id="fdd_products" colspan="3">
						<input type="text" name="product_name" placeholder="<?php echo _("Search by product's name,producer's name,EAN or Article Number"); ?>" class="text prod_name ui-autocomplete-input ui-autocomplete-loading" style="width: 100%" id="search_box" onkeyup="show_suggestion(event,'<?php echo get_lang( $_COOKIE['locale'] ) ?>');" autocomplete="off" />
					</td>
				</tr>
				
				<tr>
					<th><?php echo _('Product name'); ?></th>
					<th><?php echo _('Producer name'); ?></th>
					<th><?php echo _('Make Same'); ?></th>
				</tr>
		
				<?php 
				$count_sugg = 1;
				if(!empty($searched)){
					foreach ($searched as $search){ 
				?>
				<tr>
					<td><?php echo stripslashes($search['p_name_dch']); ?></td>
					<td><?php echo stripslashes($search['s_name']); ?></td>
					<td>
						<a href="javascript:;" onclick="make_it_same(<?php echo $obs_pro_id; ?>,<?php echo $search['p_id']; ?>)"><?php echo _('Assign'); ?></a>
					</td>
				</tr>
				<?php 
						$count_sugg++;
						if($count_sugg > 10){
							break;
						}
					}
				}else{ ?>
				<tr id="no_suggest"><td colspan="3"><?php echo _('No suggestion for this product.'); ?></td></tr>
				<?php } ?>
		
			</table>
		</div>
	</body>
</html>