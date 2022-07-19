<script type="text/javascript">
var mark_prod = "<?php echo _("Mark the product as semi-product");?>";
var move_to_semi = "<?php echo _("Move to Semi Product");?>";
var move_to_extra = "<?php echo _("Move to Extra Semi Product");?>";
var checked = "<?php echo _("checked");?>";
</script>
<?php
$sel_lang = get_lang($_COOKIE['locale']);
?>
<div class="boxed">
      			<h3 id="recipe"> <?php echo _('Recipe')?></h3>
      			<div style="padding: 0px; display: block;" class="inside">
        			<div class="table">
        				<table border="0">
			       			<tbody>
			       				
			       				<?php if ($this->company->ac_type_id == 2 || $this->company->ac_type_id  == 3 ) { ?>
			       				<tr id="recipe_weight_tr" <?php if(isset($product_information) && !empty($product_information) && $product_information[0]->direct_kcp == 1){ echo 'style="display: none"'; }?>>
					            	<td class="textlabel">
					                	<?php echo _('Weight for recipe in Kg')?><br/>
					                </td>
					                <td style="padding-right:100px">
					                	<input id="recipe_weight" name="recipe_weight" type="hidden" step=".1" value="<?php if(isset($product_information) && !empty($product_information)){ echo  $product_information[0]->recipe_weight; } ?>" min="0" class="text" style="width: 20%;" onchange="change_recipe_weight()" onblur="change_recipe_weight()">
					                	<strong><span id="recipe_weight_span"> <?php if(isset($product_information) && !empty($product_information)){ echo  $product_information[0]->recipe_weight; }else{ echo "0"; } ?></span></strong>
					                	<strong><?php echo _("Kg");?></strong>

					                	<?php if(isset($product_information) && !empty($product_information)){ ?>
					                		&nbsp;&nbsp;&nbsp;
					                		<img src="<?php echo base_url().'assets/cp/images/select_list_h.gif'; ?>" <?php if (isset($product_information) && $product_information[0]->parent_proid == 0){?> onclick="reset_wt()"<?php }?>>
					                	<?php } ?>

					                	&nbsp;&nbsp;&nbsp;
					                	<span><?=_('Important: only with hot dishes, the weight must be weighted when it\'s hot (not cold)'); ?></span>
					                </td>
					            </tr>
					            
			       			<?php if( $this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium'){?>
				                            <?php if(!empty($product_ingredients)){ ?>
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
							                    				ing_datas.push({id:'<?php echo $product_ingredient->ki_name; ?>',text:"<?php echo $product_ingredient->ki_name; ?>"});
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
							                    			var combine_id = "<?php echo $product_ingredient->prefix.'#'.$product_ingredient->ki_name.'#'.$product_ingredient->ki_id.'#'.$product_ingredient->kp_id.'#'.$product_ingredient->is_obs_ing.'#'.$key.'#'.$aller_type.'#'.$allergence.'#'.$sub_allergence.'#'.$new_allergence; ?>" ;
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
				            <tr id="recipe_weight_tr" <?php if(isset($product_information) && !empty($product_information) && $product_information[0]->direct_kcp == 1){ echo 'style="display: none"'; }?>>
				            	<td class="textlabel">
				                	<?php echo _('Weight for recipe in Kg')?><br/>
				                </td>
				                <td style="padding-right:100px">
				                	<input id="recipe_weight" name="recipe_weight" type="hidden" step=".1" value="<?php if(isset($product_information) && !empty($product_information)){ echo  $product_information[0]->recipe_weight; } ?>" min="0" class="text" style="width: 20%;" onchange="change_recipe_weight()" onblur="change_recipe_weight()">
				                	<strong><span id="recipe_weight_span"> <?php if(isset($product_information) && !empty($product_information)){ echo  $product_information[0]->recipe_weight; }else{ echo "0"; } ?></span></strong>
				                	<strong><?php echo _("Kg");?></strong>

				                	<?php if(isset($product_information) && !empty($product_information)){ ?>
				                		&nbsp;&nbsp;&nbsp;
				                		<img src="<?php echo base_url().'assets/cp/images/select_list_h.gif'; ?>" <?php if (isset($product_information) && $product_information[0]->parent_proid == 0){?> onclick="reset_wt()"<?php }?>>
				                	<?php } ?>

				                	&nbsp;&nbsp;&nbsp;
				                	<span><?=_('Important: only with hot dishes, the weight must be weighted when it\'s hot (not cold)'); ?></span>
				                </td>
				            </tr>
				            <?php if (!(!empty($product_information) && $product_information[0]->parent_proid != 0)){?>
							<tr id="recipe_contains_tr" <?php if(isset($product_information) && !empty($product_information) && $product_information[0]->direct_kcp == 1){ echo 'style="display: none"'; }?>>
				                <td class="textlabel">
				                	<?php //echo _('Contains')?> 
				                </td>
				                <td>
				                	<div id="fdd_tools">
			              				<br/>

					              		<?php if($fdd_credits > 0){?>

						              		<?php if(isset($product_information) && !empty($product_information)){?>
						              			<?php if (isset($check_prod_share) && !empty($check_prod_share)){?>
						              				<a href="<?php echo base_url().'cp/fooddesk/fdd_own_products_all/'.$product_information['0']->id.'?height=300&width=900&shared_prod_status=1'?>" title="<?php echo _('Add Recipe of PRODUCT ');  echo $product_information['0']->proname; ?>" class="thickbox"><?php echo _('FoodDESK/Own products')?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						              			<?php } else{?>
						              				<a href="<?php echo base_url().'cp/fooddesk/fdd_own_products_all/'.$product_information['0']->id.'?height=300&width=900'?>" title="<?php echo _('Add Recipe of PRODUCT ');  echo $product_information['0']->proname; ?>" class="thickbox"><?php echo _('FoodDESK/Own products')?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						              				<?php }?>
						              		<?php }else{?>
						              			<a href="<?php echo base_url().'cp/fooddesk/fdd_own_products_all?height=300&width=900'?>" title="<?php echo _('Add FoodDESK or Own products');?>" class="thickbox"><?php echo _('FoodDESK/Own products')?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						              		<?php }?>

					              		<?php }else{?>
					              			<a href="#TB_inline?height=300&width=500&inlineId=credit_require" title="<?php echo _('No credit left!');?>" class="thickbox"><?php echo _('FoodDESK/Own products')?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					              		<?php }?>

				              		</div>



				              		<div style="width: 100%; <?php if(isset($product_information)){if(!empty($product_information)){ if($product_information['0']->direct_kcp == 1 && ($product_information['0']->fdd_producer_id != 0 || $product_information['0']->fdd_supplier_id != 0)) { echo 'display:none;';  }}} ?>" >
			              			<table border="0" class="override" id="remove_con" >
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
	                                			<?php 
	                                				if($fdd_info['semi_product_id'] == 0){
	                                					if( $fdd_info[ 'approval_status' ] == 0 ){
	                                			?>
	                                					<tr id="ing_sub_row_<?php echo $fdd_info['fdd_pro_id']; ?>" class="ing_pro_name_row unapproved" rel="0">
	                                			<?php
	                                					}else{
	                                			?>
		                                			<tr id="ing_sub_row_<?php echo $fdd_info['fdd_pro_id']; ?>" class="ing_pro_name_row" rel="0">

		                                		<?php 	
		                                				}
		                                			}else{
		                                				if( $fdd_info[ 'approval_status' ] == 0 ){
		                                		?>
		                                				<tr id="ing_sub_row_<?php echo $fdd_info['fdd_pro_id']; ?>" class="unapproved ing_pro_name_row semi_pro" rel="<?php echo $fdd_info['semi_product_id']; ?>">
		                                		<?php 	}else{ ?>
		                                				<tr id="ing_sub_row_<?php echo $fdd_info['fdd_pro_id']; ?>" class="ing_pro_name_row semi_pro" rel="<?php echo $fdd_info['semi_product_id']; ?>">
		                                		<?php 	}
		                                			} ?>
		                                			<td width="7%" >
		                                				

		                                				<?php if(!empty($product_ingredients)){
		                                					$count_val=0;
		                                					?>
							                    			<?php foreach ($product_ingredients as $product_ingredient){?>
							                    				<?php if($product_ingredient->kp_id == $fdd_info['fdd_pro_id'] && $product_ingredient->ki_id == 0 && $product_ingredient->ki_name != '(' && $product_ingredient->ki_name != ')' ){?>
							                    					<?php $count_val++;?>
							                    					<?php $prefix = $product_ingredient->prefix;?>
							                    				<?php }?>
							                    			<?php }?>
						                    			<?php }?>

		                                				<?php if ($count_val){?>
		                                					<input type="text" style="width:100%" class="text pro_prefix" onkeyup="pro_prefix_change(this)" value="<?php echo $prefix; ?>" placeholder="<?php echo _('prefix');?>" >
		                                				<?php }?>
		                                			</td>
		                                			<td width="3%" ></td>
		                                			<td width="30%">
		                                				<?php if ($fdd_info['product_type'] == 1){
		                                					if( $fdd_info['approval_status'] == 1 ){?>
		                                						<input type="text" style="width:100%;" class="text product_name_text" value="<?php echo stripslashes($fdd_info['p_name'.$sel_lang.'']); ?>" disabled>
		                                						<?php } else {?>
		                                						<input type="text" style="width:100%;background-color:pink;" class="text product_name_text" value="<?php echo stripslashes($fdd_info['p_name'.$sel_lang.'']); ?>" disabled>
		                                					<?php }
		                                				}else{ ?>
		                                					<input type="text" style="width:100%;" class="text product_name_text" value="<?php echo stripslashes($fdd_info['p_name'.$sel_lang.'']); ?>" disabled>
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
		                                				<?php if ($fdd_info['product_type']){?>
		                                					<input type="hidden" class="ing_pro_name" value="<?php echo $fdd_info['p_name'.$sel_lang.''].'--'.$fdd_info['s_name'].'--EAN:'.$fdd_info['barcode'].'--PLU:'.$fdd_info['plu'].'--GS1';?>" >
		                                				<?php }else{?>
		                                					<input type="hidden" class="ing_pro_name" value="<?php echo $fdd_info['p_name'.$sel_lang.''].'--'.$fdd_info['s_name'].'--EAN:'.$fdd_info['barcode'].'--PLU:'.$fdd_info['plu'];?>" >
		                                				<?php }?>
		                                			</td>
		                                			<td width="4%" style="vertical-align: middle;">
														<img src="<?php echo base_url();?>assets/cp/images/Delete.gif" style="width: 20px;" onclick="remove_this_fdd_pro(<?php echo $fdd_info['fdd_pro_id'];?>)" />
													</td>
														<?php if ($fdd_info['product_type'] == 1){?>
														<td style="color: blue;"><?php echo "GS1";?>
															<input type="hidden" name="pro_fixed" class="pro_fixed" value="1" />
														</td>
														<td width="5%">
															<?php if( in_array( $fdd_info['fdd_pro_id'], $fdd_pro_fav ) ){?>
																	<img src="<?php echo base_url(); ?>/assets/images/greenstar.png" data-status="marked" class="mark_fav prod_status"  data-prod_id="<?php echo $fdd_info[ 'fdd_pro_id' ]; ?>" >
																<?php
																}else{ ?>
																	<img src="<?php echo base_url(); ?>/assets/images/star.jpg" data-status="unmarked" class="mark_fav prod_status"  data-prod_id="<?php echo $fdd_info[ 'fdd_pro_id' ]; ?>" >
																<?php } ?>
														</td>
														<td width="32%" style="vertical-align: middle;padding-left:5px">
			                                				<?php
			                                				$all_str = '';
			                                				 if(!empty($product_allergences)){
			                                					foreach ($product_allergences as $pa){
			                                						if($pa->kp_id == $fdd_info['fdd_pro_id']){
																		$all_str = $all_str.$pa->ka_name.', ';
																	}
			                                					}
			                                				}?>
			                                				<?php $all_str = substr($all_str,0,-2); ?>
			                                				<?php if( $fdd_info['approval_status'] != 0 ){ ?>
			                                				<?php if($fdd_info['gs1_response'] != '' && $fdd_info['gs1_response'] != NULL){?>
			                                					<?php $pdf_year = substr($fdd_info['pdf_date'],0,4) ?>
			                                					<span style="color: #777">(
			                                					<?php if($pdf_year != '0000' && $pdf_year != NULL){?>
			                                						<?php echo $pdf_year; ?>
			                                					<?php }?>
			                                					</span>
			                                					<span style="color: #777">)</span>
			                                				<?php } }?>
			                                					</br>
			                                				<?php if($all_str != ''){ $all_str = implode(', ',array_unique(explode(', ', $all_str)));?>
			                                						<i><span style="color: #777"><?php echo '('.$all_str.')';?></span> </i>
			                                				<?php }?>
														</td>
													<?php }else{?>
														<td width="4%" style="vertical-align: middle;">
														<input type="hidden" name="pro_fixed" class="pro_fixed" value="<?php echo $fdd_info['fixed']; ?>">
														<?php if( $fdd_info['approval_status'] != 0 ){ ?>
															<?php if($fdd_info['data_sheet'] != '' && $fdd_info['data_sheet'] != NULL){?>
																<a target="_blank" href="<?php echo $this->config->item('fdd_url').'assets/cp/uploads/'.$fdd_info['data_sheet']; ?>" >
																	<img src="<?php echo base_url();?>assets/images/pdf2.jpeg" style="width: 20px;" />
																</a>
															<?php }else{?>
																<img src="<?php echo base_url();?>assets/images/pdf1.jpeg" style="width: 20px;" />
															<?php }?>
														<?php } ?>
			                                			</td>
			                                			<td width="5%">
															<?php if( in_array( $fdd_info['fdd_pro_id'], $fdd_pro_fav ) ){?>
																	<img src="<?php echo base_url(); ?>/assets/images/greenstar.png" data-status="marked" class="mark_fav prod_status"  data-prod_id="<?php echo $fdd_info[ 'fdd_pro_id' ]; ?>" >
																<?php 
																}else{ ?>
																	<img src="<?php echo base_url(); ?>/assets/images/star.jpg" data-status="unmarked" class="mark_fav prod_status"  data-prod_id="<?php echo $fdd_info[ 'fdd_pro_id' ]; ?>" >
																<?php } ?>
														</td>
			                                			<td width="32%" style="vertical-align: middle;padding-left:5px">

			                                				<?php
			                                				$all_str = '';
			                                				 if(!empty($product_allergences)){
			                                					foreach ($product_allergences as $pa){
			                                						if($pa->kp_id == $fdd_info['fdd_pro_id']){
																		$all_str = $all_str.$pa->ka_name.', ';
																	}
			                                					}
			                                				}?>
			                                				<?php $all_str = substr($all_str,0,-2); ?>
			                                				<?php if( $fdd_info['approval_status'] != 0 ){ ?>
			                                				<?php if($fdd_info['data_sheet'] != '' && $fdd_info['data_sheet'] != NULL){?>
			                                					<?php $pdf_year = substr($fdd_info['pdf_date'],0,4) ?>
			                                					<span style="color: #777">(
			                                					<?php if($pdf_year != '0000' && $pdf_year != NULL){?>
			                                						<?php echo $pdf_year.' - '; ?>
			                                					<?php }?>
			                                					</span>
			                                					<a class="tiny_txt" title="<?php echo _("upload new product-sheet"); ?>" href='javascript:;' onclick="upload_sheet(<?php echo $fdd_info['fdd_pro_id'];?>)"><?php echo _('upload newer');?></a>
			                                					<span style="color: #777">)</span>
			                                					<br/>
			                                				<?php } }else{ ?>
			                                					<span style="color: #777">(<?php echo _('Is being reviewed'); ?>)
			                                					</span>
			                                					</br>
			                                				<?php } if($all_str != ''){ $all_str = implode(', ',array_unique(explode(', ', $all_str)));?>
			                                						<i><span style="color: #777"><?php echo '('.$all_str.')';?></span> <a href="javascript:;" class="tiny_txt" onclick="assign_to_recheck(<?php echo $fdd_info['fdd_pro_id'];?>)"> <?php echo _('Wrong');?></a></i>
			                                				<?php }?>
														</td>
													<?php } ?>
		                                		</tr>
		                    					<?php }?>
	                    					<?php }?>
	                                    </tbody>
	                                </table>

			              		</div>

			              		<div style="width: 100%; <?php if(isset($product_information)){if(!empty($product_information)){ if($product_information['0']->direct_kcp == 1 && ($product_information['0']->fdd_producer_id != 0 || $product_information['0']->fdd_supplier_id != 0)) { echo 'display:none;';  }}} ?>" >
			              			<table border="0" class="override" id="remove_new_pro">
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
	                                		<?php if($own_pro_info['semi_product_id'] == 0){?>
		                                		<tr id="ing_sub_row_<?php echo $own_pro_info['fdd_pro_id']; ?>" class="ing_pro_name_row" rel="0">
		                                	<?php }else{?>
		                                		<tr id="ing_sub_row_<?php echo $own_pro_info['fdd_pro_id']; ?>" class="ing_pro_name_row semi_pro" rel="<?php echo $own_pro_info['semi_product_id'];?>" >
		                                	<?php }?>
		                                			<td width="7%" >
		                                				<?php if(!empty($product_ingredients)){?>
							                    			<?php foreach ($product_ingredients as $product_ingredient){?>
							                    				<?php if($product_ingredient->kp_id == $own_pro_info['fdd_pro_id'] && $product_ingredient->ki_id == 0 && $product_ingredient->ki_name != '(' && $product_ingredient->ki_name != ')' ){?>
							                    						<input type="text" style="width:100%" class="text pro_prefix" onkeyup="pro_prefix_change(this)" value="<?php echo $product_ingredient->prefix; ?>" placeholder="<?php echo _('prefix');?>" >
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
		                                			<td width="4%" style="vertical-align: middle;">
														<img src="<?php echo base_url();?>assets/cp/images/Delete.gif" style="width: 20px;" onclick="remove_this_fdd_pro(<?php echo $own_pro_info['fdd_pro_id'];?>)" />
														<input type="hidden" name="pro_fixed" class="pro_fixed" value="1">
													</td>
													<td width="4%" style="vertical-align: middle;">

		                                			</td>
		                                			<td width="37%" style="vertical-align: middle;">

		                                			</td>
		                                		</tr>
		                    					<?php }?>
	                    					<?php }?>

	                                    	</tbody>
	                                    	<tfoot>
	                                    	</tfoot>
	                                	</table>
			              			</div>
			              			<div id="last_td"></div>
			              			<?php if(isset($product_information) && !empty($product_information) && $product_information[0]->direct_kcp == 0 && isset($check_prod_share) && empty($check_prod_share)){?>
				              			<p style="padding-top: 25px;"><b><?php echo _('Mark this product as semi-product')?></b>&nbsp;&nbsp;&nbsp;<input type="checkbox" name="is_custom_semi" id="is_custom_semi" <?php if(isset($product_information)){if(!empty($product_information)){if($product_information['0']->semi_product){echo 'checked="checked"';}}}?> value="1"></p>
				              			<h1 id="is_semi_alert" style="color:red;"></h1>
				              			<p class="move_to_alert"><a onclick="return move_to('<?php echo $product_information[0]->id; ?>',1)" href="javascript:;"><b><?php echo _('Move to Semi Product');?></b></a></p>
				              			<p class="move_to_alert"><a onclick="return move_to('<?php echo $product_information[0]->id; ?>',2)" href="javascript:;"><b><?php echo _('Move to Extra Semi Product');?></b></a></p>
									<?php }?>
				            	</td>

				            </tr>

				             <?php } else if (!empty($product_information) && $product_information[0]->parent_proid != 0) {?>
				             	<tr id="recipe_contains_tr">
									<td class="textlabel"> </td>
									<td><b><?php echo _( "Product shared by" );?> : <i><?php if( isset( $shared_by ) ){ echo $shared_by[ 'company_name' ]; }?></i></b></td>
								</tr>
				             <?php }?>
				               <tr id="recipe_method_tr" <?php if(isset($product_information) && !empty($product_information) && $product_information[0]->direct_kcp == 1){ echo 'style="display: none"'; }?>>
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
										<input class="text small" style="width: 600px; height: 100px;" id="ingredients" name="ingredients" value="<?php if($product_information && $product_information['0']->ingredients){ echo $product_information['0']->ingredients ; }?>" />
										<input type="hidden" id="ingredientscopy" value="<?php if(isset($product_ingredients_dist)){ echo $product_ingredients_dist ; }?>">
										<a class="copy_cboard" href="javascript:;" data-type="ingredients"><?php echo _('copy');?></a>
									<?php if($this->session->userdata('login_via') == 'mcp'){?>
										<?php if(isset($product_information)){if(!empty($product_information)){ if($product_information['0']->direct_kcp == 0 ){?>
									<a href="#TB_inline?height=300&width=500&inlineId=prod_ingre_list" title="<?php echo _('Products with their ingredients');?>" class="thickbox"><?php echo _('Ingredients');?></a>
									<?php }}}}?>
			              			<?php }else{ ?>
			              				<textarea class="text small" rows="8" style="width: 425px;" id="ingredient" name="ingredients" <?php if($this->company->k_assoc){?>placeholder="<?php echo _('Please add ingredients separated with comma(,).');?>"<?php }?> ><?php if($product_information && $product_information['0']->ingredients){ echo $product_information['0']->ingredients ; }?></textarea>
			              			<?php } ?><br/><br/>
										<a href="#TB_inline?height=300&width=500&inlineId=remark_mail" title="<?php echo _('Remark by mail');?>" class="thickbox"><?php echo _('Remark by mail');?></a>
			              			</td>
			              		</tr>

			              		<tr id="all_container" >
			              			<td class="textlabel">
			              				<?php echo _("Allergence");?> :
					              	</td>
					              	<td>
					              	<?php if( $this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium' ){?>
					              		<input class="text small" style="width: 600px; height: 100px;" id="allergence" name="allergence" value="<?php if($product_information && $product_information['0']->allergence){ echo $product_information['0']->allergence ; }?>" />
					              		<input type="hidden" id="rp_count_allg" value="0" />
					              		<input type="hidden" id="lp_count_allg" value="0" />
					              		<input type="hidden" id="allergencecopy" value="<?php if(isset($product_allergences_dist)){ echo $product_allergences_dist ; }?>">
					              		<a class="copy_cboard" href="javascript:;" data-type="allergence"><?php echo _('copy');?></a>

					              		<div style="width: 50%;">
					              			<table border="0" class="override" >
			                                	<tbody id="kp_allergence" class="" style="display: none">
			                    					<?php if(!empty($product_allergences)){?>
			                    					<tr><td colspan="3">&nbsp;</td></tr>
			                    					<?php $conuter = 0;?>
			                    					<?php foreach ($product_allergences as $product_allergence){?>
					                    				<?php if($product_allergence->kp_id){?>
					                    					<?php if($product_allergence->ka_id){?>
					                    					<tr id="allg_<?php echo $product_allergence->kp_id;?>_<?php echo $product_allergence->ka_id;?>">
					                    					<?php }?>
																<td width="70%">
																	<p class="draggabled">
																		<input type="text" name="kp_a_names_prefix[]" class="text short prefix" value="<?php echo $product_allergence->prefix;?>" />
																		<input type="text" name="kp_allg_names[]" class="text medium name" value="<?php echo $product_allergence->ka_name;?>" style="width:70%;<?php if(!$product_allergence->ka_id){?>font-weight:bold;<?php }?>" disabled="disabled"  />
																		<input type="hidden" class="kp_allg_ids" name="kp_allg_ids[]" value="<?php echo $product_allergence->ka_id; ?>" />
																		<input type="hidden" class="kp_allg_pid" name="kp_allg_pid[]" value="<?php echo $product_allergence->kp_id; ?>" />
																	</p>
																</td>
																<td width="10%">
																</td>
																<td width="5%" style="text-align:right">
																	
																	<?php if($product_allergence->ka_id){?>
																		<img width="18" border="0" class="handle" src="<?php echo base_url();?>assets/cp/images/upload.png" style="cursor: pointer;" title="<?php echo _("Drag me");?>" onclick="drop_me('allg_<?php echo $product_allergence->kp_id;?>_<?php echo $product_allergence->ka_id;?>','allergence');" />
																	<?php } ?>
																</td>
																<td width="10%">
																</td>
																<td width="5%" style="text-align:right;">
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
						                    						allg_datas.push({id:'<?php echo $product_allergence->ka_name; ?>',text:'<?php echo stripslashes($product_allergence->ka_name); ?>'});
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
			              		<input class="text small" style="width: 600px; height: 100px;" id="traces_of" name="traces_of" value="<?php if($product_information && $product_information['0']->traces_of){ echo $product_information['0']->traces_of ; }?>" />
			              		<input type="hidden" id="rp_count_t" value="0" />
			              		<input type="hidden" id="lp_count_t" value="0" />
			              		<input type="hidden" id="traces_ofcopy" value="<?php if(isset($product_traces_dist)){ echo $product_traces_dist ; }?>">
			              		<a class="copy_cboard" href="javascript:;" data-type="traces_of"><?php echo _('copy');?></a>
			              		
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
															
														</td>
														<td width="5%" style="text-align:right">
															
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
			                    					<?php if(!$product_trace->kp_id){
				                    					if($product_trace->kt_name == '('){ ?>
				                    						$('#lp_count_t').val(parseInt($('#lp_count_t').val()) + 1);
				                    						traces_datas.push({id:'lp#'+parseInt($('#lp_count_t').val()),text:'. ( .'});
				                    					<?php }else if($product_trace->kt_name == ')'){ ?>
				                    						$('#rp_count_t').val(parseInt($('#rp_count_t').val()) + 1);
				                    						traces_datas.push({id:'rp#'+parseInt($('#rp_count_t').val()),text:'. ) .'});
				                    					<?php }else{ ?>
				                    						traces_datas.push({id:"<?php echo $product_trace->kt_name; ?>",text:"<?php echo $product_trace->kt_name; ?>"});
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
			              				<td><strong><?php echo _("Nutritional Information");?></strong>&nbsp;&nbsp;<a class="copy_cboard" href="javascript:;" data-type="nutri_values"><?php echo _('copy');?></a><input type="hidden" id="nutri_valuescopy" value="<?php if(isset($nutri_values_dist)){ echo $nutri_values_dist ; }?>"></td>
			              				<td><strong><?php echo "per 100g";?></strong></td>
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
			              				<td>
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
				          	<?php }
				          
				     }else{ ?>
				      	  <tr id="notice"><td id="notice_msg"  colspan='2'><?php echo _("The section has been added in the new Controlepanel(2.0)" ); ?><?php echo "<br>"; ?><?php echo _("Please proceed with new Controlepanel(2.0)")?><?php echo "<br>"; ?><a target="_blank" style="color: #8c8c00;text-decoration: none;" href="<?php echo rtrim($this->config->item('new_obs_url'),'/') ?>"><?php echo rtrim($this->config->item('new_obs_url'),'/') ?></a></td></tr>
				      <?php } ?>
			        		</tbody>
			        	</table>
        			</div>
        			<?php if($product_information){?>
        			<?php if ($this->company->ac_type_id == 2 || $this->company->ac_type_id  == 3 ) { ?>
					 <div class="sub_div">
			    		<div class="sub__div" colspan="2">
			        		<input type="button" value="<?php echo _("Update");?>" class="submit" id="recipe_update" name="recipe_update" <?php if (isset($product_information) && !empty($product_information) && $product_information[0]->parent_proid != 0){?>disabled<?php }?>>
			        		<input type="hidden" value="add_edit" id="recipe_act" name="recipe_act">
			    			<input type="hidden" value="update" id="recipe_add_update" name="recipe_add_update">
			    			<input type="button" value="<?php echo _('Save & next')?>" class="submit" id="recipe_savenext" name="recipe_savenext" <?php if (isset($product_information) && !empty($product_information) && $product_information[0]->parent_proid != 0){?>disabled<?php }?>>
						</div>
					</div> 
					<?php } ?>
					<?php }else{?>
					<?php if ($this->company->ac_type_id == 2 || $this->company->ac_type_id  == 3 ) { ?>
					<div class="sub_div">
			    		<div class="sub__div" colspan="2">
			        		<input type="button" value="<?php echo _('Send')?>" class="submit" id="recipe_add" name="recipe_add" <?php if (isset($product_information) && !empty($product_information) && $product_information[0]->parent_proid != 0){?>disabled<?php }?>>
			      			<input type="hidden" value="add" id="recipe_add_update" name="recipe_add_update">
			      			<input type="button" value="<?php echo _('Save & next')?>" class="submit" id="recipe_savenext" name="recipe_savenext" <?php if (isset($product_information) && !empty($product_information) && $product_information[0]->parent_proid != 0){?>disabled<?php }?>>
			   			</div>
					</div>
					<?php } ?>
					<?php }?>
        		</div>
        	</div>