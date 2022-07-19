
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/ui/jquery.ui.all.css">
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.core.js?version=<?php echo version;?>"></script>
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.widget.js?version=<?php echo version;?>"></script>
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.sortable.js?version=<?php echo version;?>"></script>
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.position.js?version=<?php echo version;?>"></script>

<script src="<?php echo base_url(); ?>assets/plugins/jquery-ui-1.12.0.custom/jquery-ui.min.js?version=<?php echo version;?>" type="text/javascript"></script>


<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/jquery-ui-1.12.0.custom/jquery-ui.theme.min.css">


<script>
var changes="<?php echo _('Your changes saved successfully.'); ?>";
var select_cat="<?php echo _('Please select any category first');?>";
var tryagain="<?php echo _('This product cannot be added into current category. please try again later');?>";
var not_selected="<?php echo _('No Option selected');?>";
var removed="<?php echo _("Product Removed Successfully");?>";
</script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/cp/new_css/view_products.css">
<script>
jQuery(document).ready(function($){
	
	var products=<?php echo json_encode($products);?>;
	var products_arr_length=products.length;
	
	<?php if($this->session->userdata('login_via') == 'mcp'){?>
	$('.inp_fld').blur(function(){
		field_arr = $(this).attr('name').substring($(this).attr('name').lastIndexOf('_'));
		product_id = field_arr.split('_')[1];
		field = $(this).attr('name').split(field_arr)[0];
		value = $(this).val();
		obj = $(this);
		$('#loadmsg').show();
		$.post(
			base_url+'cp/products/rename_product',
			{"field_val":value,"product_id":product_id,"field":field},
			function(data){
				$('#loadmsg').hide();
				if(data != product_id)
	        		alert("<?php echo _('product not updated')?>");
				
 	           	if(obj.val().trim() == '') {
 	           		obj.css('border','1px solid #ff0000');
 	            }
 	           	else{
					obj.attr('style','');
					var shop_version = $('#shop_version').val();
	        		if(shop_version == 2 || shop_version == 3){
	    	    		$.post(
		        			base_url+"cp/shop_all/update_json_files/"+shop_version,
	    	    			{'action':'category_json'},
	    	    			function(data){},
	    	    			'json'
	    	    		);
	        		}

	        		var infodesk_status = $('#infodesk_status').val();
		        	if(infodesk_status == 1){
		        		$.post(
		        			base_url+"cp/shop_all/update_desk_files/"+infodesk_status,
		        			{'action':'category_json'},
		        			function(data){},
		        			'json'
		        		);
	        		}

		        	jQuery.post(
		        			base_url+"cp/shop_all/update_allergenkart_files/",
		        		    {'action':'category_json'},
		        		    function(data){},
		        		    'json'
		        	);
		        	
				}
		});
	});
	<?php }?>
	
	
	
});

</script>
<script src="<?php echo base_url();?>assets/cp/new_js/view_products.js?version=<?php echo version;?>"></script>
<!-- MAIN -->

