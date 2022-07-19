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
$sel_lang = get_lang($_COOKIE['locale']);
?>
<script type="text/javascript">
var drag_me = "<?php echo _("Add this");?>";
var drag_me_all = "<?php echo _("Add all of this product");?>";
var required_txt = "<?php echo _('Required'); ?>";
var shortname_sugg = "<?php echo _('Product Short name suggestion');?>";
var suggestion_send = "<?php echo _('Product short name send to Fooddesk admin, it will be automatic updated in your product after approval');?>";
var suggestion_not_send = "<?php echo _('could not be submitted product shortname');?>";

var plz_give_a_name_of_product_msg = "<?php echo _('Please give a name to product');?>";
var fill_producer_art_msg = "<?php echo _('Please fill in the article number of producer so the producer knows exactly which product you need for this recipe')?>";
var fill_supplier_art_msg = "<?php echo _('Please fill in the article number of supplier so the supplier knows exactly which product you need for this recipe')?>";
var fill_000_msg = "<?php echo _('If you can\'t find this article number then type 000');?>";
var note_msg = "<?php echo _('Note: If you don\'t know the article number then it will take more time to fix this product.');?>";
var do_u_mean_txt = "<?php echo _('Do you mean');?>";
var from_txt = "<?php echo _('from')?>";
var art_nbr_p = "<?php echo _('Article Number Producer')?>";
var art_nbr_s = "<?php echo _('Article Number Supplier')?>";
var producer_txt = "<?php echo _('Producer')?>";
var supplier_txt = "<?php echo _('Supplier')?>";
var no_product_added = "<?php echo _("No products added"); ?>";
var cant_be_zero_any_msg = "<?php echo _("No quantity field can be 0 or empty!"); ?>";

var gm_str = "<?php echo _('gm');?>";
var qunat_greater_than_zero = "<?php echo _('Quantity of product must be greater than 0 gm.');?>";
var select_from_list = "<?php echo _('Please Add a product from suggestion first.');?>";

var prefix_text = "<?php echo _('prefix'); ?>";
var plz_select_cat_msg = "<?php echo _('Please select a category');?>";
var ing_datas = new Array();
var allg_datas = new Array();
var traces_datas = new Array();
var fdd_url = "<?php echo $this->config->item('fdd_url'); ?>";
var not_more_than_100 = "<?php echo _("Total quatity of All foodDESK product shuold not be more than 100gm");?>";
var plz_select_producer_msg = "<?php echo _('Please select a producer or supplier');?>";
var cant_add_as_semi = "<?php echo _('Can not be added as semi product');?>";

var move_to_sp_txt = '<?php echo _('Are you sure want to move this product to semi products');?>?';
var move_to_esp_txt = '<?php echo _('Are you sure want to move this product to extra semi products');?>?';
var move_success = "<?php echo _('Successfully Moved');?>";
var move_fail = "<?php echo _('Can not be Moved');?>";
var checked = "<?php echo _("checked");?>";
var mark_prod = "<?php echo _("Mark the product as semi-product");?>";
</script>

<script src="<?php echo base_url();?>assets/kcp/js/select2/select2.min.js?version=<?php echo version;?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/kcp/js/select2/select2.css?version=<?php echo version;?>" media="screen">

<link rel="stylesheet" href="<?php echo base_url();?>assets/css/ui/jquery.ui.all.css?version=<?php echo version;?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/kcp/js/select2/select2.css" media="screen">

<link rel="stylesheet" href="<?php echo base_url();?>assets/css/ui/jquery.ui.all.css">
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.core.js?version=<?php echo version;?>"></script>
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.widget.js?version=<?php echo version;?>"></script>
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.sortable.js?version=<?php echo version;?>"></script>
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.position.js?version=<?php echo version;?>"></script>
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.draggable.js?version=<?php echo version;?>"></script>
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.droppable.js?version=<?php echo version;?>"></script>

<script src="<?php echo base_url(); ?>assets/plugins/jquery-ui-1.11.2.custom/jquery-ui.min.js?version=<?php echo version;?>" type="text/javascript"></script>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/jquery-ui-1.11.2.custom/jquery-ui.theme.min.css?version=<?php echo version;?>">

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/jquery-ui-1.11.2.custom/jquery-ui.theme.min.css">

<?php if( $this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium'){?>
	<script src="<?php echo base_url()?>assets/cp/new_js/kcp_prod_new_custom.js?version=<?php echo version;?>"></script>
<?php }?>

<style>
	.ui-autocomplete {
		max-height: 100px;
		overflow-y: auto;
		z-index:500;
	}

	.ing_info{
		background: none repeat scroll 0 0 #eaea00;
	    border: 2px solid #8c8c01;
	    margin: 14px auto;
	    padding: 10px;
	    text-align: center;
	    width: 81%;
	}

	.select2-container-multi .select2-choices{
    	min-height: 110px;
	}

	.select2-display-none {
	    display: none !important;
	}
	.textlabel{
		width: 200px;
	}

	.semi_pro td:nth-child(1),
	.semi_pro td:nth-child(2),
	.semi_pro td:nth-child(3),
	.semi_pro td:nth-child(4),
	.semi_pro td:nth-child(5),
	.semi_pro td:nth-child(6),
	.semi_pro td:nth-child(7){
		background:#FFDAB9;
	}

	.ing_pro_name_row img {
    	cursor: pointer;
	}

	.tiny_txt{
		font-size: 10px;
	}
	.ui-state-error { border: 1px solid red !important; }
	.usr_text{
		width:30%;
		float:left;
		margin-left:0px;
		text-align:right;
		clear: both;
	}
	.usr_val{
		width:60%;
		float:left;
		margin-left:20px;
	}
	.ui-state-hover, .ui-widget-content .ui-state-hover, .ui-widget-header .ui-state-hover, .ui-state-focus, .ui-widget-content .ui-state-focus, .ui-widget-header .ui-state-focus {
   border: 1px solid #fff;
   background: transparent!important;
   font-weight: bold;
   color: #eb8f00!important ;
}
</style>

<!-- -------------------------------------- CROPPING IMAGE ------------------------------------------------------ -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/cp/new_css/jquery.Jcrop.css?version=<?php echo version;?>" type="text/css" />
<style type="text/css">
.preview_title{
    display: block;
    font-size: 20px;
    font-weight: bold;
    margin: 10px auto;
    text-align: center;
    text-decoration: underline;
}

.jcrop-holder #preview-pane {
  display: block;
  position: absolute;
  /*z-index: 2000;*/
  top: -2px;
  right: -260px;
  padding: 6px;
  border: 1px rgba(0,0,0,.4) solid;
  background-color: white;

  -webkit-border-radius: 6px;
  -moz-border-radius: 6px;
  border-radius: 6px;

  -webkit-box-shadow: 1px 1px 5px 2px rgba(0, 0, 0, 0.2);
  -moz-box-shadow: 1px 1px 5px 2px rgba(0, 0, 0, 0.2);
  box-shadow: 1px 1px 5px 2px rgba(0, 0, 0, 0.2);
}

/* The Javascript code will set the aspect ratio of the crop
   area based on the size of the thumbnail preview,
   specified here */
#preview-pane .preview-container {
  width: 220px;
  height: 209px;
  overflow: hidden;
}
/*#TB_window{
	top: 80% !important;
	z-index: 999 !important;
}*/
#crop_button{
	background-color:#007a96;
    padding:12px 26px;
    color:#fff;
    font-size:14px;
    border-radius:2px;
    cursor:pointer;
    display:inline-block;
    line-height:1;
    border: none;
}
.crop_div{
	margin-top: 30px;
	text-align: center;
}

#GroupsTable input.medium, #GroupsPersonTable input.medium, #WGroupsTable input.medium{
	width: 100%;
}

