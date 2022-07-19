<html>
	<head>
	
		<script type="text/javascript">
			var total_recipe_wt = parseInt($('#total_recipe_wt').html());
			var availableTags = new Array();
			var select_prdct = 0;
			var xhr;
			
			function add_row(){

				var prodct = $( "#search_box" ).val();
				var quant = $( "#quant" ).val();
				var unit = $( "#unit" ).val();
				var quantity = quant*unit;
				var fdd_pro_id = $( "#hidden_search_box_id" ).val();
				
				
				var new_html = '';
				new_html += '<tr id="tr_'+fdd_pro_id+'" rel="'+quantity+'" class="product_row"><td>';
				new_html += '   <span><img onclick="remove_row('+fdd_pro_id+')" class="my_fdd_img" src="<?php echo base_url().'assets/images/delete_pro.png';?>"></span>&nbsp;&nbsp;<span><input type="text" class="pro_quant text" style="width:6%" value="'+quantity+'" onkeyup="calculate_total()" > <strong> <?php echo _('gm');?> </strong></span><span class="prdc_name">'+prodct+'</span>';
				new_html += '	<input type="hidden" class="hidden_pro_prefix" value="">'
				new_html += '</td></tr>';
				if(select_prdct){
					if(quantity > 0 ){
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
					alert("<?php echo _("No product added"); ?>");
					return false;
				}else{
					data_array = data_array.sort(function(a,b) { return b['quantity'] - a['quantity'];});
				}

				remove_all_ingredients();
				
 				if(total_quant <= total_recipe_wt){
 					$('#kp_ing').html('');

 					$("#hidden_fdds_quantity").val(data_array_str);
					$.post("<?php echo base_url().'cp/fooddesk/update_fdd_pro_quant'; ?>",
 							{'update_array':data_array},
 							function(res_data){
 								var arr1 = JSON.parse(res_data);

 								$("#nutri_values").show();
 								$("#e_val_1").html(((arr1['e_val_1']*100)/total_recipe_wt).toFixed(0));
 								$("#e_val_2").html(((arr1['e_val_2']*100)/total_recipe_wt).toFixed(0));
 								$("#proteins").html(((arr1['protiens']*100)/total_recipe_wt).toFixed(1));
 								$("#carbo").html(((arr1['carbo']*100)/total_recipe_wt).toFixed(1));
 								$("#sugar").html(((arr1['sugar']*100)/total_recipe_wt).toFixed(1));
 								$("#poly").html(((arr1['poly']*100)/total_recipe_wt).toFixed(1));
 								$("#farina").html(((arr1['farina']*100)/total_recipe_wt).toFixed(1));
 								$("#fats").html(((arr1['fats']*100)/total_recipe_wt).toFixed(1));
 								$("#sat_fats").html(((arr1['sat_fats']*100)/total_recipe_wt).toFixed(1));
 								$("#single_fats").html(((arr1['single_fats']*100)/total_recipe_wt).toFixed(1));
 								$("#multi_fats").html(((arr1['multi_fats']*100)/total_recipe_wt).toFixed(1));
								
 	 						});
 					for(var i=0;i<data_array.length;i++){
 						add_prod_here(data_array[i]['fdd_pro_id'],data_array[i]['quantity'], data_array[i]['hidden_pro_pre']);
 					}
 					alert("<?php echo _('Quantity of added FoodDESK products saved successfully. Please add related ingrediets, allergence and traces.');?>");
 					calculate_fdd_total();
 				}else{
					alert("<?php echo _('Total quantity of all products must not be more than recipe weight');?>");
 				}
				
// 				 $('#TB_window').fadeOut();
// 				 parent.tb_remove();
 				//remove_all_ingredients();
 				hide_repeated();
 				add_product_prefixes();
			}

			function show_suggestion(){
				availableTags = new Array();
				
				var search_str = $('#search_box').val();


				
				if(search_str.length > 1){
					$('#loding_gif').show();


					if(xhr && xhr.readystate != 4){
			            xhr.abort();
			        }
			        
					xhr = $.ajax({type:"POST",
							url: base_url+'cp/fooddesk/get_serched_AjaxProducts',
							data: {
								'search_str': search_str,
								'direct_add':0 
								},
							success: function(result_array){
								var arr=JSON.parse(result_array);
								for(var i=0;i<arr.length;i++){
									var new_label = '';
									
									new_label += '<strong>';
									if(arr[i]['p_name_dch'] != ''){
										new_label += arr[i]['p_name_dch'];
									}else if(arr[i]['p_name_fr'] != ''){
										new_label += arr[i]['p_name_fr'];
									}else{
										new_label += arr[i]['p_name'];
									}
									new_label += '</strong>';;
									new_label += '<span>';
									new_label += '--'+arr[i]['s_name'];

									if(arr[i]['barcode'] != null && arr[i]['barcode'] != ''){
										new_label += '--EAN: '+arr[i]['barcode'];
									}else {
										new_label += '--EAN:-- ';
									}

									if(arr[i]['plu'] != null && arr[i]['plu'] != ''){
										new_label += '--PLU: '+arr[i]['plu'];
									}else{
										new_label += '--PLU:-- ';
									}
									new_label += '</span>';
									var short_arr =  { value: arr[i]['p_id'], label: new_label };
									
									availableTags.push(short_arr);
									
								}
								$('#loding_gif').hide();
								autocomplete_intialize();
							},
							//async:false
					});
					//autocomplete_intialize();

				} else{
					autocomplete_intialize();
				}
				

				

			}


			function autocomplete_intialize(){
				$( "#search_box" ).autocomplete({
					minLength: 0,
					source: availableTags,
					focus: function( event, ui ) {
						//$( "#search_box" ).val( $(ui.item.label).text() );
						return false;
					},
					select: function( event, ui ) {
						$( "#search_box" ).val( $(ui.item.label).text() );
						$( "#hidden_search_box_id").val( ui.item.value );
						select_prdct = 1;
						return false;
					},
				})
				.data( "ui-autocomplete" )._renderItem = function( ul, item ) {
					return $( "<li></li>" )
						.data( "item.autocomplete", item )
						.append( item.label )
						.appendTo( ul );
				};

				$('#search_box').autocomplete("search");
			}

// 			function remove_all_ingredients(){
// 				$('.select2-search-choice').remove();
// 				$('#kp_ing').html('');
// 				$('#kp_allergence').html('');
// 				$('#kp_traces').html('');
				
// 				alert(hidden_prod_id);
// 			}

			function calculate_total(){

				var own_total = $("#hidden_own_total").val();
				var total = 0;
				total += parseInt(own_total);
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
				
				var array_str = $("#hidden_fdds_quantity").val();
				if(array_str != '' && array_str != '&&'){
					$("#fdd_row_container").html('');
					array_str = array_str.substring(0, array_str.length - 2);
					
					var res = array_str.split("**");
					var new_html = '';
					for(var i=0;i<res.length;i++){
						
						var res2 = res[i].split("#");
						
						new_html += '<tr id="tr_'+res2[0]+'" rel="'+res2[1]+'" class="product_row"><td>';
						new_html += '	<span><img onclick="remove_row('+res2[0]+')" class="my_fdd_img" src="<?php echo base_url().'assets/images/delete_pro.png';?>"></span>&nbsp;&nbsp;<span><input type="text" class="pro_quant text" style="width:6%" value="'+res2[1]+'" onkeyup="calculate_total()" > <strong> <?php echo _('gm');?> </strong></span><span class="prdc_name">'+res2[2]+'</span>';
						new_html += '	<input type="hidden" class="hidden_pro_prefix" value="'+res2[3]+'">'
						new_html += '</td></tr>';
						
					}
					$("#fdd_row_container").append(new_html);
				}
				calculate_total();



				$(".prod_name").keyup(function(e){
					if(e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40){
						show_suggestion();
					}
				
				});

// 				 $('#log_table').dataTable({
// 						"bPaginate": false,
// 						 "sScrollY": "166px",
// 						 "bInfo": false,
// 				    	"aaSorting": [[0,'asc']],
// 				    	"oLanguage": {
// 				    		"sProcessing": "Bezig...",
// 				    	    "sLengthMenu": "_MENU_ resultaten weergeven",
// 				    	    "sZeroRecords": "Geen resultaten gevonden",
// 				    	    "sInfoEmpty": "Geen resultaten om weer te geven",
// 				    	    "sInfoFiltered": " (gefilterd uit _MAX_ resultaten)",
// 				    	    "sInfoPostFix": "",
// 				    	    "sSearch": "Zoeken:",
// 				    	    "sEmptyTable": "Geen resultaten aanwezig in de tabel",
// 				    	    "sInfoThousands": ".",
// 				    	    "sLoadingRecords": "Een moment geduld aub - bezig met laden...",
// 				        }
// 				    });


				$("#searchInput").keyup(function () {
				    //split the current value of searchInput
				    var data = this.value.split(" ");
				    //create a jquery object of the rows
				    var jo = $("#fbody").find("tr");
				    if (this.value == "") {
				        jo.show();
				        return;
				    }
				    //hide all the rows
				    jo.hide();

				    //Recusively filter the jquery object to get results.
				    jo.filter(function (i, v) {
				        var $t = $(this);
				        for (var d = 0; d < data.length; ++d) {
				            if ($t.is(":contains('" + data[d] + "')")) {
				                return true;
				            }
				        }
				        return false;
				    })
				    //show the rows that match.
				    .show();
				}).focus(function () {
				    this.value = "";
				    $(this).css({
				        "color": "black"
				    });
				    $(this).unbind('focus');
				}).css({
				    "color": "#C0C0C0"
				});

			});


			function show_wights(){
				$('#wieght_info').toggle();	
			}