<div id="main">
	<div id="main-header">
    	<h2><?php echo _('Products')?></h2>
     	<span class="breadcrumb"><a href="<?php echo base_url();?>cp/products/"><?php echo _('Home')?></a> &raquo; <?php echo _('Products')?></span>
     	
		<?php $messages = $this->messages->get();?>
		<?php if(isset ($messages)){if($messages != array()): foreach($messages as $type => $message): ?>			
			<?php if($type == 'success' && $message != array()):?>
				<div id="succeed"><?php echo $message[0];?></div>
			<?php elseif($type == 'error' && $message != array()):?>	
				<div id="error"><strong><?php echo _('Error')?></strong> : <?php echo $message[0];?></div>	
			<?php endif;?>
		<?php endforeach; endif;}?>
	</div>
	<?php /*$trail_date1= strtotime($trail_date);?>
	
	<div style="background:#EBF7C5; padding:10px 8px; margin-bottom:20px; text-align:center; border:1px solid #ddd; margin-right: 245px; margin-left:0px;">
		<span style="display: inline-block; text-align: left !important;">
			<?php echo _("Link to Bestelonline live (what visitor are seeing)");?> - <b><a target="_blank" href="<?php echo $this->config->item("portal_url").$type_slug.'/'.$company_slug;?>" target="_blank"><?php echo $this->config->item("portal_url").$type_slug.'/'.$company_slug;?></a></b>
		
		<?php if( ($ac_type_id=='2' || $ac_type_id=='3') && $general_settings[0]->shop_testdrive && $on_trial=='1' && $general_settings[0]->hide_bp_intro != '1' ){ ?>
			<br />
			<br />
	   		<?php echo _("Link to your test environment (to do settings)");?> - <b><a target="_blank" href="<?php echo $this->config->item("portal_url").$type_slug.'/'.$company_slug.'/testdrive';?>"> <?php echo $this->config->item("portal_url").$type_slug.'/'.$company_slug.'/testdrive';?> </a>    <?php echo _(' '.' (till date)').date('d-m-Y',$trail_date1);?></b>
	 	<?php } ?>
	 	
	 	<?php if( $ac_type_id=='3' && $on_trial=='1' ){?>
	 		<br />
	 		<br />
	 		<?php echo _("Link to simulation OBS-Module (in your website)");?> - <b><a target="_blank" href="http://www.onlinebestelsysteem.net/testdrive/bestelonline.php?cid=<?php echo $this->company->id?>"> <?php echo _('Click here');?> </a></b>
	 	<?php }?>
	 	</span> 		
	</div> 		
	<?php */?>
	
    <div style="display:none" id="update_checkbox" class="notification"></div>
	<div style="display:none" id="succeed_status"><?php echo _('Status successfully updated.')?></div>
    <div style="display:none" id="error_status"><?php echo _('Error occurred while updating status')?></div>
    <div style="display:none" id="update_product_status"><?php echo _('Product successfully updated.')?></div>
    <?php if (isset($shared_products) && !empty($shared_products)){?>
	   <div id="content-div">
		    <div id="content-container1">
		    	<table>
		    			<?php foreach ($shared_products as $key=>$val){
		    			$exploded_array = explode("##", $key);
		    				?>
		    			<tbody>
		    			<input type="hidden" value="<?php echo $exploded_array[1];?>">
		    			<tr>
		    				<td colspan="6">
		    					<b><?php echo $exploded_array[0]?></b> <?php echo _("HAS SHARED PRODUCTS WITH YOU");?>
		    				</td>	
		    			</tr>
		    			<?php if (is_array($val)){
		    				foreach ($val as $shared_key => $shared_val){?>
				    			<tr id="<?php echo $shared_val['proid'];?>">
				    				<td style="width:20%"><?php echo $shared_val['product_name'];?></td>
				    				<td style="width:20%">
			                    		<select  onchange="get_subcat_list(this.value,this);"  class="select" type="select" id="comp_categories_id<?php echo $shared_key;?>" name="comp_categories_id" style="width: 75%;">
											<option value="-1">-- <?php echo _('Select Category'); ?> --</option>
										 	<?php foreach($category_data as $category):?>
										       <option value="<?php echo $category->id?>"><?php echo $category->name?></option>
											<?php endforeach;?>
										</select>
									</td>
				    				<td style="width:20%">
										<select class="select" type="select" id="comp_subcategories_id<?php echo $shared_key;?>" name="comp_subcategories_id" style="width: 85%;">
											<option value="-1">-- <?php echo _('Select Subcategory')?> --</option>
			                      		</select>
				                     </td>
				    				<td style="width:20%"><?php echo $shared_val['remark'];?></td>
				    				<td style="width:15%"><input type="button" onclick="assign_share_prod(this);" style="background-color: #E1E1E1;cursor: pointer;" class="text" value="<?php echo _("Assign");?>"></td>
				    				<td style="width:15%"><a href="javascript:;" onclick="reject_share_prod(this);"><?php echo _("Reject");?></a></td>
				    			</tr>
			    				<?php
								}?>
								</tbody>
		    			<?php  	}?>
		    			<?php }?>
		    	</table>
		    </div>
	    </div>
    <?php } ?>
    
    <div id="content">
		<div id="content-container">
		<!-- -----------------------Code for showing notifications -->
		      	<?php $a_type = $this->company->ac_type_id;?>
		      	<?php if(isset($notifications)) {
		      	foreach ($notifications as $noti ){
		      		$ac_type_arr = json_decode($noti['company_type']);
		      		$show_flag = FALSE;
		      		foreach ($ac_type_arr as $ac_type){
						if($ac_type == $a_type){
							$show_flag = TRUE;
							BREAK;
						}
					}
					foreach($closed_noti as $c_noti){
						if($c_noti->notification_id == $noti['id']){
							$show_flag = FALSE;
							BREAK;
						}
					}
		      		if($show_flag){ ?>
					<div style="background:#EBF7C5; padding:10px 8px;width:96%; margin-bottom:20px; text-align:left; border:1px solid #ddd; margin-right: 245px; margin-left:0px;">
						<a id="noti_<?php echo $noti['id'];?>" href="javascript:;" data-title="close" onclick="close_this_noti(this.id)" style="float:right"><img alt="close" width="15" src="<?php echo base_url('')."assets/cp/images/Delete.gif" ?>" ></a>
						<h4><?php echo $noti['subject'];?></h4>
						<?php echo $noti['notification']; ?>
					</div>
				<?php }
		      	 	}
		      	 }?>
        	<div class="box">
          		<h3><?php echo _('Product details')?></h3>
          		<div class="table">
	            	<form action="<?php echo base_url()?>cp/products/products_addedit/add" method="post" name="product_add" id="product_add">
	            		<table cellspacing="0">
    		        		<tbody>
				  				<tr>
                    				<td class="notice_text" colspan="9" style="text-align:center">**<?php echo _('You can choose in which order the products are displayed on the website. Number 1 is placed on top.')?></td>
                  				</tr>
                  				<tr>
	                    			<td colspan="3" style="text-align:right"><?php echo _('Select Category')?></td>
	                    			<td colspan="6" style="text-align:right">
	                    				<select onchange="inCategory(this.value);" class="select" type="select" id="categories_id" name="categories_id">
											<option value="-1">-- <?php echo _('Select Category'); ?> --</option>
					                       	<?php foreach($category_data as $category):?>
										       <option value="<?php echo $category->id?>" <?php if($cat_id&&$category->id==$cat_id): ?>selected="selected"<?php endif;?>><?php echo $category->name?></option>
											<?php endforeach;?>
										</select>
									</td>
                  				</tr>
                  				<tr>
									<td colspan="3" style="text-align:right"><?php echo _('Select Subcategory')?></td>
									<td colspan="6" style="text-align:right">
										<select onchange="inSubcategory(<?php echo $cat_id?>,this.value);" class="select" type="select" id="subcategories_id" name="subcategories_id">
											<?php if($subcategory_data):?>
						   						<option value="-1">-- <?php echo _('Select Subcategory')?> --</option>
												<?php foreach($subcategory_data as $sub_category):?>
													<option value="<?php echo $sub_category->id?>" <?php if($sub_category->id==$sub_cat_id): ?>selected="selected"<?php endif;?>><?php echo $sub_category->subname ?></option>
												<?php endforeach;?>
						   					<?php else:?><option value="-1">-- <?php echo _('No Subcategory')?> --</option><?php endif;?>
	                      				</select>
	                      			</td>
                  				</tr>
                  			
	                  			<tr>
									<td colspan="3" style="text-align:right">
										<select id="serach_opt" onchange="change_search_type(this.value)" style="display: inline !important;">
											<option value="1"><?php echo _('Search for keyword')?></option>
											<option value="2"><?php echo _('Search in recipe')?></option>
										</select>
									</td>
									<td id="all_product" colspan="6">
										&nbsp;&nbsp;&nbsp;<input type="text" name="prosearch" id="prosearch" size="30" class="text" placeholder="<?php echo _('Product Name')?>">
	                      				<img alt="search_all_product" id="filtered_key_product" src="<?php echo  base_url()."assets/images/search.png";?>">
	                      			</td>
	                      			<td id="recipe_product" colspan="6" style="display: none">
										&nbsp;&nbsp;&nbsp;<input type="text" id="prosearchreci" size="30" class="text proreciname" placeholder="<?php echo _('Ingredient')?>">
										<div style="float:right; width:25px;height:25px">
											<img id="loding_gif" alt="loading" src="<?php echo  base_url()."assets/images/loading2.gif" ?>" style="display:none;width: 22px; margin-top: 2px;">
										</div>
	                      			</td>	                      			
	                  			</tr>
                  			</tbody>
                  		</table>
					</form>
					<table>
                  			<tr><!-- 
                  				<?php if($this->company->k_assoc){?>
                    			<td colspan="9" style="text-align:right;">
                    				<a href="<?php echo base_url().'cp/keurslager/products?height=300&width=700'?>" title="<?php echo _('Keurslager Products');?>" class="thickbox"><?php echo _('Products KEURSLAGERS')?></a>&nbsp;&nbsp;&nbsp;&nbsp;
                    				<a href="javascript:;" onclick="document.product_add.submit()"><?php echo _('Add Product ')?></a>
                    			</td>
                    			<?php }else if($this->company->i_assoc){?>
                    			<td colspan="9" style="text-align:right;">
                    				<a href="<?php echo base_url().'cp/i_system/products?height=300&width=700'?>" title="<?php echo _('Add i-Products');?>" class="thickbox"><?php echo _('Add i-Products')?></a>&nbsp;&nbsp;&nbsp;&nbsp;
                    				<a href="javascript:;" onclick="document.product_add.submit()"><?php echo _('Add Product ')?></a>
                    			</td>
                    			<?php }else{?>
                    			<td colspan="9" style="text-align:right;"><a href="javascript:;" onclick="document.product_add.submit()"><?php echo _('Add Product ')?></a></td>
                    			<?php }?>  -->
                    			<td colspan="4" style="text-align:left;"><?php if($no_cat != 0){?><a href="<?php echo base_url()?>cp/products/assign_category"><?php echo _('Products without category')?><?php echo '('.$no_cat.')';?></a><?php }?></td>
                    			<?php if(($this->session->userdata('menu_type') == 'fdd_light') || ($this->session->userdata('menu_type') == 'fdd_pro') || ($this->session->userdata('menu_type') == 'fdd_premium') || ($this->session->userdata('menu_type') == 'fooddesk_light')){?>
                    			<td colspan="9" style="text-align:right;">
                    				<!--<a href="<?php echo base_url()?>cp/products/product_recipe"><?php echo _('Product Recipe')?></a>&nbsp;&nbsp;&nbsp;&nbsp;-->
	                    			<?php if ($this->session->userdata('menu_type') != 'fooddesk_light'){?>
	                    				<a href="<?php echo base_url().'cp/shared'?>" id="shared_prod" title="<?php echo _('Share Products');?>"><?php echo _('Share Products')?></a>&nbsp;&nbsp;&nbsp;&nbsp;
		                    			
		                    			
		                    			<?php if($fdd_credits > 0){?>
		                    				<!-- <a href="<?php echo base_url().'cp/fooddesk/products?height=300&width=700'?>" title="<?php echo _('Add product from FoodDESK (If not found, add new)');?>" class="thickbox"><?php echo _('Add FoodDESK Product')?></a>&nbsp;&nbsp;&nbsp;&nbsp; -->
		                    			<?php  
	                    				}else{?>
		                    				<!-- <a href="#TB_inline?height=300&width=500&inlineId=credit_require" title="<?php echo _('No credit left!');?>" class="thickbox"><?php echo _('Add FoodDESK Product')?></a>&nbsp;&nbsp;&nbsp;&nbsp; -->
		                    			<?php $fdd_credits = 0;
		                    				}
		                    				
		                    				
		                    			}?>
                    				<!-- <a href="javascript:;" onclick="document.product_add.submit()" title="<?php echo _('Add Custom Product');?>" class=""><?php echo _('Add Custom Product')?></a>&nbsp;&nbsp;&nbsp;&nbsp; -->
                    			</td>
                    			<?php }else{?>
                    				<!-- <td colspan="9" style="text-align:right;"><a href="javascript:;" onclick="document.product_add.submit()"><?php echo _('Add Product ')?></a></td> -->
                    			<?php }?>
                  			</tr>
                  			<tr>
                    			<td colspan="9" style="text-align:center"></td>
                  			</tr>
                   </table>
                   <?php $seg = $this->uri->segments;
                   $ord_col = true;
                   if(isset($seg[4]) && $seg[4] == "filtered_product"){$ord_col = false;}?>
                   <form method="post" action="">
					<table cellspacing="0" id="prosheet">
						<thead>
                  			<tr>
                  				<th class="checkall"><input type="checkbox"></th>
                    			<?php if($ord_col){?>
                    			<th><?php echo _('ord.')?></th>
				  				<?php }else{?><th></th><?php }?>
								<th><?php echo _('Article No.')?></th>
                    			<th><?php echo _('Product Name')?></th>
                    			<th><?php echo _('Description')?></th>
                    			<th><?php echo _('Rate')?></th>
                    			<th style="display: none"><?php echo _('Show Images')?></th>
                    			<th><?php echo _('New')?></th>
                    			<th><?php echo _('Status')?></th>
                    			<th><?php echo _('Action')?></th>
                  			</tr>
                  			<tr>
                  			<td colspan="9"></td>
                  			</tr>
                  			
                		</thead>
                		<tbody class="sortable">
			                <?php if(!$products):?>
							<tr>
                    			<td colspan="9"><span class="field-error"><?php echo _('Product list is empty.')?></span></td>
                  			</tr>
				  			<?php else:?>
				 			<?php foreach($products as $product):?>
				 				<?php $td_style = '';
			 					if( $product->prod_sent == 1 ) {
			 						if( isset($product->no_fdd_con) && $product->no_fdd_con == 1 ){
			 							// FFE4C4
				 						$td_style = 'style="background:#FFEBCD"';	
				 					} else {
				 						$td_style = 'style="background:#FFFFE0"';
				 					}
				 				} else {
				 					if( isset($product->no_fdd_con) && $product->no_fdd_con == 1 ){
				 						$td_style = 'style="background:#FBE5E5"';
				 					}
				 				}?>
				  			<tr class="odd">
				  				<td <?php echo $td_style;?> >
				  					<input type="checkbox" class="check" value="<?php echo $product->id;?>">
				  				</td>
				  				<?php if($ord_col){ ?>
				  				<td <?php echo $td_style;?> >
                    				<img width="18" border="0" class="handle" src="<?php echo base_url(); ?>assets/cp/images/move.png" style="cursor: pointer;" title="<?php echo _("Drag me");?>"/>
                    				<input type="hidden" name="order_display[]" value="" />
                    			</td>
				  				<?php }else{?><td <?php echo $td_style;?> ></td>
                    			<?php }?>
								<td <?php echo $td_style;?> >
									<input type="hidden" name="ids[]" value="<?php echo $product->id; ?>" />
									<input type="text" name="pro_art_num_<?php echo $product->id; ?>" value="<?php echo $product->pro_art_num; ?>" size="6" class="inp_fld" />
								</td>
								<td <?php echo $td_style;?> >
						    		<input type="text" name="proname_<?php echo $product->id; ?>" value="<?php echo stripslashes($product->proname)?>" size="20" class="inp_fld" <?php if(trim($product->proname) == ''){echo 'style="border:1px solid #ff0000"';}?> />
						    	</td>
								<td <?php echo $td_style;?> >
									<input type="text" name="prodescription_<?php echo $product->id; ?>" value="<?php echo stripslashes($product->prodescription)?>" size="25" class="inp_fld" /></span>
								</td>
								<td <?php echo $td_style;?> >
								
									<?php if($product->sell_product_option=='per_unit' || $product->sell_product_option=='client_may_choose') { ?>
								
									<input type="text" name="price_per_unit_<?php echo $product->id; ?>" value="<?php echo round($product->price_per_unit,2)?>" size="3" class="inp_fld" style="" /><span style="">&nbsp;&euro;</span>
									<br/>
								
									<?php } if($product->sell_product_option=='per_person') { ?>								
									<input type="text" name="price_per_person_<?php echo $product->id; ?>" value="<?php echo round($product->price_per_person,2)?>" size="3" class="inp_fld" style="" /><span style="">&nbsp;&euro;&nbsp;/&nbsp;<?php echo _("Per p.");?></span>
									<br/>
									<?php } if($product->sell_product_option=='weight_wise' || $product->sell_product_option=='client_may_choose') { ?>
										<br />
									<input type="text" name="price_weight_<?php echo $product->id; ?>" value="<?php echo round($product->price_weight*1000,2)?>" size="3" class="inp_fld" style="" />
									<span style="">&nbsp;&euro;&nbsp;/&nbsp;kg</span>
									<br/>
									<?php } ?>
								</td>
								
								<!--<td style="display: none" <?php //if($product->no_fdd_con == 1){echo 'style="background:#FBE5E5"';}?> >
									<input id="image_display" class="checkbox" type="checkbox" name="image_display" value="1"<?php //if($product->image_display==1):?>checked="checked"<?php //endif;?> onclick="set_checkbox_value('image_display',this,<?php //echo $product->id;?>)"></span>
								</td>-->
								<td <?php echo $td_style;?> >
									<input id="type" class="checkbox" type="checkbox" name="type" value="1"<?php if($product->type==1):?>checked="checked"<?php endif;?> onclick="set_checkbox_value('type',this,<?php echo $product->id;?>)"></span>
								</td>
					 			<td	<?php echo $td_style;?> >
					 				<div class="select-popup-container">
                     					<div style="display: none; position:absolute" id="product_option_div_<?php echo $product->id; ?>" class="popup-tools cm-popup-box hidden"> <img width="13" height="13" border="0" onclick="close_option(<?php echo $product->id;?>, 'product')" src="<?php echo base_url(); ?>assets/cp/images/icon_close.gif" alt="" class="close-icon no-margin cm-popup-switch">
                        					<ul class="cm-select-list">
						  						<li><a id="show_<?php echo $product->id; ?>" class="status-link-a cm-active" href="javascript:void(0)" onclick="return update_product_status(<?php echo $product->id; ?>, '1', '<?php echo _('Show'); ?>', 'product');">
						  						<?php echo _('Show'); ?></a></li>
                          						<li><a id="hide_<?php echo $product->id; ?>" class="status-link-d cm-ajax" href="javascript:void(0)" onclick="return update_product_status(<?php echo $product->id; ?>, '0', '<?php echo _('Hide'); ?>', 'product');">
						  						<?php echo _('Hide'); ?></a></li>
                        					</ul>
                      					</div>
                      					<div onclick="open_option(<?php echo $product->id; ?>, 'product')" id="product_div_<?php echo $product->id; ?>" class="selected-status status-a cm-combination cm-combo-on"> <a id="status_value_<?php echo $product->id; ?>" class="cm-combo-on cm-combination" style="width:30px;"> <?php if($product->status=='0'): ?><?php echo _('Hide'); ?><?php else:?><?php echo _('Show'); ?><?php endif;?></a>
										</div>
                    				</div>
                    			</td>
								<td <?php echo $td_style;?> >
									<a href="<?php echo base_url().'cp/products/products_addedit/product_id/'.$product->id?>" class="edit" title="<?php echo _('Edit');?>"> <img width="16" height="16" border="0" alt="Edit" src="<?php echo base_url(); ?>assets/cp/images/edit.gif"></a> | 
									<a onclick="return confirmation('<?php echo $product->id; ?>');" href="#" class="delete" title="<?php echo _('Delete');?>"><img width="16" height="16" border="0" alt="remove" src="<?php echo base_url(); ?>assets/cp/images/delete.gif"></a>
									<?php if(!$product->parent_proid){?> | <a href="<?php echo base_url().'cp/products/product_clone/'.$product->id?>" class="delete" title="<?php echo _('Clone');?>"><img width="16" height="16" border="0" alt="clone" src="<?php echo base_url(); ?>assets/cp/images/arrow-turn-180.png"></a><?php }?> 
									<?php if( ($this->session->userdata('menu_type') == 'fdd_light') || ($this->session->userdata('menu_type') == 'fdd_pro') || ($this->session->userdata('menu_type') == 'fdd_premium') ){?>
										<?php if($product->direct_kcp == 1 && $product->direct_kcp_id != 0 && isset($product->pdf_name)){?>
											| <a target="_blank" class="producer_sheet" href="<?php echo $this->config->item('fdd_url').'dwpdf/?pdf='.$product->pdf_name;?>" title="<?php echo _('Product Sheet');?>"><img src="<?php echo base_url();?>assets/cp/images/01-pdf.png" style="width: 15px;" /></a> 
											<?php if($product->product_type == 1){ ?>
												<span>| GS1</span>
											<?php }
											
										 }elseif($product->direct_kcp == 1 && $product->direct_kcp_id == 0){ ?>
											
											| <a target="_blank" class="technical_sheet" href="<?php echo base_url().'cp/fooddesk/technical_sheet/'.$product->id;?>" title="<?php echo _('Product Sheet');?>"><img src="<?php echo base_url();?>assets/cp/images/01-pdf.png" style="width: 15px;" /></a>
											
									<?php }elseif($product->direct_kcp == 0 && $product->recipe_weight != 0){?>
									
											| <a target="_blank" class="technical_sheet" href="<?php echo base_url().'cp/fooddesk/technical_sheet/'.$product->id;?>" title="<?php echo _('Technical Sheet');?>"><img src="<?php echo base_url();?>assets/cp/images/02-pdf.png" style="width: 15px;" /></a>
											<?php if( isset($show_recipe) && $show_recipe == 1 ){ ?>
											| <a target="_blank" class="recipe_sheet" href="<?php echo base_url().'cp/fooddesk/recipe_sheet/'.$product->id;?>" title="<?php echo _('Recipe Sheet');?>"><img src="<?php echo base_url();?>assets/cp/images/03-pdf.png" style="width: 15px;" /></a>
											<?php } ?>
										<?php }?>
									<?php }?>
									<?php if( $product->prod_sent == 1 ){ ?>
											<br>
											<input style="margin-top: 5px;" type="checkbox" data-id="<?php echo $product->id;?>" class="styled mark_as_approved"></input>&nbsp
											<span><?php echo _( 'Recipe approved' );?></span>
						    		<?php }?>
								</td>
								
							</tr>
				  			<?php endforeach;?>
							<tr>
							    <td colspan="9">
									<input type="button" name="save" id="save" value="<?php echo _('UPDATE'); ?>" />
								  	<input type="button" name="remove" id="remove" value="<?php echo _('REMOVE'); ?>" />
								<?php if(($this->session->userdata('menu_type') == 'fdd_light') || ($this->session->userdata('menu_type') == 'fdd_pro') || ($this->session->userdata('menu_type') == 'fdd_premium')){?>
								  	<!-- <button type="button" id="technical_sheets" data-link='<?php echo base_url()."cp/fooddesk/all_technical_sheets/".$cat_id."/".$sub_cat_id."/";?>'><?php echo _('PRINT SHEETS');?></button>
								  	<?php if( isset($show_recipe) && $show_recipe == 1 ){ ?>
								  		<button type="button" id="recipe_sheets" data-link='<?php echo base_url()."cp/fooddesk/all_recipe_sheets/".$cat_id."/".$sub_cat_id."/"; ?>'><?php echo _('PRINT RECIPES');?></button> -->
								  	<?php } ?>
									<img src="<?php echo base_url();?>assets/cp/images/01-pdf.png" style="margin-left:120px;width: 15px;" /> - <b><?php echo _('Producer Sheet(pdf)');?> </b>&nbsp;&nbsp;&nbsp;&nbsp;
									<img src="<?php echo base_url();?>assets/cp/images/02-pdf.png" style="width: 15px;" /> - <b><?php echo _('Techinal Sheet(pdf)');?> </b>&nbsp;&nbsp;&nbsp;&nbsp;
									<?php if( isset($show_recipe) && $show_recipe == 1 ){ ?>
										<img src="<?php echo base_url();?>assets/cp/images/03-pdf.png" style="width: 15px;" /> - <b><?php echo _('Recipe Sheet(pdf)');?> </b>&nbsp;&nbsp;&nbsp;&nbsp;
									<?php } ?>	
								<?php }?>
								</td>
							</tr>
							