.fc-first th {
    background: none repeat scroll 0 0 black !important;
    border: medium none !important;
}
</style>
<script src="<?php echo base_url();?>assets/cp/new_js/jquery.Jcrop.js?version=<?php echo version;?>"></script>
<script type="text/javascript">
	var jcrop_api,boundx,boundy,xsize,ysize,$preview,$pcnt,$pimg;
	var do_diasble = 0;

	$(document).ready(function(){
		//hide_repeated();
		$(".thickboxed").click(function(){
			tb_show("<?php echo _("Upload Image");?>", "<?php echo base_url(); ?>cp/image_upload/ajax_img_upload?height=400&width=600", "true");
		});
	});

  	function updateCoords(c){
    	$('#x').val(c.x);
	    $('#y').val(c.y);
	    $('#w').val(c.w);
	    $('#h').val(c.h);
  	};

  	function checkCoords(){
	    if (parseInt($('#w').val())) return true;
	    alert("<?php echo _('Please select a crop region then press submit.');?>");
	    return false;
  	};

  	function updatePreview(c){
    	if (parseInt(c.w) > 0){
      		var rx = xsize / c.w;
      		var ry = ysize / c.h;

      		$pimg.css({
        		width: Math.round(rx * boundx) + 'px',
        		height: Math.round(ry * boundy) + 'px',
        		marginLeft: '-' + Math.round(rx * c.x) + 'px',
        		marginTop: '-' + Math.round(ry * c.y) + 'px'
      		});
    	}
  	};

	function crop(){
		$("#uploaded_image").append('<img src="<?php echo base_url();?>assets/cp/images/loader.gif" alt="<?php echo _("Cropping");?>...."/>');
		$.ajax({
			url : base_url+'cp/image_upload/crop_image',
			data : {'image_name': $("#image_name").val(), 'x': $("#x").val(), 'y': $("#y").val(), 'w': $("#w").val(), 'h': $("#h").val()},
			type: 'POST',
			success: function(response){
				//$("#uploaded_image").toggle("slow");
				$("#uploaded_image").html(response);
				$("#uploaded_image").focus();
				//$("#uploaded_image").toggle("slow");
			}
		});
	};
	jQuery(document).ready(function(){
		var myEvent = window.attachEvent || window.addEventListener;
		var chkevent = window.attachEvent ? 'onbeforeunload' : 'beforeunload'; /// make IE7, IE8 compitable

        myEvent(chkevent, function(e) { // For >=IE7, Chrome, Firefox
        	if (needToConfirm){
            var confirmationMessage = '<?php echo _('If you leave before saving, your changes will be lost.');?>';  // a space
                (e || window.event).returnValue = confirmationMessage;
                return confirmationMessage;
        	}
        });
		$("#product_info_add,#product_info_update,#product_info_savenext").click(function(){
			var rotated_image = $('.rotated_image_hid').val();
			var current_prod_img = $('#current_prod_image').val();
			if(product_info_validate()){
				$('#loadingmessage').show();
				if($("#product_info_add_update").val() == 'add')
					$("#product_info_add").attr("disabled","disabled");

				if($("#product_info_add_update").val() == 'update')
					$("#product_info_update").attr("disabled","disabled");

				$("#product_info_savenext").attr("disabled","disabled");

				if($("#image_name").length != 0)
					var img_name = $("#image_name").val();
				else
					var img_name = 0;
				$.post(
					"<?php echo base_url()?>cp/products/products_addedit",
					{"categories_id":$("#categories_id").val(),"subcategories_id":$("#subcategories_id").val(),"pro_art_num":$("#pro_art_num").val(),"proname":$("#proname").val(),"prodescription":$("#prodescription").val(),"producer":$("#producer").val(),"new_producer":$("#new_pr").val(),"supplier":$("#supplier").val(),"new_supplier":$("#new_sr").val(),"product_type":$("#fd_product_type").val(),"image_name":img_name,"ajax_add_update":$("#product_info_add_update").val(),"action":"product_info","prod_id":$(".prod_id").val(),"action_val":$(this).val(),"rotated_image":rotated_image,"current_prod_img":current_prod_img},
					function(data){
						$('#loadingmessage').hide();

						var infodesk_status = $('#infodesk_status').val();
			        	jQuery.post(
			        			base_url+"cp/shop_all/update_allergenkart_files/",
			        		    {'action':'category_json'},
			        		    function(data){},
			        		    'json'
			        	);
			        	if(infodesk_status == 1){
			        		$.post(
			        			base_url+"cp/shop_all/update_desk_files/"+infodesk_status,
			        			{'action':'category_json'},
			        			function(data){},
			        			'json'
			        	);

						$("#fd_product_type").prop("disabled", true);

						if($("#product_info_add_update").val() == 'update'){
							$("#product_info_update").removeAttr("disabled");
							alert("<?php echo _('Product Information Updated')?>");
						}
						else{
							$("#product_info_add").removeAttr("disabled");
							alert("<?php echo _('Product Information Added')?>");
						}
						$("#product_info_savenext").removeAttr("disabled");

						var response = JSON.parse(data);
						if(response['is_next'] == 'true'){
							window.location = "<?php echo base_url()?>cp/products/products_addedit/product_id/"+response['id'];
						}
						else{
							if($(".prod_id").length == 0){
								var html = '<input type="hidden" class="prod_id" name="prod_id" value="'+response['id']+'">';
								$("#content-container").append(html);
							}
						}
			        }
				}
			);
			}
		});

		$("#recipe_add,#recipe_update,#recipe_savenext").click(function(){
			if(product_info_validate()){
				$('#loadingmessage').show();
				needToConfirm = false;
				if($("#recipe_add_update").val() == 'add')
					$("#recipe_add").attr("disabled","disabled");

				if($("#recipe_add_update").val() == 'update')
					$("#recipe_update").attr("disabled","disabled");

				$("#recipe_savenext").attr("disabled","disabled");

				$.post(
					"<?php echo base_url()?>cp/products/products_addedit",
					{"recipe_method_txt":$("#recipe_method_txt").val(),"recipe_weight":$("#recipe_weight").val(),"ingredients":$("#ingredients").val(),"allergence":$("#allergence").val(),"traces_of":$("#traces_of").val(),"hidden_fdds_quantity":$("#hidden_fdds_quantity").val(),"hidden_own_pro_quantity":$("#hidden_own_pro_quantity").val(),"is_custom_semi":$("input[name='is_custom_semi']:checked").val(),"product_type":$("#fd_product_type").val(),"categories_id":$("#categories_id").val(),"subcategories_id":$("#subcategories_id").val(),"proname":$("#proname").val(),"ajax_add_update":$("#recipe_add_update").val(),"action":"recipe","prod_id":$(".prod_id").val(),"action_val":$(this).val()},
					function(data){
						$('#loadingmessage').hide();
						var infodesk_status = $('#infodesk_status').val();
			        	jQuery.post(
			        			base_url+"cp/shop_all/update_allergenkart_files/",
			        		    {'action':'category_json'},
			        		    function(data){},
			        		    'json'
			        	);
			        	if(infodesk_status == 1){
			        		$.post(
			        			base_url+"cp/shop_all/update_desk_files/"+infodesk_status,
			        			{'action':'category_json'},
			        			function(data){},
			        			'json'
			        	);
						$("#fd_product_type").prop("disabled", true);

						if($("#recipe_add_update").val() == 'update'){
							$("#recipe_update").removeAttr("disabled");
							alert("<?php echo _('Recipe Updated')?>");
						}
						else{
							$("#recipe_add").removeAttr("disabled");
							alert("<?php echo _('Recipe Added')?>");
						}
						$("#recipe_savenext").removeAttr("disabled");

						var response = JSON.parse(data);
						if(response['is_next'] == 'true'){
							window.location = "<?php echo base_url()?>cp/products/products_addedit/product_id/"+response['id'];
						}
						else{
							if($(".prod_id").length == 0){
								var html = '<input type="hidden" class="prod_id" name="prod_id" value="'+response['id']+'">';
								$("#content-container").append(html);
							}
						}
					}
					}
				);
			}
		});

		$("#labeler_add,#labeler_update,#labeler_savenext").click(function(){
			if(product_info_validate()){
				$('#loadingmessage').show();

				if($("#labeler_add_update").val() == 'add')
					$("#labeler_add").attr("disabled","disabled");

				if($("#labeler_add_update").val() == 'update')
					$("#labeler_update").attr("disabled","disabled");

				$("#labeler_savenext").attr("disabled","disabled");

				$.post(
					"<?php echo base_url()?>cp/products/products_addedit",
					{"conserve_min":$("#conserve_min").val(),"conserve_max":$("#conserve_max").val(),"prod_date":$("#prod_date").val(),"duedate":$("#duedate").val(),"duedate_type":$("#duedate_type").val(),"weight":$("#weight").val(),"weight_unit":$("#weight_unit").val(),"show_bcode":$("input[name='show_bcode']:checked").val(),"extra_noti":$("#extra_noti").val(),"ajax_add_update":$("#labeler_add_update").val(),"action":"labeler","prod_id":$(".prod_id").val(),"categories_id":$("#categories_id").val(),"subcategories_id":$("#subcategories_id").val(),"proname":$("#proname").val(),"action_val":$(this).val()},
					function(data){
						$('#loadingmessage').hide();

						if($("#labeler_add_update").val() == 'update'){
							$("#labeler_update").removeAttr("disabled");
							alert("<?php echo _('Labeler Updated')?>");
						}
						else{
							$("#labeler_add").removeAttr("disabled");
							alert("<?php echo _('Labeler Added')?>");
						}
						$("#labeler_savenext").removeAttr("disabled");

						var response = JSON.parse(data);
						if(response['is_next'] == 'true'){
							window.location = "<?php echo base_url()?>cp/products/products_addedit/product_id/"+response['id'];
						}
						else{
							if($(".prod_id").length == 0){
								var html = '<input type="hidden" class="prod_id" name="prod_id" value="'+response['id']+'">';
								$("#content-container").append(html);
							}
						}
					}
				);
			}
		});

		/*$('.copy_cboard').on('click',function(){
			if(typeof needToConfirm != 'undefined' && !needToConfirm){
				alert("<?php echo _('Please refresh the page again');?>");
			}
			else{
				var type = $(this).attr('data-type');
				var copied_text = $('#'+type+'copy').val();
				var $temp = $("<input>");
				$("body").append($temp);
				$temp.val(copied_text).select();
				document.execCommand("copy");
				$temp.remove();
			}
		});*/

		$('.copy_cboard').on('click',function(){
			if(typeof needToConfirm != 'undefined' && !needToConfirm)
			{
				alert(refresh);
			}
			else
			{

				var type = $(this).attr('data-type');
				if(type=='ingredients')
				{
					var new_ingre_arr = new Array();
					$('#s2id_ingredients').find('ul.select2-choices li').each(function(index) {
						if($(this).css('display')!='none')
						{
							new_ingre_arr.push($(this).children('div').text());
						}
					});
					var copied_text=new_ingre_arr.join(' ');
				}
				else
				{
					var copied_text = $('#'+type+'copy').val();
				}
				var $temp = $("<input>");
				$("body").append($temp);
				$temp.val(copied_text).select();
				document.execCommand("copy");
				$temp.remove();
			}
		});
	});

   	function product_info_validate(){
      	// checking category
    	if($("#categories_id").val() == "-1"){
        	alert("<?php echo _('Select a categoy.');?>");
        	$('#categories_id').focus();
        	return false;
        }

      	// checking product name
       	if($("#proname").val() == ""){
        	alert("<?php echo _('please give the product name.');?>");
	       	$('#proname').focus();
        	return false;
        }

       	return check_crop();
	}
</script>
<!-- -------------------------------------------------------------------------------------------- -->
<style>
	/*#TB_window{
		margin-top: -270px !important;
	}*/
	.littletext {
		font-size: 10px;
	}
	#TB_ajaxContent{
		max-height: 400px !important;
	}
	select {
	    margin-left: 0px;
	}

	.ing_pro_name_row td {
	    padding: 10px 0;
	}
		.sub_div{
		border-color: #e3e3e3;
		border-style: solid;
		border-width: 1px 0 0;
	}
	.sub__div{
		padding:20px 60px 20px 20px;
		text-align:right;
	}
</style>

