<html>
	<head>
		<script type="text/javascript">
			var total_recipe_wt = parseInt($('#total_recipe_wt').html());
			var availableTags = new Array();
			var select_prdct = 0;
			
			<?php  $i1 = 0; ?>
			<?php if(!empty($own_products)){ ?>
				<?php foreach($own_products as $pro){ ?>
					availableTags[<?php echo $i1;?>] = {'value':'<?php echo $pro['id'];?>','label':'<?php echo $pro['proname'];?>'};
					<?php $i1++; ?>
				<?php } ?>
			<?php } ?>

			
			function add_row(){

				var prodct = $( "#search_box" ).val();
				var quant = $( "#quant" ).val();
				var unit = $( "#unit" ).val();
				var quantity = quant*unit;
				var fdd_pro_id = $( "#hidden_search_box_id" ).val();
				
				
				var new_html = '';
				new_html += '<tr id="tr_'+fdd_pro_id+'" rel="'+quantity+'" class="product_row"><td>';
				new_html += "<span><img onclick='remove_row("+fdd_pro_id+")' class='my_fdd_img' src='<?php echo base_url()."assets/images/delete_pro.png";?>'></span>&nbsp;&nbsp;<span><input type='text' class='pro_quant text' style='width:6%' value='"+quantity+"' onkeyup='calculate_total()' > <strong> <?php echo _("gm");?> </strong></span><span class='prdc_name'>"+prodct+"</span>";
				new_html += '	<input type="hidden" class="hidden_pro_prefix" value="">'
				new_html += '</td></tr>';
				if(select_prdct){
					if(quantity > 0  ){
						$("#fdd_row_container").append(new_html);
						$( "#search_box" ).val('');
						$( "#quant" ).val('');
						$( "#unit" ).val('');
						select_prdct = 0;
						calculate_total();
					}else{
							alert("<?php echo _('Quantity of product must be greater than 0 gm.');?>");
						}
				}
				else{
						alert("<?php echo _('Please Add a product from suggestion first.');?>");
					}
				
			}

			function remove_row(rmv_pro_id){
				$("#tr_"+rmv_pro_id).remove();
				calculate_total();
			}

			function submit_all_data(){

 				var flag = 0;
 				$(".pro_quant").each(function(){
					if(parseInt($(this).val()) == 0 || isNaN(parseInt($(this).val()))){
						flag++;
					}
				});

 				if(flag > 0){
 					alert("<?php echo _("No quantity field can be 0 or empty!"); ?>");
 					return false;
 				}
 				
 			
 				var data_array = new Array();
 				var total_quant = 0;
 				var data_array_str ='';
 				$("#fdd_row_container .product_row").each(function(){
 					var row_id = $(this).attr("id");
 					var fdd_id = row_id.substring(3);
 					var pro_quantity = $(this).find(".pro_quant").val();
 					var hidden_pro_prefix = $(this).find(".hidden_pro_prefix").val();
 					total_quant = total_quant+parseInt(pro_quantity);
 					var new_data_array= {fdd_pro_id:fdd_id,quantity:pro_quantity,hidden_pro_pre:hidden_pro_prefix};
 					data_array_str += fdd_id+'#'+pro_quantity+'#'+$(this).find(".prdc_name").html()+'#'+hidden_pro_prefix+'**';
  					data_array.push(new_data_array);
  					
 				});

 				if(data_array.length == 0){
					alert("<?php echo _('No product added'); ?>");
					return false;
				}else{
					data_array = data_array.sort(sortFunction);
				}
 				
 				if(total_quant <= total_recipe_wt){
 					
 					$('#kp_ing_own').html('');
 					
 					$("#hidden_own_pro_quantity").val(data_array_str);
	//				$.post("<?php echo base_url().'cp/fooddesk/update_fdd_pro_quant'; ?>",
//  							{'update_array':data_array},
//  							function(res_data){
//  								var arr1 = JSON.parse(res_data);

//  								$("#nutri_values").show();
//  								$("#e_val_1").html(arr1['e_val_1']);
//  								$("#e_val_2").html(arr1['e_val_2']);
//  								$("#proteins").html(arr1['protiens']);
//  								$("#carbo").html(arr1['carbo']);
//  								$("#fats").html(arr1['fats']);
								
//  	 						});
 					for(var i=0;i<data_array.length;i++){
 						add_own_prod_here(data_array[i]['fdd_pro_id'],data_array[i]['quantity'],data_array[i]['hidden_pro_pre']);
 					}
 					alert("<?php echo _('Quantity of added FoodDESK products saved successfully. Please add related ingrediets, allergence and traces.');?>");
 					calculate_fdd_total();
 					add_all_own_pro(); 
 				}else{
 					alert("<?php echo _('Total quantity of all products must not be more than recipe weight');?>");
 				}
				
// 				$('#TB_window').fadeOut();
// 				parent.tb_remove();
 //				remove_all_ingredients();
 				add_product_prefixes();
			}

			function show_suggestion(){
				
			}


			function autocomplete_intialize(){
				$( "#search_box" ).autocomplete({
					minLength: 0,
					source: availableTags,
					focus: function( event, ui ) {
						$( "#search_box" ).val( ui.item.label);
						return false;
					},
					select: function( event, ui ) {
						$( "#search_box" ).val( ui.item.label );
						$( "#hidden_search_box_id").val( ui.item.value );
						select_prdct = 1;
						return false;
					}
				})
				.data( "ui-autocomplete" )._renderItem = function( ul, item ) {
					return $( "<li></li>" )
						.data( "item.autocomplete", item )
						.append( item.label )
						.appendTo( ul );
				};
			}

			

			function calculate_total(){

				var fdd_total = $("#hidden_fdd_total").val();
				var total = 0;
				total += parseInt(fdd_total);
				$(".pro_quant").each(function(){
					if(isNaN(parseInt($(this).val())))
						total += 0;
					else
						total += parseInt($(this).val());
				});
				$("#total_quant").html(total);
				if(total == 100){
					$('#total_td').css('color','green');
				}else{
					$('#total_td').css('color','red');
				}
				
			}

			$(document).ready(function(){

				$('#recipe_total_in_fdd').html(total_recipe_wt);
				var array_str = $("#hidden_own_pro_quantity").val();
				if(array_str != '' && array_str != '&&'){
					$("#fdd_row_container").html('');
					array_str = array_str.substring(0, array_str.length - 2);
					
					var res = array_str.split("**");
					var new_html = '';
					for(var i=0;i<res.length;i++){
						
						var res2 = res[i].split("#");
						
						new_html += '<tr id="tr_'+res2[0]+'" rel="'+res2[1]+'" class="product_row"><td>';
						new_html += '<span><img onclick="remove_row('+res2[0]+')" class="my_fdd_img" src="<?php echo base_url().'assets/images/delete_pro.png';?>"></span>&nbsp;&nbsp;<span><input type="text" class="pro_quant text" style="width:6%" value="'+res2[1]+'" onkeyup="calculate_total()" > <strong> <?php echo _('gm');?> </strong></span><span class="prdc_name">'+res2[2]+'</span>';
						new_html += '	<input type="hidden" class="hidden_pro_prefix" value="'+res2[3]+'">'
						new_html += '</td></tr>';
						
					}
					$("#fdd_row_container").append(new_html);
				}
				calculate_total();

				autocomplete_intialize();

// 				$(".prod_name").keyup(function(e){
// 					if(e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40){
// 						show_suggestion();
// 					}
				
//				});
			});

			function sortFunction(a, b) {
			    return b['quantity'] - a['quantity'];
			  
			}
		</script>
		<style>
 			
			.my_fdd_img{
				margin-left: 10px;
    			width: 15px;
			}
			
			a {
			    color: #005da0;
			    cursor: pointer;
			}
			
			.ui-widget-content {
			    font-size: 13px;
			    width: 391px !important;
			}
			.ui-widget-content:hover{
				font-size: 13px;
				font-weight:normal;
			}
			
			.ui-menu .ui-menu-item {
			    margin: 2px 2px 7px 2px !important;
			    padding: 0px !important;
			}
			.ui-autocomplete.ui-front.ui-menu.ui-widget.ui-widget-content {
			    max-height: 180px !important;
			    overflow-x: hidden !important;
			}
			
			.ui-menu-item .ui-state-focus {
			    padding: 2px !important;
			}
			
			.ui-state-hover,
			.ui-widget-content
			.ui-state-hover,
			.ui-widget-header
			.ui-state-hover,
			.ui-state-focus,
			.ui-widget-content
			.ui-state-focus,
			.ui-widget-header
			.ui-state-focus{
				margin: 2px 2px 7px 2px !important;
			    padding: 0px !important;
			    font-size: 13px !important;
			    font-weight: normal !important;
			}
		</style>
	</head>
	<body>
		<div class="clear"></div>
		<p><?php echo _("Add your own fixed products and its quantity, Add quantity of product that is added in 100g of your product, it will effct nutrtion values of your product.");?></p>
		<table>
			<thead >
				<tr>
					<td>
						<img id="loding_gif" alt="<?php echo _('loading');?>" src="<?php echo  base_url()."assets/images/loading2.gif"?>" style="display:none;width: 25px; margin: -19px 0px;">
						<input id="search_box" style="width: 60%" class="text prod_name" type="text" placeholder="<?php echo _("Search by product name");?>" name="product_name" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input id="quant" style="width: 10%" class="text quant" type="number" placeholder="<?php echo _("Quantity");?>" name="quantity" title="<?php echo _("quantity in 100g");?>" min="1" max="100" >&nbsp;&nbsp;
						<select id="unit" style="width: 17%; display: inline; padding: 5px;">
							<option value="1"><?php echo _("gm");?></option>
							<option value="1"><?php echo _("ml");?></option>
						</select>
						<img class="my_fdd_img" onclick="add_row()" alt="<?php echo _("add");?>" src="<?php echo base_url().'assets/images/plus_btn.png';?>" >&nbsp;&nbsp;
					</td>
				</tr>
				</thead>
				<tbody id="fdd_row_container">
				<?php if(isset($total_used_pro) && !empty($total_used_pro)){?>
					<?php foreach ($total_used_pro as $used_pro){?>
						<tr id="tr_<?php echo $used_pro['fdd_pro_id'];?>" class="product_row" rel="<?php echo $used_pro['quantity'];?>">
							<td>
								<span><img onclick="remove_row(<?php echo $used_pro['fdd_pro_id'];?>)" class="my_fdd_img" src="<?php echo base_url().'assets/images/delete_pro.png';?>"></span>&nbsp;&nbsp;<span><input type="text" class="pro_quant text" style="width:6%" value="<?php echo $used_pro['quantity'];?>" onkeyup="calculate_total()" > <strong> <?php echo _('gm'); ?> </strong></span><span class="prdc_name"><?php echo $used_pro['proname'];?></span>
								<input type="hidden" class="hidden_pro_prefix" value="<?php echo $used_pro['prefix'];?>">
							</td>
						</tr>
					<?php }?>
				<?php }?>
			</tbody>
			<tfoot>
		    <tr>
		      <td style="text-align: center;color:red" id="total_td"><?php echo _("Total");?> <strong id="total_quant">00</strong><strong> / </strong><strong id="recipe_total_in_fdd">00</strong> <strong> <?php echo _("gm");?></strong></td>
		    </tr>
		  </tfoot>
		</table>
		
		<p style="text-align: center;">
			<a onclick="submit_all_data()"><?php echo _('Add these Products');?></a>
		</p>
		
		<input type="hidden" id="hidden_search_box_id" value="">
		
<!-- <div id="choose_one_page" style="display: none">
			<ul>
				<li>&nbsp;</li>
				<li><a href="<?php echo base_url();?>cp/fooddesk/add_new_product"><?php echo _('Add product\'s pductsheets.');?></a></li>
				<li>&nbsp;</li>
				<li><a  href="<?php echo base_url();?>cp/cdashboard/add_empty_ingredient_product"><?php echo _('Add a product without productsheet.');?></a></li>
			</ul>
		</div>  -->		
	</body>
</html>