<!-- 							<tr> -->
<!-- 							    <td colspan="9"> -->
<!-- 									<a href="javascript:;" onclick="print_these();"><?php //echo _('Print these products');?></a>
<!-- 									&nbsp;&nbsp;&nbsp;&nbsp; -->
<!-- 									<a href="javascript:;" onclick="print_all();"><?php //echo _('Print all products');?></a>
									
								<?php //if(($this->session->userdata('menu_type') == 'fdd_light') || ($this->session->userdata('menu_type') == 'fdd_pro') || ($this->session->userdata('menu_type') == 'fdd_premium')){?>
<!-- 										&nbsp;&nbsp;&nbsp;&nbsp; -->
<!-- 										<a target="_blank" href = "<?php //echo base_url()."cp/fooddesk/all_technical_sheets/".$cat_id."/".$sub_cat_id; ?>" ><?php //echo _('Download Technical sheets of these products');?></a>
<!-- 										&nbsp;&nbsp;&nbsp;&nbsp; -->
<!-- 										<a target="_blank" href = "<?php //echo base_url()."cp/fooddesk/all_recipe_sheets/".$cat_id."/".$sub_cat_id; ?>" ><?php //echo _('Download Recipe sheets of these products');?></a>
<!-- 										&nbsp;&nbsp;&nbsp;&nbsp; -->
<!-- 										<a target="_blank" href = "<?php //echo base_url()."cp/fooddesk/zenius_export/".$cat_id."/".$sub_cat_id;; ?>" ><?php //echo _('Zenius Export');?></a>
<!-- 										&nbsp;&nbsp;&nbsp;&nbsp; -->
<!-- 										<a class="dat_format_export" href = "javascript:void(0);"><?php //echo 'DIGI '._('Export');?>
										<div style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8; display: none;" class='loadimg'>