// 			function sortFunction(a, b) {
// 			   	return (b['quantity'] - a['quantity']);
// 			}
		</script>
		<style>
 			
 			
/*  			#log_table { */
/* 			    display: block; */
/* 			    height: 200px !important; */
/* 			    overflow-y: scroll; */
/* 			    width: 100% !important; */
/* 			} */
			
			#log_table_filter label input{
				background: none repeat scroll 0 0 #fcfcfc;
			    border: 1px solid #ccc;
			    color: #333;
			    display: inline;
			    margin: 0 0 5px;
			    padding: 5px;
			    width: 75%;
			}
			
			
 			.dataTable{ 
				width:100% !important; 
 			} 
 			
 			.dataTable th, .dataTable td{ 
				width:33% !important; 
 			} 
 			
 			.dataTables_scrollHeadInner{
 			width:600px !important;
 			}
 			#TB_ajaxContent{
			    width: 900px !important;
			    max-height: 450px !important;
 			}
 			
 			#TB_window {
 			 	margin-left: -465px !important;
    			margin-top: -260px !important;
			    width: 930px !important;
			   
			}
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
			    width: 554px !important;
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
		<p><?php echo _("Add Fooddesk products and its quantity, it will effct nutrtion values of your product.");?></p>
		<table>
			<thead >
				<tr>
					<td>
						<div style="float: left; width:25px;height:25px">
							<img id="loding_gif" alt="loading" src="<?php echo  base_url()."assets/images/loading2.gif"?>" style="display:none;width: 22px; margin-top: 2px;">
						</div>
						<input id="search_box" style="width: 65%" class="text prod_name" type="text" placeholder="<?php echo _("Search by product's name,producer's name,EAN or PLU Number");?>" name="product_name" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<!-- <input id="quant" style="width: 10%" class="text quant" type="number" placeholder="<?php echo _("quantity");?>" name="quantity" title="<?php echo _("quantity in 100g");?>" min="1" max="100" >&nbsp;&nbsp; -->
						<input id="quant" style="width: 10%" class="text quant" type="number" placeholder="<?php echo _("quantity");?>" name="quantity" min="1" max="100" >&nbsp;&nbsp;
						<select id="unit" style="width: 10%; display: inline; padding: 5px;">
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
								<span><img onclick="remove_row(<?php echo $used_pro['fdd_pro_id'];?>)" class="my_fdd_img" src="<?php echo base_url().'assets/images/delete_pro.png';?>"></span>&nbsp;&nbsp;<span><input type="text" class="pro_quant text" style="width:6%" value="<?php echo $used_pro['quantity'];?>" onkeyup="calculate_total()" > <strong> <?php echo _("gm");?> </strong></span><span class="prdc_name"><?php echo $used_pro['pro_display_name'];?></span>
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
			<a href="javascript:;" onclick="show_wights()" class="thickbox" ><?php echo _("Don't know the weight! This can help");?></a>
		</p>
		
		
		<div id="wieght_info" style="display: none;width: 600px;margin:0 auto;">
			<input id="searchInput"  type="text" class="text" placeholder="<?php echo _("Search");?>">
			<table>
				<thead>
					<tr>
						<th width="41%"><?php echo _('product');?></th>
						<th width="27%"><?php echo _('capacity'); ?></th>
						<th width="25%"><?php echo _('weight'); ?></th>
					</tr>
				</thead>
			</table>
			
			<div style="height:200px;overflow-y:scroll">
			<table id="log_table" style="width:100%">
				
				<tbody id="fbody">
					<?php foreach ($weight_list as $weights){?>
					<tr>
						<td width="45%"><?php echo $weights['nutrient'.$fdd_lang];?></td>
						<td width="30%"><?php echo $weights['capacity'.$fdd_lang];?></td>
						<td width="25%"><?php echo $weights['weight'];?></td>
					</tr>
					<?php }?>
				</tbody>
			</table>
			</div>
		</div>
		
		<p style="text-align: center;">
			<button onclick="submit_all_data()"><?php echo _('Add these Products');?></button>
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