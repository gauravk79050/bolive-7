
	<?php if($product_information):?>
				<!--<input type="hidden" value="<?php echo $product_information['0']->id?>" name="prod_id" class="prod_id" >-->
			<input type="hidden" value="<?php echo $product_information['0']->direct_kcp?>" name="direct_kcp">
			<?php endif;?>
	    	<div <?php if($this->session->flashdata('webshop')){?>class="boxed"<?php }else{?>class="box"<?php }?>>
				<h3 id="product_info"> <?php echo _('Product information ')?></h3>					
				<div class="table">
					<table border="0">
						
			   			<tbody>
			   				<tr style="display: none;">
					           	<td class="textlabel"><?php echo _('Article No.')?></td>
					            <td style="padding-right:250px" colspan="2"><input type="text" class="text medium" size="10" id="pro_art_num" name="pro_art_num" <?php if(isset($product_information) && isset($product_information['0']->pro_art_num)): echo 'value="'.$product_information['0']->pro_art_num.'"'; endif;?> style="width:80px;"></td>
					        </tr>
			           		<tr style="display: none;">
				           		<td class="textlabel"><?php echo _('Select Category')?></td>
			               		<td style="padding-right:250px" colspan="2">
			               			<select onChange="inCategory(this.value);" class="select" id="categories_id" name="categories_id" style="padding:4px">
				                   		<option value="-1">-- <?php echo _('Select category')?> --</option>
				   		            <?php foreach($category_data as $category):?>
				    		        <?php if($product_information):?>
				                    	<option value="<?php echo $category->id?>"<?php if(($product_information)&&$category->id==$product_information['0']->categories_id): ?>selected="selected"<?php endif;?>><?php echo $category->name?></option>
				                    <?php else:?>
				                    	<option value="<?php echo $category->id?>"<?php if($category->id==$this->input->post('categories_id')):?>selected="selected"<?php endif;?>><?php echo $category->name?></option>
				                    <?php endif;?>
				                    <?php endforeach;?>
			                  		</select>
			                  		<input type="hidden" name="cat_id_for_product_add" id="cat_id_for_product_add" />
			                	</td>
			              	</tr>
			               	<?php if($product_information)
			              		if($product_information['0']->categories_id == 0)
			              			$subcategory_data = array();
			              	?>
			              	<tr style="display: none;">
					        	<td class="textlabel"><?php echo _('Select subcategory')?></td>
					            <td style="padding-right:250px" colspan="2">
					               	<select class="select"  id="subcategories_id" name="subcategories_id" style="padding:4px">
						        		<option value="-1">-- <?php echo _('Select subcategory')?> --</option>
						            <?php foreach($subcategory_data as $subcategory):?>
						            <?php if($product_information):?>
						                <option value="<?php echo $subcategory->id?>"<?php if(($product_information)&&$subcategory->id==$product_information['0']->subcategories_id): ?>selected="selected"<?php endif;?>><?php echo $subcategory->subname;?></option>
						            <?php else:?>
						                <option value="<?php echo $subcategory->id?>"<?php if($subcategory->id==$this->input->post('subcategories_id')):?>selected="selected"<?php endif;?>><?php echo $subcategory->subname;?></option>
						            <?php endif;?>
						            <?php endforeach;?>
					                </select>
					             </td>
					            </tr>
					            <tr>
					                <td class="textlabel"><?php echo _('Product Name')?></td>
					                <td style="padding-right:250px" colspan="2"><input type="text" class="text medium" size="30" id="proname" name="proname" <?php if($product_information):?>value="<?php echo stripslashes($product_information['0']->proname)?>"<?php endif;?> <?php if( $this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium'){if(isset($product_information)){if(!empty($product_information)){ if($product_information['0']->direct_kcp == 1 && $product_information['0']->parent_proid == 0 ){ if(!isset($fixed_pdf) || empty($fixed_pdf) || $product_type[0]->product_type ){ ?>style="background:pink"<?php }}}}}?>></td>
					            </tr>



			              
			      <!--         	<?php if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium'){?>
					            <tr id="pro_supp_tr" <?php if(isset($product_information) && !empty($product_information) && $product_information[0]->direct_kcp == 1 ){}else{ echo 'style="display: none"'; }?> > 
					                <td class="textlabel"><?php echo _('Producer/Supplier Name')?></td>
					                <td style="padding-right:250px" colspan="2">
						                <div style="float: left">
						                	<select id="producer" name="producer" onchange="show_new_producer(this)" style=" padding: 4px;width: 200px;" <?php if(isset($product_information)){if(!empty($product_information)){ if($product_information['0']->direct_kcp == 1 && $product_information['0']->direct_kcp_id != 0 && ($product_information['0']->fdd_producer_id != 0 || $product_information['0']->fdd_supplier_id != 0)) { echo 'disabled';  }}else{ echo 'disabled';} }else{ echo 'disabled';} ?> >
						                		<option value="0"><?php echo _('No Producer Added'); ?></option>
						               		<?php foreach ($producers as $producer){?>
						               			<option value="<?php echo $producer['s_id']; ?>" <?php if(isset($product_information)){ if(!empty($product_information)){ if($product_information['0']->fdd_producer_id == $producer['s_id']){ echo "selected";  } } } ?> ><?php echo stripslashes($producer['s_name']); ?></option>
						               		<?php }?>
						                		<option value="-1"><?php echo _('Add new producer'); ?></option>
						                	</select>
						                	<input style=" width: 200px; margin-top: 10px;display:<?php if(isset($producers) && empty($producers)){?>;<?php }else{?> none;<?php }?>" type="text" class="text medium" name="new_producer" id="new_pr" placeholder="<?php echo _('New producer Name');?>">
						                </div>	
						                <div style="float: left; height: 27px;">
					                		<p style=" width: 50px; text-align: center; font-weight: bold; margin-top: 6px;"><?php echo _('OR');?></p>
					                	</div>
					                	<div style="float: left">
						                	<select id="supplier" name="supplier" onchange="show_new_supplier(this)" style=" padding: 4px;width: 200px;" <?php if(isset($product_information)){if(!empty($product_information)){ if($product_information['0']->direct_kcp == 1 && $product_information['0']->direct_kcp_id != 0 && ($product_information['0']->fdd_producer_id != 0 || $product_information['0']->fdd_supplier_id != 0)) { echo 'disabled';  }}else{ echo 'disabled';} }else{ echo 'disabled';} ?> >
						                		<option value="0"><?php echo _('No Supplier Added'); ?></option>
						                		<?php foreach ($suppliers as $supplier){?>
						                			<option value="<?php echo $supplier['rs_id']; ?>" <?php if(isset($product_information)){if(!empty($product_information)){ if($product_information['0']->fdd_supplier_id == $supplier['rs_id']){ echo "selected";  } } } ?> ><?php echo stripslashes($supplier['rs_name']); ?></option>
						                		<?php }?>
						                		<option value="-1"><?php echo _('Add new supplier'); ?></option>
						                	</select>
						                	<input style=" width: 200px; margin-top: 10px;display:<?php if(isset($suppliers) && empty($suppliers)){?>;<?php }else{?> none;<?php }?>" type="text" class="text medium" name="new_supplier" id="new_sr" placeholder="<?php echo _("New Supplier Name");?>">
					                	</div>
					                	<div class="clear"></div>				     
					                 </td>
					            </tr>
					              
					            <tr>
					              	<td class="textlabel"><?php echo _('Product Type')?></td>
					              	<td colspan="2">
					              	
					              		<?php 
					              		$product_type_disable = 0;
					              		
					              		if(isset($product_information)){
											if(!empty($product_information)){
												if(!empty($used_fdd_pro_info) || !empty($used_own_pro_info)){
													$product_type_disable = 1;
												}
												
												if($product_information[0]->direct_kcp == 0 && $product_information[0]->direct_kcp_id != 0){
													$product_type_disable = 1;
												}
												if(($product_information[0]->id) && ($product_information['0']->categories_id != 0)){
													$product_type_disable = 1;
												}
											}
										}
					              		
					              		
					              		?>
					              		<select name="product_type" id="fd_product_type" onchange="change_pro_type(this)" style="padding:4px" <?php if($product_type_disable == 1){ echo 'disabled'; } ?> >
					              			<option value="0" <?php if(isset($product_information)){if(!empty($product_information)){ if($product_information['0']->direct_kcp == 0 ){ echo "selected";  } } }?> ><?php echo _('Custom Product');?></option>
					              			<option value="1" <?php if(isset($product_information)){if(!empty($product_information)){ if($product_information['0']->direct_kcp == 1 ){ echo "selected";  } } } ?> ><?php echo _('Fixed Product');?></option>
					              		</select>
					              	</td>
					              </tr>
			              <?php } ?> -->



			              
			              <!-- <tr>
			                <td class="textlabel"><?php echo _('Description')?></td>
			                <td style="padding-right:250px" colspan="2">
			                	<textarea rows="5" cols="50" type="textarea" id="prodescription" name="prodescription"><?php if($product_information):echo trim(stripslashes($product_information['0']->prodescription));endif;?></textarea>
							</td>
			              </tr>
			              
			              <tr>
                    		<td class="textlabel"><?php echo _('Image Upload')?>&nbsp;<a title="<?php echo _('Please upload a rectangle image in jpg/gif/png format')?>" href="#" id="help-prod0"><img width="16" height="16" src="<?php echo base_url(); ?>assets/cp/images/help.png"></a></td>
                   			<td style="padding-right:00px" colspan="2">
                   				<div id="uploaded_image"></div>
                   				<input type="hidden" id="x" name="x" />
				  				<input type="hidden" id="y" name="y" />
				  				<input type="hidden" id="w" name="w" />
				  				<input type="hidden" id="h" name="h" />
                   				<div><a href="javascript:;" class="thickboxed" style="text-decoration: none;"><input type="button" name="upload_image" id="upload_image" value="<?php echo _("Upload Image Here");?>" /></a></div>
                   			</td>
			              	</tr>
			             
			              	<?php if($product_information):?>
			              	<tr>
			                	<td class="textlabel"><?php echo _('Current image')?>
			                	</td> 
			                	<td style="padding-right:250px" ><?php if($product_information['0']->image) { ?>
				                  	<div id='uploaded_image_td'><img src="<?php echo base_url(''); ?>assets/cp/images/product/<?php echo $product_information['0']->image;?>" alt="<?php echo _('No product image available.Please upload one.')?>" style="height:300px" id="suploaded_image"/>
				                  		<a href="#" class="remove_image" rel="<?php echo $product_information['0']->id;?>"><?php echo _('Remove'); ?></a>
				                  		
				                  	</div>
				                  	<input class="rotated_image_hid" type="hidden" value="">
				                  	<input type="hidden" id="current_prod_image" value="<?php echo $product_information['0']->image;?>">
				                 	 <?php } else { ?>
				                  	<img src="<?php echo base_url(''); ?>assets/cp/images/product/no_image.jpg" alt="<?php echo _('No product image available.Please upload one.')?>"/>
				                  	<?php } ?>			                  
			                	</td>
			                	<td>
			                		<?php if(!empty($product_information['0']->image)) { ?> 
									<a href="javascript:;" class="pro_rotate_img" onClick="srotcw(this)" data-img1="<?php echo $product_information['0']->image;?>" title="<?php echo _('Rotate image Clock-wise')?>">
										<img src="<?php echo base_url();?>/assets/cp/images/cw.png"></a>
									<a href="javascript:;" class="pro_rotate_img" onClick="srotacw(this)" data-img2="<?php echo $product_information['0']->image;?>" title="<?php echo _('Rotate image Anti-clockwise')?>">
										<img src="<?php echo base_url();?>/assets/cp/images/acw.png">
									</a>
									<?php } ?>
			                	</td> -->
			              	</tr>
			              	 <tr id="notice"><td id="notice_msg"  colspan='2'><?php echo _("The section has been added in the new Controlepanel(2.0)" ); ?><?php echo "<br>"; ?><?php echo _("Please proceed with new Controlepanel(2.0)")?><?php echo "<br>"; ?><a target="_blank" style="color: #8c8c00;text-decoration: none;" href="<?php echo rtrim($this->config->item('new_obs_url'),'/') ?>"><?php echo rtrim($this->config->item('new_obs_url'),'/') ?></a></td></tr>
			              <?php endif;?>
			        	</tbody> 
					</table>
		          	<?php if($product_information):?>
					<!-- <div class="sub_div">
					    <div class="sub__div" colspan="2">
					        <input type="button" value="<?php echo _("Update");?>" class="submit" id="product_info_update" name="product_info_update">
					        <input type="hidden" value="add_edit" id="product_info_act" name="product_info_act">
					    	<input type="hidden" value="update" id="product_info_add_update" name="product_info_add_update">
					    	<input type="button" value="<?php echo _('Save & next')?>" class="submit" id="product_info_savenext" name="product_info_savenext">
						</div>
					</div>  -->         
					<?php else:?>
					<!-- <div class="sub_div">
					    <div class="sub__div" colspan="2">
					        <input type="button" value="<?php echo _('Send')?>" class="submit" id="product_info_add" name="product_info_add">
					      	<input type="hidden" value="add" id="product_info_add_update" name="product_info_add_update">
					      	<input type="button" value="<?php echo _('Save & next')?>" class="submit" id="product_info_savenext" name="product_info_savenext">
					   	</div>
					</div> -->
					<?php endif;?>
				</div>
			</div>
			