<!--   											<img style="position: absolute; color: White; top: 50%; left: 45%;" src="<?php //echo base_url();?>assets/cp/images/ajax-loading.gif">
<!-- 										</div>	 -->
									<?php //}?>
<!-- 								</td> -->
<!-- 							</tr> -->
							<?php endif;?>
							<?php if(($this->session->userdata('menu_type') == 'fdd_light') || ($this->session->userdata('menu_type') == 'fdd_pro') || ($this->session->userdata('menu_type') == 'fdd_premium')){?>
	                    		<tr>
								    <td colspan="9">
									   <p id="fdd_credits"> <?php echo _('You have still ').$fdd_credits._(' FoodDESK credits left!');?></p>
									</td>
								</tr>	
							<?php }?>
                		</tbody>
              		</table>
              		</form>
              		<ul class="pagination">
						<?php if($links):echo $links; endif;?>
              		</ul>
              		<div style="margin-left:50px"> </div>
				</div>
        	</div>
      	</div>
    </div>
    <div id="credit_require" style="display: none">
   		<p><?php echo _('Sorry! Currently You have no credit to use a FoodDESK product.');?></p>
   		<p><?php echo _('To buy credits, choose a package.');?></p>
   		<ul>
   			<li><a onclick="add_credit(100)" href="javascript:;"><?php echo _('100 products/credits for 10');?>&euro;</a></li>
   			<li><a onclick="add_credit(200)" href="javascript:;"><?php echo _('200 products/credits for 15');?>&euro;</a></li>
   		</ul>
    </div>
<!-- /content -->