<?php
	$sel_lang = get_lang($_COOKIE['locale']);
	$new_ing_name = array();
?>
<html>
	<head>
		<style type="text/css">
		p, td, div{
			font-size:11px;
		}

		p, td, div, h2 {
		    font-family: Calibri;
		 }
		</style>
		

	</head>

	<body>
		<?php if($sheet_banner[0]->sheet_banner != ''){ ?>
		<div id="container">
			<img alt="shop-banner" src="<?php echo base_url()?>assets/mcp/images/sheet_banner/<?php echo $sheet_banner[0]->sheet_banner;?>" style="width:100%;height:auto;">
		</div>
		<?php }?>
		<div id="container" style="margin: 10px; padding: 0px 20px;">
			<h2><u><?php echo stripslashes($product_information[0]->proname); ?></u><?php if($marked == 1){ echo "**";};?></h2>
		</div>
		<div id="container" style="margin:5px;padding:5px;">
			<div id="left_div" style="width:30%;margin:10px;padding:10px;float:left">

				<?php if($product_information[0]->image != NULL || $product_information[0]->image != ''){?>
					<img src="<?php echo base_url().'assets/cp/images/product/'.$product_information[0]->image; ?>" style="width:100%">
				<?php }else 
				if( $sheet_banner[0]->comp_default_image != NULL || $sheet_banner[0]->comp_default_image != ''){?>
					<img src="<?php echo base_url().'assets/cp/images/infodesk_default_image/'. $sheet_banner[0]->comp_default_image;?>" style="width:100%">
				<?php } else {?>
					<img src="<?php echo base_url().'assets/cp/images/no_image.jpg'; ?>" style="width:100%">
				<?php } ?>

					<p><b><?php echo _('PRODUCER'); ?></b></p>
					<?php if($product_information[0]->parent_proid == 0 || $product_information[0]->direct_kcp != 1){?>
						<?php if(isset($comp_det)){?>
						<p><?php echo $comp_det[0]->company_name;?><br/>
							<?php echo $comp_det[0]->address;?><br/>
							<?php echo $comp_det[0]->zipcode.' '.$comp_det[0]->city;?><br/>
							<?php echo $comp_det[0]->phone;?></p>
						<?php }else{?>
						<p><?php echo $this->company->company_name;?><br/>
							<?php echo $this->company->address;?><br/>
							<?php echo $this->company->zipcode.' '.$this->company->city;?><br/>
							<?php echo $this->company->phone;?></p>
						<?php }?>
					<?php }else{?>
						<p><?php echo $comp_det[0]->company_name;?><br/>
						<?php echo $comp_det[0]->address;?><br/>
						<?php echo $comp_det[0]->zipcode.' '.$comp_det[0]->city;?><br/>
						<?php echo $comp_det[0]->phone;?></p>
					<?php }?>
			</div>
			<div id="right_div" style="width:60%;margin:5px;padding:5px;float:left" >
				<p><b><?php echo _('DESCRIPTION'); ?></b></p>
				<p><?php echo ($product_information[0]->prodescription != '0')?stripslashes($product_information[0]->prodescription):''; ?></p>

				<?php if(!empty($contains)){?>
					<?php $fixed_pro = '';
					 foreach ($contains as $contain){
						$fixed_pro .= stripslashes($contain['proname']).',';
					}

					$fixed_pro = substr($fixed_pro, 0, -1);
					?>
					<br/>
					<p><b><?php echo _('WITH RESERVATION'); ?></b></p>
					<p><span style="font-size:9px"><?php echo _('We dont have recieved the productsheet for').' '.$fixed_pro.' '._('yet. This means that the ingredients/allergens/nutrition values are most probably not correct.');?></span></p>

				<?php }elseif ( $approval_stat == 0 ){ ?>
						<br>
						<p><b><?php echo _('WITH RESERVATION'); ?></b></p>
						<p><?php echo _('We do not have one or more product sheets from manufacturers / suppliers at our disposal, which means that the values ​​below are not entirely correct. We can not be held liable for any direct or indirect damage. However, we will update this information as soon as the product sheets are available.'); ?></p>
				<?php } ?>
				<br/>
				<p><b><?php echo _('INGREDIENTS'); ?></b></p>
				<?php
				$single_ing = 0;
				if(!empty($fixed)){
					$ing = '';
					$count = 0;
					foreach ($product_ingredients as $key => $ingredients){
						if( ( $ingredients->ki_id != 0 ) && ( $product_ingredients[$key-1]->ki_id == 0 ) && ( $product_ingredients[$key-1]->ki_name == '(' ) && ( $product_ingredients[$key+1]->ki_id == 0 ) && ( $product_ingredients[$key+1]->ki_name == ')' ) ){
							$len = strlen(mb_strtoupper($product_ingredients[$key-2]->ki_name,'UTF-8'));
							$to_remove = $len+2;
							$ing = substr($ing, 0 , -$to_remove);
							$ing .= '';
							$ing .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words);
							// $ing = rtrim($ing, ' ');
							// $ing .= ', ';
							$single_ing = 1;
						}
						else {
							if( $ingredients->ki_id == 0 ){
								if( $ingredients->ki_name == '(' ){
									if( ( $product_ingredients[$key+1]->ki_id == 0 ) && ( $product_ingredients[$key+1]->ki_name == ')' ) ){
										$ing .= ',';
									}
									else{
										$ing .= ' (';
									}
								}
								elseif( $ingredients->ki_name == ')' ){
									$ing  = rtrim($ing, ' ');
									$ing  = rtrim($ing, ',');
									if( (( $product_ingredients[$key-2]->ki_id == 0 ) && ( $product_ingredients[$key-2]->ki_name == '(' )) || ( ( $product_ingredients[$key-1]->ki_id == 0 ) && ( $product_ingredients[$key-1]->ki_name == '(' ) ) ){

										$ing .= ', ';
									}
									else{
										$ing .= '), ';
									}
									array_push( $new_ing_name, $ing );
								}
								else{
									$ing .= $ingredients->ki_name;
								}
							}

							if($ingredients->ki_id != 0){
								$count++;
								if($ingredients->prefix == ''){
									$flag = true;
									if($ingredients->ki_name == '('){
										$ing  = rtrim($ing, ' ');
										$ing  = rtrim($ing, ',');
										$ing .= ' (';
									}
									elseif( $ingredients->ki_name == ')' ){
										$ing  = rtrim($ing, ' ');
										$ing  = rtrim($ing, ',');
										$ing .= '), ';
									}
									elseif($ingredients->ki_name == ':'){
										$ing  = rtrim($ing, ' ');
										$ing  = rtrim($ing, ',');
										$ing .= ': ';
									}else{
										$ing .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words);
										// $ing = rtrim($ing, ' ');
										// $ing .= ', ';
									}
								}
							}
						}
					}

					$ing = rtrim($ing," ,");
				}
				else{
          			$aller_type 	= 'aller_type'.$sel_lang ;
				  	$allergence 	= 'allergence'.$sel_lang ;
				  	$sub_allergence = 'sub_allergence'.$sel_lang ;
					$ing = '';
					$count = 0;
					foreach ($product_ingredients as $key => $ingredients){
						if( ( $ingredients->ki_id != 0 ) && ( $product_ingredients[$key-1]->ki_id == 0 ) && ( $product_ingredients[$key-1]->ki_name == '(' ) && ( $product_ingredients[$key+1]->ki_id == 0 ) && ( $product_ingredients[$key+1]->ki_name == ')' ) ){
							$len = strlen(mb_strtoupper($product_ingredients[$key-2]->ki_name,'UTF-8'));
							$to_remove = $len+2;
							$ing = substr($ing, 0 , -$to_remove);
							$ing .= '';
							if( empty($fixed) ){
								$ing .= get_the_allergence($ingredients->ki_name,$ingredients->$aller_type,$ingredients->$allergence,$ingredients->$sub_allergence);
							}else{
								$ing .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words);
							}
							$single_ing = 1;
						}
						else{
							if( $ingredients->ki_id == 0 ){
								if( $ingredients->ki_name == '(' ){
									if( ( $product_ingredients[$key+1]->ki_id == 0 ) && ( $product_ingredients[$key+1]->ki_name == ')' ) ){
										$ing .= ',';
									}
									else{
										$ing .= ' (';
									}
								}
								elseif( $ingredients->ki_name == ')' ){
									$ing  = rtrim($ing, ' ');
									$ing  = rtrim($ing, ',');
									if( (( $product_ingredients[$key-2]->ki_id == 0 ) && ( $product_ingredients[$key-2]->ki_name == '(' )) || ( ( $product_ingredients[$key-1]->ki_id == 0 ) && ( $product_ingredients[$key-1]->ki_name == '(' ) ) ){
										$ing .= ', ';
									}
									else{
										$ing .= '),';
									}
									array_push( $new_ing_name, $ing );
								}
								else{
									$ing .= $ingredients->ki_name;
								}
							}

							if($ingredients->ki_id != 0){
								$count++;
								if($ingredients->prefix == ''){
									$flag = true;
									if($ingredients->ki_name == '('){
										$ing  = rtrim($ing, ' ');
										$ing  = rtrim($ing, ',');
										$ing .= ' (';
									}
									elseif( $ingredients->ki_name == ')' ){
										$ing  = rtrim($ing, ' ');
										$ing  = rtrim($ing, ',');
										$ing .= '), ';
									}
									elseif($ingredients->ki_name == ':'){
										$ing  = rtrim($ing, ' ');
										$ing  = rtrim($ing, ',');
										$ing .= ': ';
									}else{
										if( empty($fixed) ){
											$ing .= get_the_allergence($ingredients->ki_name,$ingredients->$aller_type,$ingredients->$allergence,$ingredients->$sub_allergence);
										}else{
											$ing .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words);
										}
									}
								}
							}
						}
					}

					if( $count == 1 ){
						$ing = rtrim($ing, ' ');
						$ing = rtrim($ing, ',');
					}
					elseif( $count == 0 ){
						$ing = str_replace(' (),', '', $ing);
						$ing = rtrim($ing, ' ');
						$ing = rtrim($ing, ',');
					}
					else{
						$ing = rtrim($ing, ' ');
						$ing = rtrim($ing, ',');
					}
				}
				?>
				<p>
					<?php
						if( sizeof( $new_ing_name ) == 1 ) {
							if( strpos( $new_ing_name[0], ')' ) !== false ) {
								$str_pos = stripos( $new_ing_name[0], '(' );
								if( $single_ing == 1 ) {
									$new_ing_name[0] = rtrim( $new_ing_name[0], ", " );
								} else {
									$new_ing_name[0] = substr( $new_ing_name[0], $str_pos + 1, strlen( $new_ing_name[0] ) );
									$new_ing_name[0] = rtrim($new_ing_name[0], ' ');
									$new_ing_name[0] = substr( $new_ing_name[0], 0, (strlen( $new_ing_name[0] )-2) ); 
								}
								echo $new_ing_name[0];
							} else {
								$ing = rtrim($ing, ' ');
								$ing = rtrim($ing, ',');
								echo $ing;
							}
						} else {
							$ing = rtrim($ing, ' ');
							$ing = rtrim($ing, ',');
							echo $ing;
						}
					?>
				</p>
				<br/>
				<p><b><?php echo _('ALLERGEN'); ?></b></p>
				<?php $all = '';
				if(!empty($product_allergences)){

					foreach ($product_allergences as $key=>$allergence){

						$all .= $allergence->ka_name;

						if(($allergence->ka_id == 1) || ($allergence->ka_id == 8)){
							$a1 = '';
							if(!empty($product_sub_allergences)){
								$a1 .= ' (';
								foreach ($product_sub_allergences as $sub_allergence){
									if($sub_allergence->parent_ka_id == $allergence->ka_id){
										$a1 .=  $sub_allergence->sub_ka_name.', ';
									}
								}
								$a1 = rtrim($a1,', ');
								$a1 .= ')';
								$a1 = str_replace('()', '', $a1);
							}
							$all .= $a1;
						}

						$all .=  ', ';
					}
					$all = substr($all, 0, -2);
				}

				?>
				<p><?php echo $all; ?></p>

				<?php if(isset($nutri_values) && !empty($nutri_values)){?>
				<?php $recipe_wt = $product_information[0]->recipe_weight;
	 				if($recipe_wt != 0){
	 					$recipe_wt = $recipe_wt*1000;
	 				}else{
	 					$recipe_wt = 0;
	 				}?>
				<br/>

				<table style="width: 100%;">
					<tr>
						<td><b><?php echo _('NUTRITION AVERAGE');?></b></td>
						<td><b><?php echo 'per 100 g'; ?></b></td>
						<td><b><?php echo 'per '.$recipe_wt.' g'; ?></b></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td><b><?php echo _('Energy');?></b></td>
						<td><b><?php echo defined_money_format($nutri_values['e_val_1'],0).' kcal/'; ?><?php echo defined_money_format($nutri_values['e_val_2'],0).' kJ'; ?></b></td>
						<td><b><?php echo defined_money_format($nutri_values['e_val_1']/100*$recipe_wt,0).' kcal/'; ?><?php echo defined_money_format($nutri_values['e_val_2']/100*$recipe_wt,0).' kJ'; ?></b></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>

					<tr>
						<td><?php echo _('Fats');?></td>
						<td><?php echo defined_money_format($nutri_values['fats'],1).' g'; ?></td>
						<td><?php echo defined_money_format($nutri_values['fats']/100*$recipe_wt,1).' g'; ?></td>
					</tr>
					<tr>
						<td>&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;<?php echo _('Of which saturated fats');?></td>
						<td><?php echo defined_money_format($nutri_values['sat_fats'],1).' g'; ?></td>
						<td><?php echo defined_money_format($nutri_values['sat_fats']/100*$recipe_wt,1).' g'; ?></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td><?php echo _('Carbohydrates');?></td>
						<td><?php echo defined_money_format($nutri_values['carbo'],1).' g'; ?></td>
						<td><?php echo defined_money_format($nutri_values['carbo']/100*$recipe_wt,1).' g'; ?></td>
					</tr>
					<tr>
						<td>&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;<?php echo _('Of which sugars');?></td>
						<td><?php echo defined_money_format($nutri_values['sugar'],1).' g'; ?></td>
						<td><?php echo defined_money_format($nutri_values['sugar']/100*$recipe_wt,1).' g'; ?></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>

					<tr>
						<td><?php echo _('Proteins');?></td>
						<td><?php echo defined_money_format($nutri_values['protiens'],1).' g'; ?></td>
						<td><?php echo defined_money_format($nutri_values['protiens']/100*$recipe_wt,1).' g'; ?></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>

					<tr>
						<td><?php echo _('Salt');?></td>
						<td><?php echo defined_money_format($nutri_values['salt'],1).' g'; ?></td>
						<td><?php echo defined_money_format($nutri_values['salt']/100*$recipe_wt,1).' g'; ?></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<?php
					if(isset($extra_notification_free_field[0]->extra_notification_free_field)){ ?>
					<tr>
					<td><?php echo $extra_notification_free_field[0]->extra_notification_free_field ?></td><tr>

					<?php }
					?>
					<?php }?>
				</table>
				<?php if( isset($temperature) && !empty($temperature[0]['conserve_min']) && !empty($temperature[0]['conserve_max']) ) { ?>
				<br>

				<table style="width: 70%;">
					<tr>
						<td><b><?php echo _('Keep between'); ?></b></td>
						<td><?php echo defined_money_format($temperature[0]['conserve_min']).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'. _("and").'&nbsp;&nbsp;'; ?></td>
						<td><?php echo defined_money_format($temperature[0]['conserve_max']).'&nbsp;&nbsp;&nbsp;&deg;C'; ?></td>
					</tr>
				</table>
				<?php } ?>

				<br>
				<p><?php if( isset( $last_modified_date ) ) {
					echo _('This file was last modified on ').$last_modified_date;
				} else{
					echo _('This file was last modified on ').date('d/m/Y');
				}?>

			</div>
			<div style="clear: both"></div>
		</div>
		<div style="text-align: center"><br>
			<div align="left">
				<span style="font-size:9px"><font color="grey"><?php echo _('Although this technical data sheet has been accurately and accurately prepared by fooddesk bvba based on the product sheets supplied by the manufacturers / suppliers, fooddesk bvba can not accept responsibility for its complete accuracy and completeness. However, the possibility exists that the above information was changed by the manufacturer or manufacturer, such as a change of recipe, ingredients, proportions, allergens, without informing us. Therefore, it is possible that the given information is incomplete, incorrect or not up to date. We therefore refer to the correct information for the information as it appears on the article / product itself, this information is the only correct information.'); ?></font></span>
				<?php if(!$is_fixed){?>
				<br/>
				<p style="font-size:9px;text-align: right"><?php echo '** - '._('product data not completed').'.';?></p>
				<?php }?>
			</div><br/>
			<img alt="fdd_logo" src="<?php echo base_url().'assets/cp/images/fdd_logo.png'; ?>" style="width: 100px">
		</div>
	</body>
</html>