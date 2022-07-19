<html>
	<head>
		<script type="text/javascript">

			var availableTags = [];
			
			function show_prod(s_id){
				$('#load_img').show();
				availableTags = [];
				$.post(
						base_url+'cp/keurslager/getAjaxProducts',
						{
							's_id': s_id 
						},
						function(response){
							var new_html = '';
							if(response.length){
								for(var i = 0; i < response.length; i++){
									availableTags.push({"label":response[i].kp_name,"value":response[i].kp_id});
									new_html += '<p>';
									new_html += '	<span class=\"supp_name\">'+response[i].s_name+'</span>';
									new_html += '	<span class=\"pro_name\"><strong>'+response[i].kp_name+'</strong></span>';
									new_html += '	<span class=\"pro_desc\">'+( (response[i].kp_description.length > 50)?response[i].kp_description.substr(0,50)+'...':response[i].kp_description)+'</span>';
									new_html += '	<span class=\"add_pro\"><a href=\"javascript:;\" class=\"thickbox\" onclick=\"add_prod_here('+response[i].kp_id+');"><?php echo _('Add in this categrory');?></a></span>';
									new_html += '</p>';
								}
								autocomplete_intialize();
							}else{
								new_html += '<p style=\"background-color:#FF7072\">';
								new_html += '	<span class=\"pro_name\"><?php echo _('No product of this Supplier'); ?></span>';
								new_html += '</p>';
							}
							
							$('#product_list').html(new_html);
							$('#load_img').hide();
						},
						'json'
				);
			}

			function autocomplete_intialize(){
				$( "#prod_suggest" ).autocomplete({
					minLength: 0,
					source: availableTags,
					focus: function( event, ui ) {
						$( "#prod_suggest" ).val( ui.item.label );
						return false;
					},
					select: function( event, ui ) {
						add_prod_here(ui.item.value);
						return false;
					}
				})
				.data( "autocomplete" )._renderItem = function( ul, item ) {
					return $( "<li></li>" )
						.data( "item.autocomplete", item )
						.append( "<a>" + item.label + "</a>" )
						.appendTo( ul );
				};
			}
		</script>
		<style>
			.top_wrap{
				margin-bottom:8px;
				text-align:center;
			}
			.top_wrap input,.top_wrap select{
			 	margin: 0;
    		 	width: 250px;
			}
			
			#TB_ajaxContent #product_list {
				margin: 0 auto;
				padding-bottom: 20px;
				width: 685px;
			}
			.pro_name{
				/*display:inline-block;
				min-width:300px;*/
			}
			.add_pro a {
				color: #4682b4 !important;
			}
			
			#product_list p span{
				display: inline-block;
			}
			.supp_name{
				width:90px;
				color:grey;
			}
			.pro_name{
				width:196px;
			}
			.pro_desc{
				width:325px;
				font-size: 11px;
			}
			.add_pro{
				width:65px;
			}
		</style>
	</head>
	<body>
		<div class="top_wrap">
			<select id="suppliers" name="suppliers" onChange="show_prod(this.value)" class="select" style="display: inline-block;">
				<option value="0">--<?php echo _('All');?>--</option>
				<?php if(!empty($suppliers)){?>
				<?php foreach ($suppliers as $supplier){?>
				<option value="<?php echo $supplier->s_id?>"><?php echo $supplier->s_name;?></option>
				<?php }?>
				<?php }?>
			</select>
			<img id="load_img" src="<?php echo base_url();?>assets/cp/images/loading-circle.gif" width="20" style="display:none;vertical-align: top;"/>
		</div>
		
		<div class="top_wrap">
			<input id="prod_suggest" name="prod_suggest" value="" class="text" />
		</div> 
		
		<div id="product_list">
			<?php if(!empty($products)){?>
			<?php foreach ($products as $product){?>
			<p>
				<span class="supp_name"><?php echo $product->s_name; ?></span>
				<span class="pro_name"><strong><?php echo $product->kp_name; ?></strong></span>
				<span class="pro_desc"><?php echo ((strlen($product->kp_description) > 50)?substr($product->kp_description,0,50).'...':$product->kp_description); ?></span>
				<span class="add_pro"><a href="javascript:;" class="thickbox" onClick="add_prod_here(<?php echo $product->kp_id; ?>);"><?php echo _('Add in this categrory');?></a></span>
			</p>
			<script>
				availableTags.push({"label":"<?php echo $product->kp_name;?>","value":"<?php echo $product->kp_id;?>"});
			</script>
			<?php }?>
			<?php }?>
		</div>
		<script>
		$(document).ready(function(){
			autocomplete_intialize();
		});
		</script>
	</body>
</html>