<script>

	/* FOR SORTABLE */
	var fixHelper = function(e, ui) {
		ui.children().each(function() {
			$(this).width($(this).width());
		});
		return ui;
	};

  	jQuery(document).ready(function($) {
  		$('.remove_image').click(function(){

  	  		$.post('<?php echo base_url();?>cp/cdashboard/delete_image',{product_id: $(this).attr('rel') },function(response){
  	  	  		if(response.trim() == 'success'){
  	  	  	  		window.location.reload();
  	  	  	  	}else{
  	  	  	  		alert("<?php echo _('Image can not be deleted successfully');?>");
  	  	  	  	}
			});
  		});

		$(".sortable").sortable({
			helper: fixHelper,
			handle: '.handle',
			cursor: "move"
		});

		rename_product_short_name();

		update_fdd_pro_values();
  	});

  	function attach_ingredients(pro_id, all, ingi, traces){
		$.post('<?php echo base_url()?>cp/cdashboard/attach_ingredients',
				{'pro_id':pro_id,'ingredients':ingi,'aller':all,'traces':traces},
				function(data){
					if(data){
							location.reload();
						}
				}
			);
  	}

  	function show_new_producer(obj){
		var pr_val = $(obj).val();
		if(pr_val == -1){
			$('#new_pr').show();
		}else{
			$('#new_pr').hide();
		}
  	 }

  	function show_new_supplier(obj){
		var pr_val = $(obj).val();
		if(pr_val == -1){
			$('#new_sr').show();
		}else{
			$('#new_sr').hide();
		}
  	 }

  	function add_credit(crdt){
		jQuery.post(
				base_url+'cp/fooddesk/send_request_for_credit/'+crdt,
				{},
				function(data){
					alert(data.toSource());
// 					if(data.error){
// 						alert(data.message);
// 					}else{
// 						alert(data.message);
// 					}
				}
			);
	}

  	function change_pro_type(obj){
 		if(parseInt($(obj).val()) == 0 ){
 			$("#pro_supp_tr").hide();
 			$('#recipe_heading_tr').show();
 	 		$('#recipe_weight_tr').show();
 	 		$('#recipe_method_tr').show();
 	 		$('#recipe_contains_tr').show();

			$('#fdd_tools').show();
			$('#producer').val(0);
			$('#producer').attr('disabled','');
			$('#supplier').val(0);
			$('#supplier').attr('disabled','');
			$("#add_single_pro").hide();
			$("#hidden_fdds_quantity").val('');
			$("#hidden_own_pro_quantity").val('');
			$("#ingredients").select2("data", [], true);
			$("#allergence").select2("data", [], true);
			$("#traces_of").select2("data", [], true);
			$("#row_container_fdd").remove();
			$("#search_box_fdd").val("");
 		}else{
 			$("#pro_supp_tr").show();
 	 		$('#recipe_heading_tr').hide();
 	 		$('#recipe_weight_tr').hide();
 	 		$('#recipe_method_tr').hide();
 	 		$('#recipe_contains_tr').hide();
 	 		$('#recipe_method_txt').val('');

 	 		$('#producer').removeAttr('disabled');
 	 		$('#supplier').removeAttr('disabled');
	 		$('.select2-search-choice').remove();
			$("#hidden_fdds_quantity").val('');
			$("#hidden_own_pro_quantity").val('');
			$("#kp_ing").html('');
			$("#kp_allergence").html('');
			$("#kp_traces").html('');
			$("#fdd_total_tr").hide();
			$('#fdd_tools').hide();
			$("#ingredients").select2("data", [], true);
			$("#allergence").select2("data", [], true);
			$("#traces_of").select2("data", [], true);
			$("#add_single_pro").show();
			$("#fdd_product").css("display","");
 		}
	}

</script>
<div id="loadingmessage" style="display:none;margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;">
  <img src="<?php echo base_url(''); ?>assets/cp/images/ajax-loading.gif" style="position: absolute; color: White; top: 50%; left: 45%;"/>
</div>

<!-- MAIN -->
<div id="main">
<div id="main-header">
  <h2>
    <?php if($product_information): echo _('UPDATE PRODUCT');else: echo _('ADD PRODUCT'); endif;?>
  </h2>
  <span class="breadcrumb"><a href="<?php echo base_url()?>cp/"><?php echo _('Home')?></a> &raquo; <a href="<?php echo base_url()?>cp/products"><?php echo _(' Products')?></a> &raquo;
  <?php if($product_information): echo _('update product');else: echo _('add product'); endif;?>
  </span>
</div>

<div id="content">
	<div id="content-container">
		<div class="ing_info">
			<h4><?php echo _("Important Note");?>:</h4><br/>
			<p><?php echo _("Many features on this page are hidden because you are using this only to display ingredients of the products. If you want to see the features that are neccessary to run a webshop please notify us via 0473/250528 or info@onlinebestelsysteem.net");?></p>
		</div>
		<!--<form action="<?php echo base_url();?>cp/cdashboard/products_addedit" enctype="multipart/form-data" method="post" id="frm_products_addedit" name="frm_products_addedit">-->
		<?php if($product_information):?>
			<input type="hidden" value="<?php echo $product_information['0']->id?>" name="prod_id" class="prod_id">
			<input type="hidden" value="<?php echo $product_information['0']->direct_kcp?>" name="direct_kcp">
		<?php endif;?>
		<div class="box">
			<h3> <?php echo _('Product information ')?></h3>
			<div class="table">
					<table border="0">
			       		<tbody>
			       			<tr>
					           	<td class="textlabel"><?php echo _('Article No.')?></td>
					            <td style="padding-right:250px" colspan="2"><input type="text" class="text medium" size="10" id="pro_art_num" name="pro_art_num" <?php if(isset($product_information) && isset($product_information['0']->pro_art_num)): echo 'value="'.$product_information['0']->pro_art_num.'"'; endif;?> style="width:80px;"></td>
					        </tr>
			              <tr>
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
			              <tr>
			                <td class="textlabel"><?php echo _('Select subcategory')?></td>
			                <td style="padding-right:250px" colspan="2">
			                	<select class="select" id="subcategories_id" name="subcategories_id" style="padding:4px">
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
			                <td style="padding-right:250px" colspan="2"><input type="text" class="text medium" size="30" id="proname" name="proname" <?php if($product_information):?>value="<?php echo stripslashes($product_information['0']->proname)?>"<?php endif;?> <?php if( $this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium'){if(isset($product_information)){if(!empty($product_information)){ if($product_information['0']->direct_kcp == 1  && $product_information['0']->parent_proid == 0 ){if(!isset($fixed_pdf)){?>style="background:pink"<?php }}}}}?>></td>
			              </tr>
			             <?php if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium'){?>
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
			              <?php } ?>

			              <tr>
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
			                <td class="textlabel"><?php echo _('Current image')?></td>
			                <td style="padding-right:250px" id="uploaded_image_td">
			                <?php if($product_information['0']->image) { ?>
			                  <img src="<?php echo base_url(''); ?>assets/cp/images/product/<?php echo $product_information['0']->image;?>" alt="<?php echo _('No product image available.Please upload one.')?>" style="height:300px" />
			                  <a href="#" class="remove_image" rel="<?php echo $product_information['0']->id;?>">Remove</a>

			                  <input class="rotated_image_hid" type="hidden" value="">
				              <input type="hidden" id="current_prod_image" value="<?php echo $product_information['0']->image;?>">
			                  <?php } else { ?>
			                  <img src="<?php echo base_url(''); ?>assets/cp/images/product/no_image.jpg" alt="<?php echo _('No product image available.Please upload one.')?>"/>
			                  <?php } ?>
			                </td>
			                <td>
		                		<?php if(!empty($product_information['0']->image)) { ?>
		                		<!-- For not showing the rotate images if uploaded images not there -->
								<a href="javascript:;" class="pro_rotate_img" onClick="srotcw(this)" data-img1="<?php echo $product_information['0']->image;?>" title="<?php echo _('Rotate image Clock-wise')?>">
									<img src="<?php echo base_url();?>/assets/cp/images/cw.png"></a>
								<a href="javascript:;" class="pro_rotate_img" onClick="srotacw(this)" data-img2="<?php echo $product_information['0']->image;?>" title="<?php echo _('Rotate image Anti-clockwise')?>">
									<img src="<?php echo base_url();?>/assets/cp/images/acw.png">
								</a>
								<?php } ?>
			                </td>
			              </tr>
			              <?php endif;?>
					</tbody>
				</table>
			<?php if($product_information):?>
			<div class="sub_div">
			    <div class="sub__div" colspan="2">
			        <input type="button" value="<?php echo _("Update");?>" class="submit" id="product_info_update" name="product_info_update">
			        <input type="hidden" value="add_edit" id="product_info_act" name="product_info_act">
			    	<input type="hidden" value="update" id="product_info_add_update" name="product_info_add_update">
					    	<input type="button" value="<?php echo _('Save & next')?>" class="submit" id="product_info_savenext" name="product_info_savenext">
				</div>
			</div>
		<?php else:?>
			<div class="sub_div">
			    <div class="sub__div" colspan="2">
			        <input type="button" value="<?php echo _('Send')?>" class="submit" id="product_info_add" name="product_info_add">
			      	<input type="hidden" value="add" id="product_info_add_update" name="product_info_add_update">
					      	<input type="button" value="<?php echo _('Save & next')?>" class="submit" id="product_info_savenext" name="product_info_savenext">
			   	</div>
			</div>
		<?php endif;?>
		</div>
		</div>
	    	<div class="boxed">
      			<h3 id="recipe"> <?php echo _('Recipe')?></h3>
      			<div style="padding: 0px; display: block;" class="inside">
        			<div class="table">
        				<table border="0">
			       			<tbody>
			       				<?php if( $this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium'){?>
				              <!--<tr id="recipe_heading_tr" <?php if(isset($product_information) && !empty($product_information) && $product_information[0]->direct_kcp == 1 ){ echo 'style="display: none"'; }?> >
				              	<td class="textlabel" colspan="2" style="font-size: 20px;text-align:center">
				              		<?php //echo _('Your Recipe')?><br/>
				              	</td>
				            </tr>-->
				             	<?php if(!empty($product_ingredients)){?>
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
							                    			var combine_id = "<?php echo $product_ingredient->prefix.'#'.$product_ingredient->ki_name.'#'.$product_ingredient->ki_id.'#'.$product_ingredient->kp_id.'#'.$product_ingredient->is_obs_ing.'#'.$key.'#'.$aller_type.'#'.$allergence.'#'.$sub_allergence.'#'.$new_allergence; ?>";
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
				                		<img src="<?php echo base_url().'assets/cp/images/select_list_h.gif'; ?>" <?php if (isset($product_information) && $product_information[0]->parent_proid == 0){?>  onclick="reset_wt()<?php }?>">
				                	<?php } ?>

				                	&nbsp;&nbsp;&nbsp;
				                	<span><?=_('Important: only with hot dishes, the weight must be weighted when it\'s hot (not cold)'); ?></span>
				                </td>
				            </tr>
				            <?php if (!(!empty($product_information) && $product_information[0]->parent_proid != 0)){?>
							<tr id="recipe_contains_tr" <?php if(isset($product_information) && !empty($product_information) && $product_information[0]->direct_kcp == 1){ echo 'style="display: none"'; }?>>
				                <td class="textlabel">
				                	<?php //echo _('Contains')?> <!-- : -->
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
			              			<table border="0" class="override" id="remove_con">
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
	                                			<?php if($fdd_info['semi_product_id'] == 0){?>
		                                			<tr id="ing_sub_row_<?php echo $fdd_info['fdd_pro_id']; ?>" class="ing_pro_name_row" rel="0">
		                                		<?php }else{?>
		                                			<tr id="ing_sub_row_<?php echo $fdd_info['fdd_pro_id']; ?>" class="ing_pro_name_row semi_pro" rel="<?php echo $fdd_info['semi_product_id']; ?>">
		                                		<?php }?>
		                                			<td width="7%" >
		                                				<!-- <div style="border: 1px solid rgb(204, 204, 204); box-sizing: border-box; padding: 1px 4px 0px;background:#eee;">
		                                				<img onclick="toggle_ing(<?php echo $fdd_info['fdd_pro_id']; ?>)" src="<?php echo base_url();?>assets/images/icon-plus-minus.png" style="width: 22px; display: inline-block; padding-top: 1px;" class="close_img"/>
		                                				<img onclick="toggle_ing(<?php echo $fdd_info['fdd_pro_id']; ?>)" src="<?php echo base_url();?>assets/images/icon-plus-minus1.png" style="width: 22px; display: inline-block; padding-top: 1px;display:none" class="open_img" />

		                                				<strong style="display: inline-block;font-size: 11px;margin-left: 5px;padding-top: 6px;vertical-align: top;" title="<?php echo $fdd_info['p_name_dch']; ?>"> <?php echo $this_pro_name;?></strong>
		                                				</div> -->
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
		                                					<input type="text" style="width:100%" class="text product_name_text" value="<?php echo stripslashes($fdd_info['p_name'.$sel_lang.'']); ?>" disabled>
		                                				<?php } ?>
		                                			</td>
		                                			<td width="3%" ></td>
		                                			<td width="12%">

		                                			<?php
// 		                                			if($fdd_info['quantity'] > 1){
// 														$fdd_quant = round($fdd_info['quantity'],0);
// 													}else{
														$fdd_quant = str_replace($search, $replace,round($fdd_info['quantity'],2));
//													}
		                                			?>
		                                				<input type="text" class="text fdd_product_quants" value="<?php echo $fdd_quant;?>" onkeyup="quant_change(this)" style="width: 60%; padding-bottom: 7px; margin-top: 5px;"><strong> <?php echo $fdd_info['unit'];?> </strong>
		                                				<input type="hidden" class="fdd_product_hidden_id" value="<?php echo $fdd_info['fdd_pro_id'];?>" >
		                                				<?php if ($fdd_info['product_type']){?>
		                                					<input type="hidden" class="ing_pro_name" value="<?php echo $fdd_info['p_name'.$sel_lang.''].'--'.$fdd_info['s_name'].'--EAN:'.$fdd_info['barcode'].'--PLU:'.$fdd_info['plu'].'--GS1';?>" >
		                                				<?php }else{?>
		                                					<input type="hidden" class="ing_pro_name" value="<?php echo $fdd_info['p_name'.$sel_lang.''].'--'.$fdd_info['s_name'].'--EAN:'.$fdd_info['barcode'].'--PLU:'.$fdd_info['plu'];?>" >
		                                				<?php }?>
		                                			</td>
		                                			<td width="4%" style="vertical-align: sub;">
														<img src="<?php echo base_url();?>assets/cp/images/Delete.gif" style="width: 20px;margin-top: 7px;" onclick="remove_this_fdd_pro(<?php echo $fdd_info['fdd_pro_id'];?>)" />
													</td>
													<?php if ($fdd_info['product_type'] == 1){?>
														<td style="color: blue;"><?php echo "GS1";?></td>
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
			                                						<i><span style="color: #777"><?php echo '('.$all_str.')';?></span> <a href="javascript:;" class="tiny_txt" onclick="assign_to_recheck(<?php echo $fdd_info['fdd_pro_id'];?>)"> <?php echo _('Wrong');?></a></i>
			                                				<?php }?>
														</td>
													<?php }else{?>
														<td width="4%" style="vertical-align: middle;">
															<?php if($fdd_info['data_sheet'] != '' && $fdd_info['data_sheet'] != NULL){?>
																<a target="_blank" href="<?php echo $this->config->item('fdd_url').'assets/cp/uploads/'.$fdd_info['data_sheet']; ?>" >
																	<img src="<?php echo base_url();?>assets/images/pdf2.jpeg" style="width: 20px;" />
																</a>
															<?php }else{?>
																<img src="<?php echo base_url();?>assets/images/pdf1.jpeg" style="width: 20px;" />
															<?php }?>
			                                			</td>
			                                			<td width="5%">
															<?php if( in_array( $fdd_info['fdd_pro_id'], $fdd_pro_fav ) ){?>
																	<img src="<?php echo base_url(); ?>/assets/images/greenstar.png" data-status="marked" class="mark_fav prod_status"  data-prod_id="<?php echo $fdd_info[ 'fdd_pro_id' ]; ?>" >
																<?php }else{ ?>
																	<img src="<?php echo base_url(); ?>/assets/images/star.jpg" data-status="unmarked" class="mark_fav prod_status"  data-prod_id="<?php echo $fdd_info[ 'fdd_pro_id' ]; ?>" >
																<?php } ?>
														</td>
			                                			<td width="37%" style="vertical-align: middle;padding-left:5px">
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
			                                				<?php }?>
			                                				<?php if($all_str != ''){ $all_str = implode(', ',array_unique(explode(', ', $all_str)));?>
			                                						<i><span style="color: #777"><?php echo '('.$all_str.')';?></span> <a href="javascript:;" class="tiny_txt" onclick="assign_to_recheck(<?php echo $fdd_info['fdd_pro_id'];?>)"> <?php echo _('Wrong');?></a></i>
			                                				<?php }?>
														</td>
													<?php }?>
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
 															$own_quant1 = round($own_pro_info['quantity'],2);
