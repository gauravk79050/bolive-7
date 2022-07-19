<?php
$larr = localeconv();
$search = array(
		$larr['decimal_point'],
		$larr['mon_decimal_point'],
		$larr['thousands_sep'],
		$larr['mon_thousands_sep'],
		$larr['currency_symbol'],
		$larr['int_curr_symbol']
);
$replace = array('.', '.', '', '', '', '');
?>
<html>
	<head>
		<script type="text/javascript">
			var status_arr 	= [];
			var counter 	= 0 ;
			$( document ).find( '.mark_fav' ).each( function(){
				status_arr[ counter ] = {  'prod_id': $( this ).attr( 'data-prod_id' ),'is_favourate':$( this ).attr( 'data-status' ) };
				counter++;
			});

			var sel_lang = "<?php echo get_lang($_COOKIE['locale']); ?>";
			$("#semi_p_search_box").select2({
				 placeholder: "<?php echo _('Select Semi-Product');?>",
				 allowClear: true
			});
			var total_recipe_wt = parseFloat($('#recipe_weight').val());
			var wt_set = 1;
			if(isNaN(total_recipe_wt)){
				total_recipe_wt = 0;
				wt_set = 0;
			}else{
				total_recipe_wt = total_recipe_wt*1000;
			}

			var availableTags = new Array();
			var select_prdct = 0;
			var xhr;

			var availableTags2 = new Array();
			var availableTags3 = new Array();
			var availableTags4 = new Array();

			var select_prdct2 = 0;
			var select_prdct_semi = 0;

			$(document).ready(function(){
				var semi_contents = new Array();
				var semi_contents1 = new Array();
				var semi_contents2 = new Array();
				$('#recipe_total_in_fdd').html(total_recipe_wt);

				var array_str = $("#hidden_fdds_quantity").val();
				var array_str2 = $("#hidden_own_pro_quantity").val();
				if(array_str == '' && array_str2 == ''){
				}else{
					$("#fdd_row_container").html('');
				}

				if(array_str != '' && array_str != '&&'){
					array_str = array_str.substring(0, array_str.length - 2);

					var res = array_str.split("**");
					var new_html = '';
					for(var i=0;i<res.length;i++){

						var res2 = res[i].split("#");
						if(res2[5] == 0){
							new_html += '<tr id="tr_'+res2[0]+'" rel="'+res2[1]+'" class="product_row"><td colspan="2">';
							if( status_arr.length > 0 ){
								$.each( status_arr, function( key,value ) {
									if( value[ 'prod_id' ] == res2[0] ){
										new_html += '<input type="hidden" class="product_status" data-prod_id="'+value[ 'prod_id' ]+'" data-product_status="'+value[ 'is_favourate' ]+'" >';
									}
								});
							}
							new_html += '	<span><img onclick="remove_row('+res2[0]+')" class="my_fdd_img" src="<?php echo base_url().'assets/images/delete_pro.png';?>"></span>&nbsp;&nbsp;<span><input type="text" class="pro_quant text" style="width:6%" value="'+res2[1]+'" onkeyup="calculate_total()" > <strong> '+res2[4]+' </strong></span><span class="prdc_name">'+stripslashes(res2[2])+'</span>';
							new_html += '	<input type="hidden" class="hidden_pro_prefix" value="'+res2[3]+'">';
							new_html += '	<input type="hidden" class="hidden_semi_pro_id" value="'+res2[5]+'">';
							new_html += '</td></tr>';
						}else{
							semi_contents1.push(res2);
						}
					}

					$("#fdd_row_container").append(new_html);
				}
				if(array_str2 != '' && array_str2 != '&&'){

					array_str2 = array_str2.substring(0, array_str2.length - 2);

					var res2 = array_str2.split("**");
					var new_html2 = '';
					for(var i=0;i<res2.length;i++){

						var res22 = res2[i].split("#");
						if(res22[5] == 0){
							var pro_s=res22[2].split(" -- ");
							if(pro_s[1] != "")
							{
								var pro_fir=pro_s[0]+" -- "+pro_s[1];
							}
							else
							{
								var pro_fir=pro_s[0];
							}
							new_html2 += '<tr id="tr_'+res22[0]+'" rel="'+res22[1]+'" class="product_row2"><td colspan="2">';
							new_html2 += '<span><img onclick="remove_row('+res22[0]+')" class="my_fdd_img" src="<?php echo base_url().'assets/images/delete_pro.png';?>"></span>&nbsp;&nbsp;<span><input type="text" class="pro_quant2 text" style="width:6%" value="'+res22[1]+'" onkeyup="calculate_total()" > <strong> '+res22[4]+' </strong></span><span class="prdc_name2">'+stripslashes(pro_fir)+'</span>';
							new_html2 += '	<input type="hidden" class="hidden_pro_prefix2" value="'+res22[3]+'">';
							new_html2 += '	<input type="hidden" class="hidden_semi_pro_id" value="'+res22[5]+'">';
							new_html2 += '</td></tr>';
						}else{
							semi_contents2.push(res22);
						}

					}

					$("#fdd_row_container").append(new_html2);
				}

				var semi_ids = new Array();
				for(var x=0;x<semi_contents1.length;x++){
					if($.inArray(semi_contents1[x][5] ,semi_ids ) < 0){
						semi_ids.push(semi_contents1[x][5]);
					}
				}
				for(var x=0;x<semi_contents2.length;x++){
					if($.inArray(semi_contents2[x][5] ,semi_ids ) < 0){
						semi_ids.push(semi_contents2[x][5]);
					}
				}

				if(semi_ids.length > 0){
					jQuery.ajax({
						url: base_url+'cp/fooddesk/get_semi_name',
						type: 'post',
						async: false,
						dataType:'json',
						data:{'semi_ids':semi_ids},
						success: function(response){

							for(var y=0;y<response.length;y++){

								var new_htmls = '';
								new_htmls += '<tr id="tr_'+response[y].id+'" rel="" class="semi_product_row"><td colspan="2">';
								new_htmls += '   <span><img onclick="remove_semi_prods('+response[y].id+')" class="my_fdd_img" src="'+base_url+'assets/images/delete_pro.png"></span>&nbsp;&nbsp;<span><span class="semi_prdc_name"><strong>'+stripslashes(response[y].proname)+'</strong></span></span>';
								new_htmls += '	<input type="hidden" class="hidden_pro_prefix_semi" value="">';
								new_htmls += '	<input type="hidden" class="hidden_semi_pro_id" value="0">';
								new_htmls += '</td></tr>';

								$("#fdd_row_container").append(new_htmls);

								for(var x=0;x<semi_contents1.length;x++){
									if(semi_contents1[x][5] ==  response[y]['id']){

										var new_html = '';
 										new_html += '<tr id="tr_'+semi_contents1[x][0]+'" rel="'+semi_contents1[x][1]+'" class="product_row"><td colspan="2" style="background:pink">';
 										if( status_arr.length > 0 ){
 											$.each( status_arr, function( key,value ) {
 												if( value[ 'prod_id' ] == semi_contents1[x][0] ){
 													new_html += '<input type="hidden" class="product_status" data-prod_id="'+value[ 'prod_id' ]+'" data-product_status="'+value[ 'is_favourate' ]+'" >';
 												}
 											});
 										}
										new_html += '   <span><img style="margin-left:32px" onclick="remove_row('+semi_contents1[x][0]+')" class="my_fdd_img" src="'+base_url+'assets/images/delete_pro.png"></span>&nbsp;&nbsp;<span><input type="text" class="pro_quant text" style="width:6%" value="'+semi_contents1[x][1]+'" onkeyup="calculate_total()" disabled ><strong> '+semi_contents1[x][4]+' </strong></span><span class="prdc_name">'+stripslashes(semi_contents1[x][2])+'</span>';
										new_html += '	<input type="hidden" class="hidden_pro_prefix" value="'+semi_contents1[x][3]+'">';
										new_html += '	<input type="hidden" class="hidden_semi_pro_id" value="'+semi_contents1[x][5]+'">';
										new_html += '</td></tr>';

										$("#fdd_row_container").append(new_html);
									}

								}

								for(var x=0;x<semi_contents2.length;x++){
									if(semi_contents2[x][5] ==  response[y]['id']){
										var semi_contents=semi_contents2[x][2].split(" -- ");
										if(semi_contents[1] == ""){
											var semi_contents_val=semi_contents[0];
										}
										else{
											var semi_contents_val=semi_contents2[x][2];
										}
										var new_html = '';
										new_html += '<tr id="tr_'+semi_contents2[x][0]+'" rel="'+semi_contents2[x][1]+'" class="product_row2" ><td colspan="2" style="background:pink">';
										new_html += '	<span><img style="margin-left:32px" onclick="remove_row('+semi_contents2[x][0]+')" class="my_fdd_img" src="'+base_url+'assets/images/delete_pro.png"></span>&nbsp;&nbsp;<span><input type="text" class="pro_quant2 text" style="width:6%" value="'+semi_contents2[x][1]+'" onkeyup="calculate_total()" disabled > <strong> '+semi_contents2[x][4]+' </strong></span><span class="prdc_name2">'+stripslashes(semi_contents_val)+'</span>';

										new_html += '	<input type="hidden" class="hidden_pro_prefix2" value="'+semi_contents2[x][3]+'">';
										new_html += '	<input type="hidden" class="hidden_semi_pro_id" value="'+semi_contents2[x][5]+'">';
										new_html += '</td></tr>';
										$("#fdd_row_container").append(new_html);
									}
								}

								total_semi_quant = 0;
								unit_semi = 'g';
								$('#fdd_row_container').find('tr').each(function(){
									hidden_semi_pro_id = $(this).children('td').children('.hidden_semi_pro_id').val();
									if(hidden_semi_pro_id == response[y].id){
										total_semi_quant += parseFloat($(this).find('span:eq(1)').children('.text').val());
										unit_semi = $(this).find('span:eq(1)').children('strong').text().trim();
									}
								});

								$('#tr_'+response[y].id).children('td').children('span').children('.semi_prdc_name').append('<span><input type="text" class="pro_quant2s text" style="width:6%" value="'+total_semi_quant.toFixed(2)+'" onkeyup="calculate_semi_total(this,\''+unit_semi+'\')" ><strong> '+unit_semi+' </strong></span>');
							}
						}
					});
				}

				autocomplete_intialize2();
				calculate_total();

				$(".prod_name").keyup(function(e){
					if(e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40){
						var recipe_option = $('#recipe_option').val();
						show_suggestion(sel_lang,recipe_option);
					}
				});
				$(".prod_name2").keyup(function(e){
					if(e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40){
						show_suggestion2();
					}
				});

				$(".supp_name").keyup(function(e){
					if(e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40){
						show_suggestion3();
					}
				});
				$(".real_supp_name").keyup(function(e){
					if(e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40){
						show_suggestion4();
					}
				});

				$('#searchInput').keyup(function()
				{
							searchTable($(this).val());
					}).focus(function () {
						    this.value = "";
						    $(this).css({
						        "color": "black"
						    });
						    $(this).unbind('focus');
					}).css({
						    "color": "#C0C0C0"
				});

				$('#semi_p_search_box').on('change',function(){
					select_prdct_semi = 1;
 					$("#hidden_search_box_id_semi" ).val($('#semi_p_search_box').val());
				});

			});

			function searchTable(inputVal)
			{
				var table = $('#log_table');
				table.find('tr').each(function(index, row)
				{
					var allCells = $(row).find('td');
					if(allCells.length > 0)
					{
						var found = false;
						allCells.each(function(index, td)
						{
							var regExp = new RegExp(inputVal, 'i');
							if(regExp.test($(td).text()))
							{
								found = true;
								return false;
							}
						});
						if(found == true)$(row).show();else $(row).hide();
					}
				});
			}

			function submit_all_data_semi(){
 				var data_array = new Array();
 				var data_array_str ='';
 				$("#fdd_row_container .semi_product_row").each(function(){
 					var row_id = $(this).attr("id");
 					var fdd_id = row_id.substring(3);
 					var pro_quantity = $(this).find(".pro_quant_semi").val();
 					var unit = $(this).find(".pro_quant").next('strong').text().trim();
 					var hidden_pro_prefix = $(this).find(".hidden_pro_prefix_semi").val();
 					var new_data_array= {fdd_pro_id:fdd_id,quantity:pro_quantity,hidden_pro_pre:hidden_pro_prefix};
 					data_array_str += fdd_id+'#'+pro_quantity+'#'+$(this).find(".prdc_name2").html()+'#'+hidden_pro_prefix+'**';
  					data_array.push(new_data_array);

 				});

 				if(data_array.length == 0){

					return false;
				}else{
					data_array = data_array.sort(sortFunction);
				}

 				$.ajax({
 					type: "POST",
 					async:false,
 					url: '<?php echo base_url()."cp/fooddesk/semi_products_assets"?>',
 					data: {data:data_array},
 					dataType:"json",
 					success: function(res){
 	 					var semi_product_total = new Array();

 						for(var i=0;i<res.length;i++){
 							var semi_product = 0;
 							for(var j=0;j<res[i].length;j++){
 								semi_product += parseFloat(res[i][j].quantity);
 							}
 							semi_product_total[i] = semi_product;
 						}


						for(var i=0;i<res.length;i++){
							for(var j=0;j<res[i].length;j++){
								if(res[i][j].is_obs_product == 1){
									add_own_prod_here( res[i][j].fdd_pro_id, (res[i][j].quantity/semi_product_total[i]*data_array[i]['quantity']).toFixed(0),'', data_array[i]['fdd_pro_id']);
								}else{
									add_prod_here( res[i][j].fdd_pro_id, (res[i][j].quantity/semi_product_total[i]*data_array[i]['quantity']).toFixed(0),'', data_array[i]['fdd_pro_id']);
								}
							}
						}
 	 				},
 				});
			}

		</script>
			<style>
				#own_products .ui-autocomplete.ui-front.ui-menu.ui-widget.ui-widget-content {
				    max-height: 180px !important;
				    overflow-x: hidden !important;
				    width: 232px !important;
				}
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
	    			cursor: pointer;
				}

				a {
				    color: #005da0;
				    cursor: pointer;
				}

				.ui-widget-content {
				    font-size: 13px;
				    width: 505px !important;
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
				.own_p{
					float: left;
					width:20%;
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
				.producer_name ul{
					top:92px;
				}
				.supplier_name ul{
					top:92px;
				}
			</style>
		</head>
	<body>
		<div id="popup_content" style="width: 100%;">
			<div class="clear"></div>
			<p><?php echo _("Add Fooddesk products and its quantity, it will effct nutrtion values of your product.");?></p>
			<table>
				<thead >
					<tr>
						<td width="10%" style="vertical-align: top;">
							<select onchange="change_product_db(this.value)" style="padding: 5px;width:115px" id="recipe_option">
							<?php if( isset( $fdd_pro_fav_data ) && !empty( $fdd_pro_fav_data ) ) {?>
								<option value="4"><?php echo _('Favorites');?></option>
							<?php }?>
								<option value="1"><?php echo _('FoodDESK DB');?></option>
								<?php if (!isset($_GET['shared_prod_status'])){?>
									<option value="2"><?php echo _('Semi-Products');?></option>
								<?php }?>
								<option value="3"><?php echo _('New products');?></option>
							</select>
						</td>

						<td id="fdd_products" width="90%" >
							<input id="search_box" style="width: 72%" class="text prod_name" type="text" placeholder="<?php echo _("Search by product's name,producer's name,EAN or Article Number");?>" name="product_name" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<!-- <div style="display: inline-block; width:25px;height:25px">
								<img id="loding_gif" alt="loading" src="<?php echo  base_url()."assets/images/loading2.gif"?>" style="display:none;width: 22px; margin-top: 2px;">
							</div>  -->
							<input id="quant" style="width: 9%" class="text quant" type="number" placeholder="<?php echo _("Quantity");?>" name="quantity" min="1" max="100" >&nbsp;&nbsp;
							<select id="unit" style="width: 7%; display: inline; padding: 5px;">
								<option value="g"><?php echo _("gm");?></option>
								<option value="ml"><?php echo _("ml");?></option>
							</select>
							<img class="my_fdd_img" onclick="add_row()" alt="<?php echo _("add");?>" src="<?php echo base_url().'assets/images/plus_btn.png';?>" >&nbsp;&nbsp;
						</td>
						<!-- <td id="favourate_products">
								<input id="search_box" style="width: 72%" class="text prod_name" type="text" placeholder="<?php echo _("Search by product's name,producer's name,EAN or Article Number");?>" name="product_name" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<input id="quant" style="width: 9%" class="text quant" type="number" placeholder="<?php echo _("Quantity");?>" name="quantity" min="1" max="100" >&nbsp;&nbsp;
							<select id="unit" style="width: 7%; display: inline; padding: 5px;">
								<option value="g"><?php echo _("gm");?></option>
								<option value="ml"><?php echo _("ml");?></option>
							</select>
							<img class="my_fdd_img" onclick="add_row()" alt="<?php echo _("add");?>" src="<?php echo base_url().'assets/images/plus_btn.png';?>" >&nbsp;&nbsp;
						</td> -->
						<td id="semi_products" width="90%" style="display: none">
							<!--<input id="semi_p_search_box" style="width: 72%" class="text semi_prod_name" type="text" placeholder="<?php echo _("Search Semi-Products");?>" name="product_name" >-->
							<select id="semi_p_search_box" class="semi_prod_name" style="padding: 5px; width:72%; float:left;">
							<?php if(!empty($semi_products)){?>

								<option value=""></option>
								<?php foreach ($semi_products as $pro){?>
								<option value="<?php echo $pro['id'];?>"><?php echo stripslashes($pro['proname']);?></option>
								<?php }?>
							<?php }else {?>
								<option value="0"><?php echo _('No Semi-Products');?></option>
							<?php }?>
							</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<input id="quant_semi" style="width: 9%" class="text semi_quant" type="number" placeholder="<?php echo _("Quantity");?>" name="quantity" min="1" max="100" >&nbsp;&nbsp;
							<select id="unit_semi" style="width: 7%; display: inline; padding: 5px;">
								<option value="g"><?php echo _("gm");?></option>
								<option value="ml"><?php echo _("ml");?></option>
							</select>
							<img class="my_fdd_img" onclick="add_row_semi()" alt="<?php echo _("add");?>" src="<?php echo base_url().'assets/images/plus_btn.png';?>" >&nbsp;&nbsp;
							<div class="clear"></div>
						</td>

						<td id="own_products" width="90%" style="display: none">
							<p class="own_p" style="width: 30%">
								<input id="search_box2" class="text prod_name2" type="text" placeholder="<?php echo _("Product not found? Type it here");?>" name="product_name2" onkeyup="make_enable()" >
								<input id="ean_code" class="text ean_code" type="text" placeholder="<?php echo _("EAN-code");?>" name="ean_code" >
							</p>
							<p class="own_p producer_name">
								<input class="text supp_name" type="text" placeholder="<?php echo _("Producers name");?>" name="supplier_name[]" id="supplier_name">
								<input id="art_no_p" class="text art_no" type="text" placeholder="<?php echo _("Article Number Producer");?>" name="art_no_p" >
							</p>
							<p class="own_p" style="width: 3%">
								<?php echo _('or')?>
							</p>
							<p class="own_p supplier_name">
								<input class="text real_supp_name" type="text" placeholder="<?php echo _("Suppliers name");?>" name="real_supplier_name[]" id="real_supplier_name">
								<input id="art_no_s" class="text art_no" type="text" placeholder="<?php echo _("Article Number Supplier");?>" name="art_no_s" >
							</p>
							<input id="quant2" style="width: 9%" class="text quant2" type="number" placeholder="<?php echo _("Quantity");?>" name="quantity2" title="<?php echo _("quantity in 100g");?>" min="1" max="100" >&nbsp;
							<select id="unit2" style="width: 8%; display: inline; padding: 5px;">
								<option value="g"><?php echo _("gm");?></option>
								<option value="ml"><?php echo _("ml");?></option>
							</select>
							<img class="my_fdd_img" onclick="add_row2()" alt="<?php echo _("add");?>" src="<?php echo base_url().'assets/images/plus_btn.png';?>" >&nbsp;
						</td>
					</tr>
					<tr id="alert-msg" style="display:none"><td style="border-color: white;"></td><td></td><td style="border-color: white;"></td></tr>
				</thead>
				<tbody id="fdd_row_container">
				<?php if(isset($total_used_pro) && !empty($total_used_pro)){?>
					<?php foreach ($total_used_pro as $used_pro){?>
					<tr id="tr_<?php echo $used_pro['fdd_pro_id'];?>" class="product_row" rel="<?php echo $used_pro['quantity'];?>">
						<td colspan="2">
							<span><img onclick="remove_row(<?php echo $used_pro['fdd_pro_id'];?>)" class="my_fdd_img" src="<?php echo base_url().'assets/images/delete_pro.png';?>"></span>&nbsp;&nbsp;<span><input type="text" class="pro_quant text" style="width:6%" value="<?php echo str_replace($search, $replace,round($used_pro['quantity'],1));?>" onkeyup="calculate_total()" > <strong> <?php echo $used_pro['unit'];?> </strong></span><span class="prdc_name"><?php echo stripslashes($used_pro['pro_display_name']);?></span>
							<input type="hidden" class="hidden_pro_prefix" value="<?php echo $used_pro['prefix'];?>">
							<input type="hidden" class="hidden_semi_pro_id" value="<?php echo $used_pro['semi_product_id'];?>">
						</td>
					</tr>
					<?php }?>
				<?php }?>

				<?php if(isset($total_used_pro_own) && !empty($total_used_pro_own)){?>
					<?php foreach ($total_used_pro_own as $used_pro){?>
					<tr id="tr_<?php echo $used_pro['fdd_pro_id'];?>" class="product_row2" rel="<?php echo $used_pro['quantity'];?>">
						<td colspan="2">
							<span><img onclick="remove_row(<?php echo $used_pro['fdd_pro_id'];?>)" class="my_fdd_img" src="<?php echo base_url().'assets/images/delete_pro.png';?>"></span>&nbsp;&nbsp;<span><input type="text" class="pro_quant2 text" style="width:6%" value="<?php echo str_replace($search, $replace,round($used_pro['quantity'],1));?>" onkeyup="calculate_total()" > <strong> <?php echo $used_pro['unit']; ?> </strong></span><span class="prdc_name"><?php echo stripslashes($used_pro['proname']);?></span>
							<input type="hidden" class="hidden_pro_prefix2" value="<?php echo $used_pro['prefix'];?>">
							<input type="hidden" class="hidden_semi_pro_id" value="<?php echo $used_pro['semi_product_id'];?>">
						</td>
					</tr>
					<?php }?>
				<?php }?>
				</tbody>
				<tfoot>
			    	<tr>
			      		<td colspan="2" style="text-align: center;color:red" id="total_td"><?php echo _("Total");?> <strong id="total_quant">00</strong> <strong> <?php echo _("gm");?></strong></td>
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
				<button onclick="submit_all_products()"><?php echo _('Add these Products');?></button>
			</p>

			<input type="hidden" id="hidden_search_box_id" value="">
			<input type="hidden" id="hidden_search_box_id2" value="">
			<input type="hidden" id="hidden_search_box_id_semi" value="0">
			<input type="hidden" id="mark_favorite" value="1">

		</div>
		<div id="loading_content" style="width: 100%;display:none">
			<h1><?php echo _("Making Recipe")."...";?></h1>
		</div>
	</body>
</html>