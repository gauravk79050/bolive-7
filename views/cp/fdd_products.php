<html>
	<head>
		<script type="text/javascript">
			var availableTags = new Array();
			var availableTags2 = new Array();
			var select_prdct = 0;
			var select_prdct2 = 0;
			var xhr;
			var tagsp = <?php echo json_encode($producers); ?>;
			var tagss = <?php echo json_encode($suppliers); ?>;

			<?php  $i1 = 0; ?>
			<?php if(!empty($own_products)){ ?>
				<?php foreach($own_products as $pro){ ?>
					availableTags2[<?php echo $i1;?>] = {'value':'<?php echo $pro['id'];?>','label':"<?php echo $pro['proname'];?>"};
					<?php $i1++; ?>
				<?php } ?>
			<?php } ?>
			
			autocompleteInitp();
			autocompleteInits();
			autocomplete_intialize2();
			
			function show_suggestion(){
				availableTags = new Array();
				
				var search_str = $('#search_box').val();

				$('#search_box2').val(search_str);
				if(search_str.length > 1){
					if(xhr && xhr.readystate != 4){
			            xhr.abort();
			        }
					xhr = $.ajax({type:"POST",
							url: base_url+'cp/fooddesk/get_serched_AjaxProducts',
							data: {
								'search_str': search_str,
								'direct_add':1 
								},
							success: function(result_array){
								var arr=JSON.parse(result_array);
								for(var i=0;i<arr.length;i++){
									var new_label = '';
									
									new_label += '<strong>';
									
									if(arr[i]['p_name_dch'] != ''){
										new_label += stripslashes(arr[i]['p_name_dch']);
									}else if(arr[i]['p_name_fr'] != ''){
										new_label += stripslashes(arr[i]['p_name_fr']);
									}else{
										new_label += arr[i]['p_name'];
									}
									new_label += '</strong>';
									new_label += '<span>';
									new_label += '--'+stripslashes(arr[i]['s_name']);

									if(arr[i]['barcode'] != null && arr[i]['barcode'] != ''){
										new_label += '-- EAN: '+arr[i]['barcode'];
									}else {
										new_label += '-- EAN:-- ';
									}

									if(arr[i]['plu'] != null && arr[i]['plu'] != 0){
										new_label += '-- Article nbr: '+arr[i]['plu'];
									}else{
										new_label += '-- Article nbr:-- ';
									}
									if(arr[i]['product_type'] != null && arr[i]['product_type'] != 0){
										new_label += '--GS1 ';
									}
									new_label += '</span>';
									var short_arr =  { value: arr[i]['p_id'], label: new_label };
									
									availableTags.push(short_arr);					
								}
								$('#loding_gif').hide();
								autocomplete_intialize();
							},
					});
				} else{
					autocomplete_intialize();
				}
			}

			$(document).ready(function(){

				$(".prod_name").keyup(function(e){
					if(e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40){
						show_suggestion();
					}
				});
				$('#art_no_p').on('focusout',function(){
					var art_no_p = $( "#art_no_p" ).val();
					if(art_no_p != ''){
						$.ajax({
							type:"POST",
							url:base_url+'cp/fooddesk/get_art_num_suggestion',
							data:{'art_no_p': art_no_p,'art_type': 'producer'},
							dataType: "json",
							success:function(result_data){
								if(result_data.trim() != ''){
									var p_id = result_data.p_id;
									var pro_name = stripslashes(result_data.p_name_dch);
									var supp_name = stripslashes(result_data.s_name);
									var barcode,plu; 
									if(result_data.barcode != null && result_data.barcode != ''){
										barcode = result_data.barcode;
									}else {
										barcode = '--';
									}
									if(result_data.plu != null && result_data.plu != ''){
										plu = result_data.plu;
									}else{
										plu = '--';
									}
									
									$('#TB_ajaxContent').find('#alert-msg').show();
									$('#TB_ajaxContent').find('#alert-msg td:eq(1)').html('<?php echo _('Do you mean');?>:<br/><m style="color:grey">-'+pro_name+' ('+art_no_p+') <?php echo _('from')?> '+supp_name+'?</m><a onclick="select_suggest('+p_id+',\''+pro_name+'\',\''+supp_name+'\',\''+barcode+'\',\''+plu+'\')">yes</a>');
								}
								else{
									$('#TB_ajaxContent').find('#alert-msg').hide();
								}
							},
							async: false
						});
					}
				});
				
				$('#art_no_s').on('focusout',function(){
					var art_no_s = $( "#art_no_s" ).val();
					if(art_no_s != ''){
						$.ajax({
							type:"POST",
							url:base_url+'cp/fooddesk/get_art_num_suggestion',
							data:{'art_no_s': art_no_s,'art_type': 'supplier'},
							dataType: "json",
							success:function(result_data){
								if(result_data.trim() != ''){
									var p_id = result_data.p_id;
									var pro_name = stripslashes(result_data.p_name_dch);
									var supp_name = stripslashes(result_data.s_name);
									var barcode,plu; 
									if(result_data.barcode != null && result_data.barcode != ''){
										barcode = result_data.barcode;
									}else {
										barcode = '--';
									}
									if(result_data.plu != null && result_data.plu != ''){
										plu = result_data.plu;
									}else{
										plu = '--';
									}
									
									$('#TB_ajaxContent').find('#alert-msg').show();
									$('#TB_ajaxContent').find('#alert-msg td:eq(1)').html('<?php echo _('Do you mean');?>:<br/><m style="color:grey">-'+pro_name+' ('+art_no_s+') <?php echo _('from')?> '+supp_name+'?</m><a onclick="select_suggest('+p_id+',\''+pro_name+'\',\''+supp_name+'\',\''+barcode+'\',\''+plu+'\')">yes</a>');
								}
								else{
									$('#TB_ajaxContent').find('#alert-msg').hide();
								}
							},
							async: false
						});
					}
				});
			});
			function select_suggest(p_id,pro_name,supp_name,barcode,plu){
				$('#TB_ajaxContent').find('#alert-msg').hide();
				$('#search_box2').val('');
				make_enable();
				$("#TB_ajaxContent table thead tr td:eq(0) select").val('1');
				change_product(1);
				
				var new_label = '';
				
				new_label += pro_name;
				
				new_label += '--'+supp_name;

				if(barcode != '--'){
					new_label += '--EAN: '+barcode;
				}else {
					new_label += '--EAN:-- ';
				}

				if(plu != '--'){
					new_label += '--Article nbr: '+plu;
				}else{
					new_label += '--Article nbr:-- ';
				}
				
				$( "#search_box" ).val(new_label);
				$( "#hidden_search_box_id").val(p_id);

				$('#select_opt').val('1');
				select_prdct = 1;
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
						.append(  item.label  )
						.appendTo( ul );
				};
				$('#search_box').autocomplete("search");
			}

			if ($.ui){
				$.extend($.ui.autocomplete,
				{
					filter: function(results, term)
					{
						if($(':focus').attr('id') == 'search_box'){
							var all_terms = split( term );
							// remove the current input
							last_item = all_terms.pop();
							if(last_item == ''){
								last_item = all_terms.pop();
							}
							var matcher = new RegExp( $.ui.autocomplete.escapeRegex( last_item ), "i" );
							return $.grep( availableTags, function( value ) {
								return matcher.test( value.label || value.value || value );
							});
						}
						else{
							var matcher = new RegExp( $.ui.autocomplete.escapeRegex( term ), "i" );
							return $.grep( results, function( value ) {
								return matcher.test( value.label || value.value || value );
							});
						}
					}
				});
			}
			
			function split( val ) {
				return val.split( / \s*/ );
			}
			
			function extractLast( term ) {
				return split( term ).pop();
			}

			function autocomplete_intialize2(){
				$( "#search_box2" ).autocomplete({
					minLength: 0,
					appendTo: '#new_fixed_product',
					source: availableTags2,
					focus: function( event, ui ) {
						//$( "#search_box2" ).val( ui.item.label);
						return false;
					},
					select: function( event, ui ) {
						$( "#search_box2" ).val( ui.item.label );
						select_prdct2 = 1;
						
						$.post(base_url+'cp/fooddesk/update_supplier_name',
							{'pro_id': ui.item.value},
							function(result_data){
								$pro_sup = result_data.split('/');
								$( "#supplier_name" ).val(stripslashes($pro_sup[0]));
								$( "#real_supplier_name" ).val(stripslashes($pro_sup[1]));
								$( "#art_no_p" ).val($pro_sup[2]);
								$( "#art_no_s" ).val($pro_sup[3]);
							});
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

			function make_enable(){
				$( "#supplier_name" ).val('');
				$( "#real_supplier_name" ).val('');
				$( "#art_no_p" ).val('');
				$( "#art_no_s" ).val('');
				select_prdct2 = 0;
			}
			
			function add_direct_pro(){
				var p_id = $( "#hidden_search_box_id").val();
				var pro_id = parseInt(p_id);
				if(pro_id != -1){
					add_prod_here(pro_id);
				}else{
					var prodct = $( "#search_box2" ).val();
					
					if(prodct != ''){
						var supplier_name = $( "#supplier_name" ).val();
						var real_supplier_name = $( "#real_supplier_name" ).val();
						var art_no_p = $( "#art_no_p" ).val();
						var art_no_s = $( "#art_no_s" ).val();
						if((supplier_name != '') || (real_supplier_name != '') || (select_prdct2 == 1)){
							if((supplier_name != '') && ((art_no_p == '') || (art_no_p == '<?php echo _('Article Number Producer')?>')) && (select_prdct2 != 1)){
								$('#TB_ajaxContent').find('#alert-msg').show();
								$('#TB_ajaxContent').find('#alert-msg td:eq(1)').html('<?php echo _('Please fill in the article number of producer so the producer knows exactly which product you need for this recipe')?><br/><?php echo _("If you can\'t find this article number then type 000");?><br/><m style="color:grey"><?php echo _("Note: If you don\'t know the article number then it will take more time to fix this product.");?></m>');
							}
							else if((real_supplier_name != '') && ((art_no_s == '') || (art_no_s == '<?php echo _('Article Number Supplier')?>')) && ((art_no_p == '') || (art_no_p == '<?php echo _('Article Number Producer')?>'))  && (select_prdct2 != 1)){
								$('#TB_ajaxContent').find('#alert-msg').show();
								$('#TB_ajaxContent').find('#alert-msg td:eq(1)').html('<?php echo _('Please fill in the article number of supplier so the supplier knows exactly which product you need for this recipe')?><br/><?php echo _("If you can\'t find this article number then type 000");?><br/><m style="color:grey"><?php echo _("Note: If you don\'t know the article number then it will take more time to fix this product.");?></m>');
							}
							else{
								var cat_id = $('#categories_id').val();
								var thic_cat_id = jQuery('#thic_categories_id').val();
								if(cat_id != "-1")
								{
									cat_id=cat_id;
								}
								if(thic_cat_id != -1)
								{
									cat_id=thic_cat_id;
								}
								if(cat_id == "-1" && thic_cat_id == "-1"){
									alert("<?php echo _('Please select any category first');?>");
									//self.parent.tb_remove();
									jQuery('#thic_categories_id').focus();
								}else{
									var subcat_id = jQuery('#subcategories_id').val();
									var thic_subcat_id = jQuery('#thic_subcategories_id').val();
									if(subcat_id != "-1")
									{
										subcat_id=subcat_id;
									}
									if(thic_subcat_id != "-1")
									{
										subcat_id=thic_subcat_id;
									}
									$.post(
										base_url+'cp/fooddesk/add_new_fixed_product_comp',
										{'cat_id':cat_id,'subcat_id':subcat_id,'pro_name': prodct, 'supp_name' : supplier_name, 'real_supp_name' : real_supplier_name,'art_no_p':art_no_p,'art_no_s':art_no_s},
										function(response){
											if(response){
												if(response.trim() == 'Already exists'){
													$('#TB_ajaxContent').find('#alert-msg').show();
													$('#TB_ajaxContent').find('#alert-msg td:eq(1)').html("<?php echo _('This product name already exists'); ?>");
												}
												else{
													window.location = base_url+'cp/cdashboard/products_addedit/product_id/'+response;
												}
											}else{
												$('#TB_ajaxContent').find('#alert-msg').show();
												$('#TB_ajaxContent').find('#alert-msg td:eq(1)').html("<?php echo _('This product cannot be added into current category. please try again later');?>");
											}
										}
									);
								}
							}
						}else{
							$('#TB_ajaxContent').find('#alert-msg').show();
							$('#TB_ajaxContent').find('#alert-msg td:eq(1)').html("<?php echo _('Plaese give a producer or supplier name')?>");
						}
					}else{
						$('#TB_ajaxContent').find('#alert-msg').show();
						$('#TB_ajaxContent').find('#alert-msg td:eq(1)').html("<?php echo _('Please give a name of product')?>");
					}
				}
			}

			function change_product(val){
				if(val == 1){
					$('#fdd_product').show();
					$('#new_fixed_product').hide();
					$('#TB_ajaxContent').find('#alert-msg').hide();
				}else if(val == 2){
					$('#fdd_product').hide();
					$('#new_fixed_product').show();
					$( "#search_box" ).val('');
					$( "#hidden_search_box_id").val('-1');
					$('#TB_ajaxContent').find('#alert-msg').hide();
				}
			}

			function autocompleteInitp(){
				var availableTagsp = new Array();
				for(var i=0;i<tagsp.length;i++){
					var availableTagp = new Array();
					availableTagp['value'] = i;
					availableTagp['label'] = stripslashes(tagsp[i]);
					availableTagsp.push(availableTagp);
				}
				
				$( "#supplier_name" ).autocomplete({
					minLength: 0,
					appendTo: '#new_fixed_product',
					source: availableTagsp,
					focus: function( event, ui ) {
						$( "#supplier_name" ).val( ui.item.label );
						return false;
					},
					select: function( event, ui ) {
						$( "#supplier_name" ).val( ui.item.label );
						return false;
					}
				});
			}

			function autocompleteInits(){
				var availableTagss = new Array();
				for(var i=0;i<tagss.length;i++){
					var availableTags = new Array();
					availableTags['value'] = i;
					availableTags['label'] = stripslashes(tagss[i]);
					availableTagss.push(availableTags);
				}
				
				$( "#real_supplier_name" ).autocomplete({
					minLength: 0,
					appendTo: '#new_fixed_product',
					source: availableTagss,
					focus: function( event, ui ) {
						$( "#real_supplier_name" ).val( ui.item.label );
						return false;
					},
					select: function( event, ui ) {
						$( "#real_supplier_name" ).val( ui.item.label );
						return false;
					}
				});
			}

			$('.text').click(function(){
				$( ".text" ).attr('readonly',false);
				select_prdct2 = 0;
			});

			function get_subcategory(cat_id){
				jQuery.post("<?php echo base_url(); ?>cp/fooddesk/products",
					{'id':cat_id},
	    			function(data){
						var sub_arr = '';
						$("#thic_subcategories_id").html("");
						var sub_array = JSON.parse(data);
						sub_arr+="<option value=-1>-- <?php echo _('Select Subcategory')?> --</option>";
						for(var i=0;i<sub_array.length;i++)
						{
							sub_arr+='<option value="'+sub_array[i].id+'">'+sub_array[i].subname+'</option>';
						}
						$("#thic_subcategories_id").append(sub_arr);
					}
		  	 	);
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
			.ui-autocomplete {
			    font-size: 13px;
			    height: 400px !important;
			    width: 524px !important;
			    z-index: 500 !important;
			}
			.ui-widget-content:hover{
				font-size: 13px;
				font-weight:normal;
			}
			button {
			    border: 1px solid #ccccff;
			    margin: 0 0 10px;
			    padding: 4px;
			    cursor: pointer;
			}
			.ui-menu .ui-menu-item {
			    margin: 2px 2px 7px 2px !important;
			    padding: 0px !important;
			}
			.ui-autocomplete.ui-front.ui-menu.ui-widget.ui-widget-content {
			    max-height: 180px !important;
			    overflow-x: hidden !important;
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
			#new_fixed_product .ui-autocomplete.ui-front.ui-menu.ui-widget.ui-widget-content {
			    max-height: 180px !important;
			    overflow-x: hidden !important;
			    width: 232px !important;
			}
			.own_p{
				float: left;
				width:28%;
				padding:0px!important;
			}
			.own_p .text{
				width: 95% !important;
			}
			#alert-msg > td:nth-child(2){
				text-align:center;
				background-color:#EEEEEE;
				color:#FF0000;
				border-radius: 10px;
			}
			.thic_c{
				padding: 5px;
				min-width: 180px;
				margin-left: auto;
				margin-right: auto;
			}
		</style>
	</head>
	<body>
		<div class="clear"></div>
		<table>
			<thead >
					<tr>
						<td colspan="4">
							<select onchange="get_subcategory(this.value);" class="select thic_c" type="select" id="thic_categories_id" name="thic_categories_id">
								<option value="-1">-- <?php echo _('Select Category'); ?> --</option>
		                       	<?php foreach($category_data as $category):?>
							       <option value="<?php echo $category->id?>"><?php echo $category->name?></option>
								<?php endforeach;?>
							</select>
							
						</td>
						</tr>
						<tr>
						<td colspan="4">
						<select class="select thic_c" type="select" id="thic_subcategories_id" name="thic_subcategories_id">
			   						<option value=-1>-- <?php echo _('Select Subcategory')?> --</option>;
	                      	</select>
	                      	</td>
					</tr>
				<tr>
					<td width="10%" style="vertical-align: top;">
						<select onchange="change_product(this.value)" style="padding: 5px;width:115px">
							<option value="1"><?php echo _('FoodDESK DB');?></option>
							<option value="2"><?php echo _('New products');?></option>
						</select>
					</td>
					<td id="fdd_product">
						<div style="float:left">
							<img id="loding_gif" alt="<?php echo _("loading");?>" src="<?php echo  base_url()."assets/images/loading2.gif"?>" style="display:none;width: 22px; margin-top: 2px;">
						</div>
						<input id="search_box" style="width: 95%;" class="text prod_name" type="text" placeholder="<?php echo _("Search by product'name,producer'name,EAN or Article Number");?>" name="product_name" >&nbsp;
					</td>
					
					<td id="new_fixed_product" style="display: none">
						<p class="own_p" style="width: 40%">
							<input id="search_box2" class="text prod_name2" type="text" placeholder="<?php echo _("Not Found! Add it here");?>" name="product_name2" onkeyup="make_enable()" >&nbsp;
						</p>
						<p class="own_p">
							<input class="text supp_name" type="text" placeholder="<?php echo _("Producers name");?>" name="supplier_name[]" id="supplier_name">
							<input id="art_no_p" class="text art_no" type="text" placeholder="<?php echo _("Article Number Producer");?>" name="art_no_p" >
						</p>
						<p class="own_p" style="width: 3%">
							<?php echo _('or')?>
						</p>
						<p class="own_p">
							<input class="text real_supp_name" type="text" placeholder="<?php echo _("Suppliers name");?>" name="real_supplier_name[]" id="real_supplier_name">
							<input id="art_no_s" class="text art_no" type="text" placeholder="<?php echo _("Article Number Supplier");?>" name="art_no_s" >
						</p>
					</td>
					<td style="width:10px">
						<button class="text" onclick="add_direct_pro()" ><?php echo _('Add');?></button>
					</td>
				</tr>
				<tr id="alert-msg" style="display:none"><td style="border-color: white;"></td><td></td><td style="border-color: white;"></td></tr>
			</thead>
			<tbody>
				<tr>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			</tbody>
		</table>
		<input type="hidden" id="hidden_search_box_id" value="-1">
		<!-- <div id="choose_one_page" style="display: none">
			<ul>
				<li>&nbsp;</li>
				<li><a href="<?php echo base_url();?>cp/fooddesk/add_new_product"><?php echo _('Add product\'s pductsheets.');?></a></li>
				<li>&nbsp;</li>
				<li><a  href="<?php echo base_url();?>cp/cdashboard/add_empty_ingredient_product"><?php echo _('Add a product without productsheet.');?></a></li>
			</ul>
		</div> -->		
	</body>
</html>