// 														}
			                                			?>
		                                				<input type="text" class="text own_product_quants" value="<?php echo $own_quant1;?>" onkeyup="quant_change(this)" style="width: 60%; padding-bottom: 7px; margin-top: 5px;"><strong> <?php echo $own_pro_info['unit'];?> </strong>
		                                				<input type="hidden" class="fdd_product_hidden_id" value="<?php echo $own_pro_info['fdd_pro_id'];?>" >
		                                				<input type="hidden" class="ing_pro_name" value="<?php echo $own_pro_info['proname'].' '.'--'.' '.$own_pro_info['s_name'];?>" >
		                                			</td>
		                                			<td width="4%" style="vertical-align: sub;">
														<img src="<?php echo base_url();?>assets/cp/images/Delete.gif" style="width: 20px;margin-top: 7px;" onclick="remove_this_fdd_pro(<?php echo $own_pro_info['fdd_pro_id'];?>)" />
													</td>
													<td width="4%" style="vertical-align: sub;">

		                                			</td>
		                                			<td width="37%" style="vertical-align: sub;">

		                                			</td>
		                                		</tr>
		                    					<?php }?>
	                    					<?php }?>
	                                    </tbody>
	                                    <tfoot>
	                                    <!-- <tr id="fdd_total_tr" style="display:none" ><td colspan="5" style="text-align:right"><strong id="total_fdd_pro_quants_container" ><span>Total </span><span id="total_fdd_pro_quants">00</span><span> / </span><span id="total_recipe_wt"><?php if(isset($product_information) && !empty($product_information)){ echo  $product_information[0]->recipe_weight * 1000; } ?></span><span> gm</span></strong></td></tr>  -->
	                                    </tfoot>
	                                </table>
			              		</div>
			              		<div id="last_td"></div>
			              		<?php if(isset($product_information) && !empty($product_information) && $product_information[0]->direct_kcp == 0 && isset($check_prod_share) && empty($check_prod_share)){?>
			              			<p style="padding-top: 25px;"><b><?php echo _('Mark this product as semi-product')?></b>&nbsp;&nbsp;&nbsp;<input type="checkbox" name="is_custom_semi" id="is_custom_semi" <?php  if(isset($product_information)){if(!empty($product_information)){if($product_information['0']->semi_product){echo 'checked="checked"';}}}?> value="1"></p>
			              			<h1 id="is_semi_alert" style="color:red;"></h1>
			              			<p class="move_to_alert"><a onclick="return move_to('<?php echo $product_information[0]->id; ?>',1)" href="javascript:;"><b><?php echo _('Move to Semi Product');?></b></a></p>
			              			<p class="move_to_alert"><a onclick="return move_to('<?php echo $product_information[0]->id; ?>',2)" href="javascript:;"><b><?php echo _('Move to Extra Semi Product');?></b></a></p>
									<?php }?>
				            	</td>
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
				             	<!--<tr id="add_single_pro" style="<?php if(isset($product_information)){if(!empty($product_information)){ if($product_information['0']->direct_kcp == 1 ){echo 'display:';}else{ echo 'display:none';   } }else{ echo 'display:none';} }else{ echo 'display:none';} ?>">
				             		<td class="textlabel">
				                		<?php echo _('Product')?> :
									</td>
				             		<td>
				             			<span style="float: left; width:25px;height:25px">
											<img id="loding_gifs" alt="loading" src="<?php echo base_url()."assets/images/loading2.gif"?>" style="display:none;width: 22px; margin-top: 2px;">
										</span>
				             			<span id="fdd_product" width="90%" style="<?php if((isset($used_fdd_pro_info)) && (!empty($used_fdd_pro_info))){echo 'display:none';}?>" >
											<input id="search_box_fdd" style="width: 72%" class="text prod_name" type="text" placeholder="<?php echo _("Search by product's name,producer's name,EAN or PLU Number");?>" name="product_name" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<img class="my_fdd_img" onclick="add_pro()" alt="<?php echo _("add");?>" src="<?php echo base_url().'assets/images/plus_btn.png';?>" >&nbsp;&nbsp;
										</span><br/>
										<span id="row_container_fdd">
										<?php if(isset($used_fdd_pro_info)){?>
	                                		<?php foreach ($used_fdd_pro_info as $fdd_info){?>
	                                		<?php $this_pro_name = '';
		                                		if(strlen($fdd_info['p_name'.$sel_lang.'']) > 23){
													$this_pro_name = substr($fdd_info['p_name'.$sel_lang.''], 0,23).'...';
												}else{
													$this_pro_name = $fdd_info['p_name'.$sel_lang.''];
												}
		                                		?>

		                                		<span id="ing_sub_row_<?php echo $fdd_info['fdd_pro_id']; ?>" class="ing_pro_name_row" rel="0">
		                                		<span width="30%">
		                                			<input type="text" class="text product_name_text" value="<?php echo stripslashes($fdd_info['p_name_dch']); ?>" style="width:50%" disabled>
		                                		</span>
		                                		<span width="4%" style="vertical-align: middle;">
													<img src="<?php echo base_url();?>assets/cp/images/Delete.gif" style="width: 20px;" onclick="remove_this_fdd_pro(<?php echo $fdd_info['fdd_pro_id'];?>)" id="delete_pro" />
												</span>
												<span width="4%" style="vertical-align: middle;">
												<?php if($fdd_info['data_sheet'] != '' && $fdd_info['data_sheet'] != NULL){?>
													<a target="_blank" href="<?php echo $this->config->item('fdd_url').'assets/cp/uploads/'.$fdd_info['data_sheet']; ?>" >
														<img src="<?php echo base_url();?>assets/images/pdf2.jpeg" style="width: 20px;" />
													</a>
												<?php }else{?>
													<img src="<?php echo base_url();?>assets/images/pdf1.jpeg" style="width: 20px;" />
												<?php }?>
		                                		</span>
		                                		<span width="37%" style="vertical-align: middle;padding-left:5px">
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
		                                		<?php }?>
		                                		<?php if($all_str != ''){ $all_str = implode(', ',array_unique(explode(', ', $all_str)));?>
		                                				<i><span style="color: #777"><?php echo '('.$all_str.')';?></span> <a href="javascript:;" class="tiny_txt" onclick="assign_to_recheck(<?php echo $fdd_info['fdd_pro_id'];?>)"> <?php echo _('Wrong');?></a></i>
		                                		<?php }?>
												</span>
												</span>
	                                		<?php }}?>
										</span><br/>
										<input type="hidden" id="hidden_search_box_id_fdd" value="">
				             		</td>
				            	</tr>-->
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

			              <!-- ALLERGENCE -->
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
			              		<!-- <input type="button" name="" id="" value="(" onclick="add_symbol('(','allergence');" />
			              		<input type="button" name="" id="" value=")" onclick="add_symbol(')','allergence');" /> -->

			              		<div style="width: 50%;">
			              			<table border="0" class="override" >
	                                	<tbody id="kp_allergence" class="" style="display: none">
	                    					<?php if(!empty($product_allergences)){?>
	                    					<tr><td colspan="3">&nbsp;</td></tr>
	                    					<?php $conuter = 0; ?>
	                    					<?php foreach ($product_allergences as $product_allergence){?>
			                    				<?php if($product_allergence->kp_id){?>
			                    					<?php if($product_allergence->ka_id){?>
			                    					<tr id="allg_<?php echo $product_allergence->kp_id;?>_<?php echo $product_allergence->ka_id;?>">
			                    					<?php }/*else{?>
			                    					<tr><td colspan="3">&nbsp;</td></tr>
			                    					<tr id="pro_a_<?php echo $product_allergence->kp_id;?>">
			                    					<?php }*/?>
														<td width="70%">
															<p class="draggabled">
																<input type="text" name="kp_a_names_prefix[]" class="text short prefix" value="<?php echo $product_allergence->prefix;?>" />
																<input type="text" name="kp_allg_names[]" class="text medium name" value="<?php echo $product_allergence->ka_name;?>" style="width:70%;<?php if(!$product_allergence->ka_id){?>font-weight:bold;<?php }?>" disabled="disabled"  />
																<input type="hidden" class="kp_allg_ids" name="kp_allg_ids[]" value="<?php echo $product_allergence->ka_id; ?>" />
																<input type="hidden" class="kp_allg_pid" name="kp_allg_pid[]" value="<?php echo $product_allergence->kp_id; ?>" />
															</p>
														</td>
														<td width="10%">
															<!-- <img width="18" border="0" onClick="javascript:deleteIngredients(this);" src="<?php echo base_url();?>assets/cp/images/delete.gif" /> -->
														</td>
														<td width="5%" style="text-align:right">
															<!-- <img width="18" border="0" class="handle draggable_allg" src="<?php echo base_url();?>assets/cp/images/move.png" style="cursor: pointer;" title="<?php echo _("Drag me");?>" /> -->
															<?php if($product_allergence->ka_id){?>
																<img width="18" border="0" class="handle" src="<?php echo base_url();?>assets/cp/images/upload.png" style="cursor: pointer;" title="<?php echo _("Drag me");?>" onclick="drop_me('allg_<?php echo $product_allergence->kp_id;?>_<?php echo $product_allergence->ka_id;?>','allergence');" />
															<?php } ?>
														</td>
														<td width="10%">
														</td>
														<td width="5%" style="text-align:right">
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
			                    							var str = "<?php echo '(';?>";
			                    							var combine_id = "<?php echo '#(#0#'.$product_allergence->kp_id.'#'.$product_allergence->ka_id;?>";
			                    							allg_datas.push({id:combine_id,text:stripslashes(str)});
			                    						<?php if(!empty($product_sub_allergences)){?>
					                    					<?php foreach ($product_sub_allergences as $product_sub_allergence){?>
					                    						<?php if(($product_sub_allergence->kp_id == $product_allergence->kp_id) && ($product_allergence->ka_id == $product_sub_allergence->parent_ka_id)){?>
					                    						var str = "<?php echo $product_sub_allergence->sub_ka_name;?>";
					                    						var combine_id = "<?php echo '#'.$product_sub_allergence->sub_ka_name.'#'.$product_sub_allergence->sub_ka_id.'#'.$product_sub_allergence->kp_id.'#'.$product_sub_allergence->parent_ka_id;?>";
					                    						allg_datas.push({id:combine_id,text:stripslashes(str)});
			                    						<?php }}}?>
			                    						var str = "<?php echo ')';?>";
			                    						var combine_id = "<?php echo '#)#0#'.$product_allergence->kp_id.'#'.$product_allergence->ka_id;?>";
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
			              		<!-- <input type="button" name="" id="" value="(" onclick="add_symbol('(','traces_of');" />
			              		<input type="button" name="" id="" value=")" onclick="add_symbol(')','traces_of');" /> -->
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
															<!-- <img width="18" border="0" onClick="javascript:deleteIngredients(this);" src="<?php echo base_url();?>assets/cp/images/delete.gif" /> -->
														</td>
														<td width="5%" style="text-align:right">
															<!-- <img width="18" border="0" class="handle draggable_t" src="<?php echo base_url();?>assets/cp/images/move.png" style="cursor: pointer;" title="<?php echo _("Drag me");?>" /> -->

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
			                    					<?php if(!$product_trace->kp_id){?>
				                    					<?php if($product_trace->kt_name == '('){ ?>
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
			              				<td><?php echo _("energy value(Kcal)");?></td>
			              				<td id="e_val_1"><?php if(isset($nutri_values) && !empty($nutri_values)){ echo defined_money_format($nutri_values['e_val_1'],0); } ?></td>
			              				<td id="e_val_1_x"><?php if(isset($nutri_values) && !empty($nutri_values)){ echo defined_money_format($nutri_values['e_val_1']/100*$recipe_wt,0); } ?></td>
			              			</tr>
			              			<tr>
			              				<td><?php echo _("energy value(KJ)");?></td>
			              				<td id="e_val_2"><?php if(isset($nutri_values) && !empty($nutri_values)){ echo defined_money_format($nutri_values['e_val_2'],0); } ?></td>
			              				<td id="e_val_2_x"><?php if(isset($nutri_values) && !empty($nutri_values)){ echo defined_money_format($nutri_values['e_val_2']/100*$recipe_wt,0); } ?></td>
			              			</tr>
			              			<tr>
			              				<td><?php echo _("proteins(gm)");?></td>
			              				<td id="proteins"><?php if(isset($nutri_values) && !empty($nutri_values)){ echo defined_money_format($nutri_values['protiens'],1); } ?></td>
			              				<td id="proteins_x"><?php if(isset($nutri_values) && !empty($nutri_values)){ echo defined_money_format($nutri_values['protiens']/100*$recipe_wt,1); } ?></td>
			              			</tr>
			              			<tr>
			              				<td>
			              					<p><?php echo _("Carbohydrates (gm)");?></p></br>
			              					<p><?php echo '-'._("Sugar (gm)");?></p></br>
			              					<p><?php echo '-'._("Polyolen (gm)");?></p></br>
			              					<p><?php echo '-'._("Farina (gm)");?></p>
			              				</td>
			              				<td >
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
			              					<p><?php echo '-'._("Saturated Fats (gm)");?></p></br>
			              					<p><?php echo '-'._("Single Unsaturated Fats (gm)");?></p></br>
			              					<p><?php echo '-'._("Multi Unsaturated Fats (gm)");?></p>
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
			              		<td id="fats"><img src="<?php echo base_url();?>assets/images/pdf2.jpeg"><a href="<?php echo  $this->config->item('fdd_url').'assets/cp/uploads/'.$fixed_pdf;?>"><?php echo $fixed_pdf;?></a></td>
			              </tr>
			              <?php }?>
						</tbody>
			    	</table>
        		</div>
        			<?php if($product_information):?>
					<div class="sub_div">
			    		<div class="sub__div" colspan="2">
			        		<input type="button" value="<?php echo _("Update");?>" class="submit" id="recipe_update" name="recipe_update" <?php if (isset($product_information) && !empty($product_information) && $product_information[0]->parent_proid != 0){?>disabled<?php }?>>
			        		<input type="hidden" value="add_edit" id="recipe_act" name="recipe_act">
			    			<input type="hidden" value="update" id="recipe_add_update" name="recipe_add_update">
			    			<input type="button" value="<?php echo _('Save & next')?>" class="submit" id="recipe_savenext" name="recipe_savenext" <?php if (isset($product_information) && !empty($product_information) && $product_information[0]->parent_proid != 0){?>disabled<?php }?>>
						</div>
					</div>
					<?php else:?>
					<div class="sub_div">
			    		<div class="sub__div" colspan="2">
			        		<input type="button" value="<?php echo _('Send')?>" class="submit" id="recipe_add" name="recipe_add" <?php if (isset($product_information) && !empty($product_information) && $product_information[0]->parent_proid != 0){?>disabled<?php }?>>
			      			<input type="hidden" value="add" id="recipe_add_update" name="recipe_add_update">
			      			<input type="button" value="<?php echo _('Save & next')?>" class="submit" id="recipe_savenext" name="recipe_savenext" <?php if (isset($product_information) && !empty($product_information) && $product_information[0]->parent_proid != 0){?>disabled<?php }?>>
			   			</div>
					</div>
					<?php endif;?>
			</div>
		</div>
		<div class="boxed">
      			<h3 id="labeler"> <?php echo _('Labeler')?></h3>
      			<div style="padding: 0px; display: block;" class="inside">
        			<div class="table">
        				<table border="0">
			       			<tbody>
			       				<tr>
			       					<td class="textlabel"></td>
			                		<td style="padding-right:250px"><b>== <?php echo _('The setting below will printed out in FoodDESK labelprinter');?> ==</b></td>
			              		</tr>

			              		<tr>
			              			<?php
			              			if(isset($product_labeler) && !empty($product_labeler)){
										if((!empty($product_labeler[0]->duedate)) && ($product_labeler[0]->duedate != 0)){
			              					$datediff	= $product_labeler[0]->duedate;
			              				}
			              			}
			              			?>
			                		<td class="textlabel"><?php echo _('Duedate')?></td>
			                		<td style="padding-right:250px"><?php echo '+'?><input type="text" class="text medium" style="width:60px;" id="duedate" name="duedate" value="<?php if(isset($datediff)){echo $datediff;}?>">&nbsp;<?php echo _('days from today so on the label due date will be shown as')?>&nbsp;<b id="changeddate"><?php if(isset($datediff)){echo date('d/m/Y', strtotime('+'.$datediff.' days'));}?></b></td>
			              		</tr>

			              		<tr>
			                		<td class="textlabel"><?php echo _('Duedate Type ')?></td>
			                		<td style="padding-right:250px">
			                			<select id="duedate_type" name="duedate_type" style="margin-left: 0px;">
								  			<option <?php if(isset($product_labeler) && !empty($product_labeler) && $product_labeler[0]->duedate_type == 'THT'):?>selected="selected"<?php endif;?> value="THT"><?php echo _("THT");?></option>
						  		 		 	<option <?php if(isset($product_labeler) && !empty($product_labeler) && $product_labeler[0]->duedate_type == 'TGT'):?>selected="selected"<?php endif;?> value="TGT"><?php echo _("TGT");?></option>
						 				</select>
			                		</td>
			              		</tr>
			              		<tr>
			                		<td class="textlabel"><?php echo _('Production Date')?></td>
			                		<td style="padding-right:250px">
			              				<input type="text" readonly="readonly" id="prod_date" name="prod_date" class="text short" value="<?php if(isset($product_labeler) && !empty($product_labeler) && ($product_labeler[0]->production_date != '0000-00-00')): echo date('d/m/Y',strtotime($product_labeler[0]->production_date)); endif;?>">&nbsp;&nbsp;&nbsp;
                						<input type="button" id="button1" name="button1" onclick="displayCalendar(document.getElementById('prod_date'),'dd/mm/yyyy',this)" value="...">
                					</td>
			              		</tr>
			              		<tr>
			                		<td class="textlabel"><?php echo _('Conserve between')?></td>
			                		<td style="padding-right:250px"><input type="text" class="text medium"  style="width:60px;" id="conserve_min" name="conserve_min" value="<?php if(isset($product_labeler) && !empty($product_labeler)){echo $product_labeler[0]->conserve_min;}?>">&nbsp;<?php echo _('and')?>&nbsp;<input type="text" class="text medium" style="width:60px;" id="conserve_max" name="conserve_max" value="<?php if(isset($product_labeler) && !empty($product_labeler)){echo $product_labeler[0]->conserve_max;}?>">&nbsp;<?php echo '&deg;C'?></td>
			              		</tr>
			              		<tr>
			                		<td class="textlabel"><?php echo _('Weight')?></td>
			                		<td style="padding-right:250px">
			                			<input type="text" class="text medium" style="width:60px; display:inline-block;" id="weight" name="weight" value="<?php if(isset($product_labeler) && !empty($product_labeler)){echo $product_labeler[0]->weight;}?>">&nbsp;
			                			<select id="weight_unit" name="weight_unit" style="margin-left: 0px; display:inline-block;">
								  			<option <?php if(isset($product_labeler) && !empty($product_labeler) && $product_labeler[0]->weight_unit == 'kg'):?>selected="selected"<?php endif;?> value="kg"><?php echo _("kg");?></option>
						  		 		 	<option <?php if(isset($product_labeler) && !empty($product_labeler) && $product_labeler[0]->weight_unit == 'gr'):?>selected="selected"<?php endif;?> value="gr"><?php echo _("gr");?></option>
						  		 		 	<option <?php if(isset($product_labeler) && !empty($product_labeler) && $product_labeler[0]->weight_unit == 'l'):?>selected="selected"<?php endif;?> value="l"><?php echo _("l");?></option>
						 				</select>
						 			</td>
			              		</tr>
			              		<tr>
			                		<td class="textlabel">
			                			<?php echo _('Barcode')?>
										<a id="help-prodl0" href="#" title="<?php echo _('Select if you need to show barcode on label');?>">
											<img width="16" height="16" src="<?php echo base_url()?>assets/cp/images/help.png">
										</a>
									</td>
			                		<td style="padding-right:250px"><input type="checkbox" id="show_bcode" name="show_bcode" value="1" <?php if(isset($product_labeler) && !empty($product_labeler) && $product_labeler[0]->show_bcode == 1){?>checked="checked"<?php }?>></td>
			              		</tr>
			              		<tr>
				            	<td class="textlabel">
				              		<?php echo _("Extra Notification");?>:
				              	</td>
			              		<td>
			              		<?php $extra = "0"; if(isset($product_labeler)){if(!empty($product_labeler)){$extra = $product_labeler[0]->extra_notification;}}?>
			              			<select name="extra_noti" id="extra_noti">
			              				<option value="0"><?php echo _("select a phrase")?></option>
				              			<option value="1" <?php if($extra == "1"){echo 'selected="selected"';}?>><?php echo _("verpakt onder beschermende atmosfeer");?></option>
										<option value="2" <?php if($extra == "2"){echo 'selected="selected"';}?>><?php echo _("met zoetstoffen");?></option>
										<option value="3" <?php if($extra == "3"){echo 'selected="selected"';}?>><?php echo _("met suiker(s) en zoetstof(fen)");?></option>
										<option value="4" <?php if($extra == "4"){echo 'selected="selected"';}?>><?php echo _("bevat aspartaam (een bron van fenylalanine)");?></option>
										<option value="5" <?php if($extra == "5"){echo 'selected="selected"';}?>><?php echo _("bevat een bron van fenylalanine");?></option>
										<option value="6" <?php if($extra == "6"){echo 'selected="selected"';}?>><?php echo _("overmatig gebruik kan een laxerend effect hebben");?></option>
										<option value="7" <?php if($extra == "7"){echo 'selected="selected"';}?>><?php echo _("bevat zoethout — mensen met hoge bloeddruk dienen overmatig gebruik te vermijden");?></option>
										<option value="8" <?php if($extra == "8"){echo 'selected="selected"';}?>><?php echo _("Hoog cafeïnegehalte. Niet aanbevolen voor kinderen en vrouwen die zwanger zijn of borstvoeding geven");?></option>
										<option value="9" <?php if($extra == "9"){echo 'selected="selected"';}?>><?php echo _("Bevat cafeïne. Niet aanbevolen voor kinderen en zwangere vrouwen");?></option>
										<option value="10" <?php if($extra == "10"){echo 'selected="selected"';}?>><?php echo _("met toegevoegde plantensterolen");?></option>
										<option value="11" <?php if($extra == "11"){echo 'selected="selected"';}?>><?php echo _("levensmiddel uit voedingsoogpunt mogelijk niet geschikt is voor zwangere en borstvoedende vrouwen en kinderen jonger dan vijf jaar");?></option>
										<option value="12" <?php if($extra == "12"){echo 'selected="selected"';}?>><?php echo _("Bewaren in een koel constante (12c° à 18C°) droge (max 70%) ruimte");?></option>
									</select>
			              		</td>
			              	</tr>
			       			</tbody>
			       		</table>
        			</div>
        			<div class="sub_div">
			    		<div class="sub__div" colspan="2">
			        		<!--  <a href ="javascript:;"><button name="print_label" id="print_label"><?php //echo _('Export')?></button></a> -->
        				<?php if($product_information):?>
			        		<input type="button" value="<?php echo _("Update");?>" class="submit" id="labeler_update" name="labeler_update">
			        		<input type="hidden" value="add_edit" id="labeler_act" name="labeler_act">
			    			<input type="hidden" value="update" id="labeler_add_update" name="labeler_add_update">
			    			<!-- <input type="button" value="<?php //echo _('Save & next')?>" class="submit" id="labeler_savenext" name="labeler_savenext">  -->
						<?php else:?>
			        		<input type="button" value="<?php echo _('Send')?>" class="submit" id="labeler_add" name="labeler_add">
			      			<input type="hidden" value="add" id="labeler_add_update" name="labeler_add_update">
			      			<!--  <input type="button" value="<?php //echo _('Save & next')?>" class="submit" id="labeler_savenext" name="labeler_savenext"> -->
						<?php endif;?>
					</div>
					</div>
        		</div>
        	</div>
        	<script type="text/javascript">
				jQuery(document).ready(function(){
					$("#duedate").keypress(function (e) {
						if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
					        return false;
					    }
					});
					$("#conserve_min, #conserve_max").keypress(function (e) {
					    if ((e.which != 45 || $(this).val().indexOf('-') != -1) && e.which != 0 && e.which != 8 && (e.which != 46 || $(this).val().indexOf('.') != -1) && (e.which < 48 || e.which > 57)) {
							return false;
					    }
					});
					$("#duedate").change(function(){
						var date	= new Date(),
							days	= parseInt(this.value.replace(/[^0-9\.]/g,''));
						if(!isNaN(date.getTime())){
				            date.setDate(date.getDate() + days);
				            $("#changeddate").text(date.toInputFormat());
						};
					});

					Date.prototype.toInputFormat = function() {
					       var yyyy = this.getFullYear().toString();
					       var mm = (this.getMonth()+1).toString(); // getMonth() is zero-based
					       var dd  = this.getDate().toString();
					       return (dd[1]?dd:"0"+dd[0]) + "/" + (mm[1]?mm:"0"+mm[0]) + "/" + yyyy; // padding
					};

					/*$('#print_label').on('click', function(){
						//$('#loadingmessage').show();
						var lotnr = $('#lotnr').val();
						var duedate = $('#duedate').val();
						var conserve_min = $('#conserve_min').val();
						var conserve_max = $('#conserve_max').val();
						if($(".prod_id").length != 0)
							var pro_id = $(".prod_id").val();
						if(lotnr != '' && duedate != '' && conserve_min != '' && conserve_max != '' && typeof pro_id != 'undefined'){
							var url = base_url+'cp/fooddesk/label_export/'+pro_id+'/'+lotnr+'/'+duedate+'/'+conserve_min+'/'+conserve_max;
							window.open(url);
						}
						else{
							alert('<?php //echo _("All fields must be filled");?>');
						}
					});*/
					});
				//});
        	</script>
		          	<input type="hidden" value="" id="hidden_fdds_quantity" name="hidden_fdds_quantity">
		          	<input type="hidden" value="" id="hidden_own_pro_quantity" name="hidden_own_pro_quantity">
		          	<input type="hidden" value="0" id="hidden_fdd_total" name="hidden_fdd_total">
		          	<input type="hidden" value="0" id="hidden_own_total" name="hidden_own_total">
		        <!--</form>-->
		        <script language="javascript" type="text/javascript">

			       /* $('form#frm_products_addedit #add,form#frm_products_addedit #update').click(function(){
		        		if(form_validate()){
							$('form#frm_products_addedit').submit();
			        	}
			        });*/

			        /*function form_validate(){

			        	// checking category
			        	if($("#categories_id").val() == "-1"){
				        	alert('<?php echo _('Select a categoy.');?>');
				        	$('#categories_id').focus();
				        	return false;
				        }

			        	// checking product name
			        	if($("#proname").val() == ""){
				        	alert('<?php echo _('please give the product name.');?>');
				        	$('#proname').focus();
				        	return false;
				        }


			        	return check_crop();
			        }

					var frmValidator = new Validator("frm_products_addedit");

					frmValidator.setCallBack(check_crop);

					frmValidator.EnableMsgsTogether();
					frmValidator.addValidation("categories_id","dontselect=-1","<?php echo _('Select a categoy.');?>");
					frmValidator.addValidation("proname","req","<?php echo _('please give the product name.');?>");*/

					function check_crop(){
						if($("#crop_button").length){
							alert("<?php echo _('Please crop image before adding/updating product else it will be distorted in shop');?>");
							return false;
						}else{
							return true;
						}
					}

					function reset_wt(){

						tb_show("<?php echo _('New weight');?>", '#TB_inline?height=200&amp;width=300&amp;inlineId=wt_resetter');
						$('#TB_ajaxContent #insert_new_wt').val($("#recipe_weight").val());
					}

					function reset_wt_submit(){
						var old_wt = $("#recipe_weight").val();
						var new_wt = $("#TB_ajaxContent #insert_new_wt").val();
						var ratio = new_wt/old_wt;

						$(".own_product_quants,.fdd_product_quants").each(function(){
							var cur_val = parseFloat($(this).val());
							$(this).val((cur_val*ratio).toFixed(0));
						});

						$("#recipe_weight").val(new_wt);
						$("#recipe_weight_span").html(new_wt);

						quant_change();
						tb_remove();

// 						alert(old_wt);
// 						alert(new_wt);
// 						alert(ratio);
					}

					function upload_sheet(fdd_pro_id){
						tb_show("<?php echo _('New product sheet');?>", '#TB_inline?height=200&amp;width=300&amp;inlineId=pdf_uploader');
						$("#TB_ajaxContent #fdd_pro_pdf_id").val(fdd_pro_id);
					}

				</script>
	    </div>
	    </div>
<!-- /content -->

<div id="credit_require" style="display: none">
   	<p><?php echo _('Sorry! Currently You have no credit to use a FoodDESK product.');?></p>
   	<p><?php echo _('To buy credits, choose a package.');?></p>
   	<ul>
   		<li><a onclick="add_credit(100)" href="javascript:;"><?php echo _('100 products/credits for 10');?>&euro;</a></li>
   		<li><a onclick="add_credit(200)" href="javascript:;"><?php echo _('200 products/credits for 15');?>&euro;</a></li>
   	</ul>
</div>

<div id="wt_resetter" style="display: none">
	<input type="number" style="width:100px" class="text" id="insert_new_wt" value="" min="0" step=".001">
	<button onclick="reset_wt_submit()"><?php echo _('Submit'); ?></button>
</div>


<div id="shortname_renamer" style="display: none">
	<input type="text" style="width:200px" class="text" id="rename_it" value="" >
	<input type="hidden" style="width:200px" class="text" id="rename_hidden" value="" >
	<button onclick="do_rename()"><?php echo _('Submit'); ?></button>
</div>

<div id="pdf_uploader" style="display: none">
	<form action="<?php echo base_url().'cp/fooddesk/update_pdf'; ?>" method="post" enctype="multipart/form-data">
		<input type="file" style="width:200px" class="text" id="pdf" value="" name="pdf">
		<input type="hidden" value="" name="fdd_pro_pdf_id" id="fdd_pro_pdf_id">
		<input type="hidden" value="<?php echo current_url(); ?>" name="cur_url" >
		<input type="submit" value="submit">
	</form>
</div>
<?php if($this->session->userdata('login_via') == 'mcp'){?>
<div id="prod_ingre_list" style="display: none">
<?php
if(!empty($used_fdd_pro_info)){
	foreach ($used_fdd_pro_info as $fdd_pro_id){
		echo "<div><div class =\"usr_text\"><label><a href=\"javascript:;\" onclick=\"login_at_fdd(".$fdd_pro_id['fdd_pro_id'].")\">".$fdd_pro_id['p_name'.$sel_lang.'']."</a></label></div><div class =\"usr_val\">";
   		$str = '';
   		foreach ($product_ingredients as $ing){
   			if(($fdd_pro_id['fdd_pro_id'] == $ing->kp_id) && ($ing->ki_id != 0)){
   				if($ing->ki_name == ')' ){
					$str = substr($str, 0, -2);
					$str .= stripslashes($ing->ki_name);
					$str .= ', ';
				}
				elseif($ing->ki_name == '(' ){
					$str = substr($str, 0, -2);
					$str .= stripslashes($ing->ki_name);
					$str .= '  ';
				}
				elseif($ing->ki_name == ':' ){
					$str = substr($str, 0, -2);
					$str .= stripslashes($ing->ki_name);
					$str .= ' ';
				}
				else{
					$str .= stripslashes($ing->ki_name);
					$str .= ', ';
				}
   			}}
   			$str = rtrim($str,', ');
		echo trim($str,', ')."</div></div>";
	}
}
?>
</div>
<?php }?>
<div id="remark_mail" style="display: none">
   	<p><?php echo _('If you have a remark or suggestion regarding the allergens, ingredients or any other issue then you can quickly send this to us by filling the form below.');?></p>
   	<p><label><?php echo _('Email').':'?></label><input type="text" class="text medium" name="sender_name" placeholder="<?php echo _('admin email');?>" value="<?php echo (isset($admin_mail))?$admin_mail:'';?>"></p>
   	<p><label><?php echo _('Subject').':'?></label><input type="text" class="text medium" name="sender_subject" ></p>
   	<p><label><?php echo _('Message').':'?></label><textarea id="sender_msg" wrap="hard" cols="36" rows="5"></textarea></p>
   	<p><button id="remark_mail_button"><?php echo _('Send')?></button></p>
</div>
<script type="text/javascript">

//Not in use

	$(".prod_name").keyup(function(e){
		if(e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40){
			show_suggestions();
		}
	});

	function show_suggestions(){
		availableTags = [];

		var search_str = $('#search_box_fdd').val();

		if(search_str.length > 1){
			if(xhr && xhr.readystate != 4){
				xhr.abort();
			}

			xhr = $.ajax({
				type:"POST",
				url: base_url+'cp/fooddesk/get_serched_AjaxProducts',
				data: {
					'search_str': search_str,
					'direct_add':0
				},
				success: function(result_array){
					var arr=JSON.parse(result_array);
					arr = sort_array_by_word(search_str,arr);
					for(var i=0;i<arr.length;i++){
						var new_label = '';
						new_label += '<strong>';
						if(arr[i]['p_name_dch'] != ''){
							new_label += arr[i]['p_name_dch'];
						}
						else if(arr[i]['p_name_fr'] != ''){
							new_label += arr[i]['p_name_fr'];
						}
						else{
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
					autocomplete_intializes();
				},//
			});

		}else{
			autocomplete_intializes();
		}
	}

	function autocomplete_intializes(){
		$( "#search_box_fdd" ).autocomplete({
			minLength: 0,
			appendTo: '#fdd_product',
			source: availableTags,
			focus: function( event, ui ) {
				return false;
			},
			select: function( event, ui ) {
				$( "#search_box_fdd" ).val( $(ui.item.label).text() );
				$( "#hidden_search_box_id_fdd").val( ui.item.value );
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

		$('#search_box_fdd').autocomplete("search");
	}
	if ($.ui){
		$.extend($.ui.autocomplete,
		{
			filter: function(results, term)
			{
				if($(':focus').attr('id') == 'search_box_fdd'){
					var all_terms = split( term );
					// remove the current input
					var last_item = all_terms.pop();
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

	// Not in use

	function add_pro(){
		var sel_lang = '<?php echo $sel_lang; ?>';
		var data_array = new Array();
		var data_array_str ='';

		var fdd_id = $( "#hidden_search_box_id_fdd" ).val();
		var pro_quantity = 1;
		var hidden_pro_prefix = "";
		var hidden_semi_pro_id = 0;

		if(fdd_id != ''){
			$("#loding_gifs").show();
			$("#search_box_fdd").val("");
			$( "#hidden_search_box_id_fdd" ).val("");

			//total_quant = total_quant+parseInt(pro_quantity);
			var new_data_array= {fdd_pro_id:fdd_id,quantity:pro_quantity,hidden_pro_pre:hidden_pro_prefix,hidden_semi_pro_id:hidden_semi_pro_id};
			data_array_str += fdd_id+'#'+pro_quantity+'#'+$("#search_box_fdd").val()+'#'+hidden_pro_prefix+'**';
			data_array.push(new_data_array);

			$("#hidden_fdds_quantity").val(data_array_str);
			data_array = data_array.sort(function(a,b) { return b['quantity'] - a['quantity'];});

			jQuery.ajax({
				url: base_url+'cp/fooddesk/get_ing_traces_allergence_all/',
				type: 'post',
				async: false,
				dataType:'json',
				data:{'fdd_prods':data_array},
				success: function(response){
					var all_fdd_prods = response[0];

					for(var i=0;i<all_fdd_prods.length;i++){

						var product = all_fdd_prods[i].info.product;
		  				var ingredients = product[0].ingredients;
		  				var traces = product[0].traces;
		  				var allergence = product[0].allergence;
		  				var hidden_pro_pre = all_fdd_prods[i].hidden_pro_pre;
		  				var quant = all_fdd_prods[i].quantity;
		  				var semi_pro = all_fdd_prods[i].hidden_semi_pro_id;

		  				var new_ings = '';

						var this_pro_name = '';
						if( product[0].p_name_dch.length > 23 ){
							this_pro_name = product[0].p_name_dch.substring(0,23)+'...';
						}else{
							this_pro_name = product[0].p_name_dch;
						}

		  				new_ings += '<span id=\"ing_sub_row_'+product[0].p_id+'\" class=\"ing_pro_name_row\" rel=\"0\" >';

						new_ings += '	<span width=\"30%\"  >';
						new_ings += '		<input type=\"text\" class=\"product_name_text text\" style=\"width:50%\" value=\"'+stripslashes(this_pro_name)+'\" disabled>';
						new_ings += '	</span>';

						new_ings += '	<span width=\"4%\" style=\"vertical-align: sub;\">';
						new_ings += '		<img src=\"'+base_url+'assets/cp/images/Delete.gif\" style=\"width: 20px; margin-top: 7px;\" onclick=\"remove_this_fdd_pro('+product[0].p_id+')\" id=\"delete_pro\" />';
						new_ings += '	</span>';
						new_ings += '	<span width=\"4%\" style=\"vertical-align: sub;\">';
						if(product[0].data_sheet != '' && product[0].data_sheet != null){
							new_ings += '	<a target="_blank" href="'+fdd_url+'assets/cp/uploads/'+product[0].data_sheet+'">';
							new_ings += '		<img src=\"'+base_url+'assets/images/pdf2.jpeg\" style=\"width: 20px;\" />';
							new_ings += '	</a>';
						}else{
							new_ings += '		<img src=\"'+base_url+'assets/images/pdf1.jpeg\" style=\"width: 20px;\" />';
						}
						new_ings += '	</span>';
						new_ings += '	<span width=\"37%\" style=\"vertical-align: sub;\">';
						new_ings += '	</span>';
						new_ings += '</span>';

						jQuery("#row_container_fdd").append(new_ings);

						//adding ingredients
						var data = $("#ingredients").select2('data');
						if(sel_lang == '_fr'){
							var product_shortname = product[0].p_short_name_fr;
						}else if(sel_lang == '_dch'){
							var product_shortname = product[0].p_short_name_dch;
						}else{
							var product_shortname = product[0].p_short_name_dch;
						}
						var combine_id = '#'+product_shortname+'#'+0+'#'+product[0].p_id+'#0';
						var text = product_shortname;
						data.push({id:combine_id,text:text});
						if(ingredients.length > 0){
							var combine_id2 = '#(#'+0+'#'+product[0].p_id+'#0';
							var text2 = '(';
							data.push({id:combine_id2,text:text2});

							for(var j=0;j<ingredients.length;j++){
								if(sel_lang == '_fr'){
									var product_ing = ingredients[j].ing_name_fr;
									var aller_type = ingredients[j].aller_type_fr;
									var allergence = ingredients[j].allergence_fr;
									var sub_allergence = ingredients[j].sub_allergence_fr;
									var new_allergence = ingredients[j].new_allergence_fr;
								}else if(sel_lang == '_dch'){
									var product_ing = ingredients[j].ing_name_dch;
									var aller_type = ingredients[j].aller_type_dch;
									var allergence = ingredients[j].allergence_dch;
									var sub_allergence = ingredients[j].sub_allergence_dch;
									var new_allergence = ingredients[j].new_allergence_dch;
								}else{
									var product_ing = ingredients[j].ing_name;
									var aller_type = ingredients[j].aller_type;
									var allergence = ingredients[j].allergence;
									var sub_allergence = ingredients[j].sub_allergence;
									var new_allergence = ingredients[j].new_allergence;
								}
								var combine_id1 = ingredients[j].prefix+'#'+stripslashes(product_ing)+'#'+ingredients[j].ing_id+'#'+product[0].p_id+'#0'+'#'+j+'#'+aller_type+'#'+allergence+'#'+sub_allergence+'#'+new_allergence;
								if(ingredients[j].prefix == ''){
									var text1 = stripslashes(product_ing);
								}else{
									var text1 = stripslashes(product_ing)+'('+ingredients[j].prefix+')';
								}

								data.push({id:combine_id1,text:text1});
							}
							var combine_id2 = '#)#'+0+'#'+product[0].p_id+'#0';
							var text2 = ')';;
							data.push({id:combine_id2,text:text2});
						}
						$("#ingredients").select2("data", data, true);

						//adding allergense
						var data_allr = $("#allergence").select2('data');

						if(allergence.length > 0){

							for(var j=0;j<allergence.length;j++){
								//alert(ingredients[j].ing_id);
								var combine_id1 = '#'+stripslashes(allergence[j].all_name_dch)+'#'+allergence[j].all_id+'#'+product[0].p_id;
								var text1 = stripslashes(allergence[j].all_name_dch);

								data_allr.push({id:combine_id1,text:text1});
							}

						}
						$("#allergence").select2("data", data_allr, true);

						//adding traces
						var data_tr = $("#traces_of").select2('data');

						if(traces.length > 0){

						for(var j=0;j<traces.length;j++){
								//alert(ingredients[j].ing_id);
								var combine_id1 = '#'+stripslashes(traces[j].t_name_dch)+'#'+traces[j].t_id+'#'+product[0].p_id;
								var text1 = stripslashes(traces[j].t_name_dch);

								data_tr.push({id:combine_id1,text:text1});
							}

						}
						$("#traces_of").select2("data", data_tr, true);
					}
				}
			});
			$("#loding_gifs").hide();
			$("#fdd_product").css("display","none");
		}
		else{
			alert("<?php echo _('Please enter a product name first')?>");
		}
	}


	$("#delete_pro").on("click",function(){
		$("#fdd_product").css("display","");
		$("#row_container_fdd").html('');
	});

	$("#remark_mail_button").on("click",function(){
		var valid = true;
		var emailRegex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/,
		 sender = $("#TB_ajaxContent").find("input[name='sender_name']"),
		 subject = $("#TB_ajaxContent").find("input[name='sender_subject']"),
		 message = $("#TB_ajaxContent").find("#sender_msg");
		var prod_id = $(".prod_id");
		var	proname = $("#proname").val();

		allFields = $( [] ).add( sender ).add( subject ).add( message );
	    allFields.removeClass( "ui-state-error" );

	 	valid = valid && checkLength( sender );
	    valid = valid && checkLength( subject );
	   	valid = valid && checkLength( message );

	   	valid = valid && checkRegexp( sender, emailRegex );

	   	if ( valid ) {
	    	$('#loadingmessage').show();
	    	$.post(
				"<?php echo base_url()?>cp/cdashboard/send_remark_by_mail",
			   	{"sender":sender.val(),"subject":subject.val(),"message":message.val(),"prod_id":prod_id.val(), "proname":proname},
			   	function(data){
			   		$('#loadingmessage').hide();
			   		if(data)
			   			alert("<?php echo _('Remark sent successfully')?>");
			   		else
			   			alert("<?php echo _('Remark can\'t be sent successfully')?>");
			   		self.parent.tb_remove();
				}
			);
	    }
	   	else{
		   	alert("<?php echo _('Fields are empty or invalid');?>");
		}
	});
	function checkLength( o ) {
		if ( o.val().length < 1 ) {
	    	o.addClass( "ui-state-error" );
	    	setTimeout(function() {
	   			o.removeClass( "ui-state-error", 1500 );
	    	}, 1000 );
	    	return false;
	    }
	    else {
	    	return true;
	    }
	}

	function checkRegexp( o, regexp ) {
		if ( !( regexp.test( o.val() ) ) ) {
	    	o.addClass( "ui-state-error" );
	 		setTimeout(function() {
	 			o.removeClass( "ui-state-error", 1500 );
	    	}, 1000 );
	    	return false;
	    }
	    else{
			return true;
	    }
	}

	function login_at_fdd(fdd_pro_id){
		$('#loadingmessage').show();
		$.post('<?php echo base_url();?>cp/cdashboard/login_at_fdd/'+fdd_pro_id,{},
			function(res){
				$('#loadingmessage').hide();
				if(res){
					window.open("<?php echo $this->config->item('fdd_url')?>mcp/inloggen/inloggen_via_obs/"+res+"/"+fdd_pro_id+"","_blank");
					window.focus();
				}
			});
	}

	function rotcw(obj) {
  		$("#uploaded_image").html('<img src="'+base_url+'assets/cp/images/loader.gif" alt="'+cropping+'"/>');
		//console.log($(obj).attr('data-img'));
		$.ajax({
				type:'POST',
				url: base_url+'cp/image_upload/rotate_image',
				data:{src:$(obj).attr('data-img1'),angle:'cw'},
				success: function(response){
					$("#uploaded_image").html(response);

					jQuery('#target').Jcrop({
			       	//onChange: updatePreview,
			       	onSelect: updateCoords,
				    setSelect: [ $('#x').val(), $('#y').val(), $('#w').val(), $('#h').val() ],

				    aspectRatio: 1
				    });
				},
			});
		}
		function rotacw(obj) {
  		$("#uploaded_image").html('<img src="'+base_url+'assets/cp/images/loader.gif" alt="'+cropping+'"/>');

		$.ajax({
				type:'POST',
				url: base_url+'cp/image_upload/rotate_image',
				data:{src:$(obj).attr('data-img2'),angle:'acw'},
				success: function(response){
					$("#uploaded_image").html(response);

					jQuery('#target').Jcrop({
			       	//onChange: updatePreview,
			       	onSelect: updateCoords,
				    setSelect: [ $('#x').val(), $('#y').val(), $('#w').val(), $('#h').val() ],

				    aspectRatio: 1
				    });
				},
			});
		}

		// start Function for rotating saved images clockwise and anticlockwise
		function srotcw(obj) {
		$.ajax({
				type:'POST',
				url: base_url+'cp/image_upload/rotate_uploaded_image',
				data:{src:$(obj).attr('data-img1'),angle:'cw'},
				success: function(response){
					$(obj).parent().children('a').eq(0).attr('data-img1',response);
					$(obj).parent().children('a').eq(1).attr('data-img2',response);
					$("#uploaded_image_td").children('img').replaceWith('<img id="suploaded_image" style="height:300px" src="'+base_url+"assets/temp_uploads/"+response+'"/>');
					$('.rotated_image_hid').val(response);
				},
			});
		}
		function srotacw(obj) {
		$.ajax({
				type:'POST',
				url: base_url+'cp/image_upload/rotate_uploaded_image',
				data:{src:$(obj).attr('data-img2'),angle:'acw'},
				success: function(response){
					$(obj).parent().children('a').eq(0).attr('data-img1',response);
					$(obj).parent().children('a').eq(1).attr('data-img2',response);
					$("#uploaded_image_td").children('img').replaceWith('<img id="suploaded_image" style="height:300px" src="'+base_url+"assets/temp_uploads/"+response+'"/>');
					$('.rotated_image_hid').val(response);
				},
			});
		}
</script>