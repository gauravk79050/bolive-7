<html>
	<head>
		<script type="text/javascript">

			
		 	tags = <?php echo json_encode($producers); ?>;
		 
			function add_row(){
				var new_row = "<tr><td><input class=\"text prod_name\" type=\"text\" placeholder=\"<?php echo _('Product name'); ?>\" name=\"product_name[]\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				new_row += "<input class=\"text supp_name\" type=\"text\" placeholder=\"<?php echo _("Supplier name"); ?>\" name=\"supplier_name[]\" onfocus=\"autocompleteInitAgain(this)\">&nbsp;&nbsp;";
				new_row +=  "<div style=\"display: inline-block; vertical-align: middle;\"><img class=\"my_fdd_img\" onclick=\"add_row()\" alt=\"add\" src=\"<?php echo base_url().'assets/images/plus_btn.png';?>\" >&nbsp;&nbsp;&nbsp;";
				new_row += "<img class=\"my_fdd_img\" onclick=\"remove_row()\" alt=\"add\" src=\"<?php echo base_url().'assets/images/minus_btn.png';?>\" ></div></td></tr>";
				
				$("#fdd_row_container").append(new_row);
			}

			function remove_row(){
				var rowCount = $('#fdd_row_container tr').length;
				if(rowCount > 1){
					$("#fdd_row_container tr:last").remove();
				}
			}

			function submit_all_data(){
				var data_array = new Array();
				$("#fdd_row_container tr").each(function(){
					var pro_name = $(this).find(".prod_name").val();
					var sup_name = $(this).find(".supp_name").val();
					var new_data_array= {product_name:pro_name,supplier_name:sup_name}
					data_array.push(new_data_array);
				});
				 $('#TB_window').fadeOut();
				 parent.tb_remove();

				 $.post('<?php echo base_url().'cp/fooddesk/save_pending_products';?>',
						 {'data_array':data_array},
						 function(data){
							alert(data);
						 }
				)
			}

			$(document).ready(function(){
				
				autocompleteInit();
			})
			
			function autocompleteInit(){
				var availableTags = new Array();
				for(var i=0;i<tags.length;i++){
					availableTag = new Array();
					availableTag['value'] = i;
					availableTag['label'] = tags[i];
					availableTags.push(availableTag);
				}
				
				$( "#supplier_name" ).autocomplete({
					minLength: 0,
					source: availableTags,
					focus: function( event, ui ) {
						$( "#supplier_name" ).val( ui.item.label );
						return false;
					},
					select: function( event, ui ) {
						$( "#supplier_name" ).val( ui.item.label );
						return false;
					}
				})
			}

			function autocompleteInitAgain(obj){
				var availableTags = new Array();
				for(var i=0;i<tags.length;i++){
					availableTag = new Array();
					availableTag['value'] = i;
					availableTag['value'] = tags[i];
					availableTags.push(availableTag);
				}
				
				$(obj).autocomplete({
					minLength: 0,
					source: availableTags,
					focus: function( event, ui ) {
						$(obj).val( ui.item.label );
						return false;
					},
					select: function( event, ui ) {
						$(obj).val( ui.item.label );
						return false;
					}
				})

			}
		</script>
		<style>
			input.text, textarea {
			    background: none repeat scroll 0 0 #fcfcfc;
			    border: 1px solid #ccc;
			    color: #333;
			    display: inline;
			    margin: 0 0 5px;
			    padding: 5px;
			    width: 40%;
			}
			.my_fdd_img{
				width:17px;
			}
		</style>
	</head>
	<body>
		<p><?php echo _("Add product here product's name and supplier from the products you don't have productsheet from. We will then ask the supplier for these productsheets and then update your records.");?></p>
		<table>
			<tbody id="fdd_row_container">
				<tr>
					<td>
						<input class="text prod_name" type="text" placeholder="<?php echo _("Product name");?>" name="product_name[]">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input class="text supp_name" type="text" placeholder="<?php echo _("Suppliers name");?>" name="supplier_name[]" id="supplier_name">&nbsp;&nbsp;
						<div style="display: inline-block; vertical-align: middle;">
							<img class="my_fdd_img" onclick="add_row()" alt="add" src="<?php echo base_url().'assets/images/plus_btn.png';?>" >&nbsp;&nbsp;
							<img class="my_fdd_img" onclick="remove_row()" alt="add" src="<?php echo base_url().'assets/images/minus_btn.png';?>" >
						</div>
					</td>
				</tr>
			</tbody>
		</table>
		<p style="text-align: right">
			<button style="margin-right: 18px; width: 100px;" onclick="submit_all_data()"><?php echo_("Submit");?></button>
		</p>
	</body>
</html>