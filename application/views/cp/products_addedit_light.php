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
var multi_select = "<?php echo _("Multiselect Attributes");?>";
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
var apply_group_setting_msg1 = "<?php echo _("All groups data within (sub)category "); ?>";
var apply_group_setting_msg2 = "<?php echo _(" will be overwritten with these values. Are you sure to overwrite these groups in this (sub)category?"); ?>";
var ing_datas = new Array();
var allg_datas = new Array();
var traces_datas = new Array();
var fdd_url = "<?php echo $this->config->item('fdd_url'); ?>";

var not_more_than_100 = "<?php echo _("Total quatity of All foodDESK product shuold not be more than 100gm");?>";
var plz_select_producer_msg = "<?php echo _('Please select a producer or supplier');?>";

var cant_add_as_semi = "<?php echo _('Can not be added as semi product');?>";
</script>

<script src="<?php echo base_url();?>assets/kcp/js/select2/select2.min.js?version=<?php echo version;?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/kcp/js/select2/select2.css" media="screen">

<link rel="stylesheet" href="<?php echo base_url();?>assets/css/ui/jquery.ui.all.css">
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.core.js?version=<?php echo version;?>"></script>
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.widget.js?version=<?php echo version;?>"></script>
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.sortable.js?version=<?php echo version;?>"></script>
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.position.js?version=<?php echo version;?>"></script>
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.draggable.js?version=<?php echo version;?>"></script>
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.droppable.js?version=<?php echo version;?>"></script>

<script src="<?php echo base_url(); ?>assets/plugins/jquery-ui-1.11.2.custom/jquery-ui.min.js?version=<?php echo version;?>" type="text/javascript"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/jquery-ui-1.11.2.custom/jquery-ui.theme.min.css">
<style>
	.ui-autocomplete {
		max-height: 100px;
		overflow-y: auto;
		z-index:500;
	}

	.select2-container-multi .select2-choices{
    	min-height: 110px;
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
	.table #allergence_container > p {
    	padding: 0 40px;
    	width: 8% !important;
	}

	#upgrade_pop.pop-btn {
	    background: #18517e none repeat scroll 0 0;
	    border: medium none;
	    color: #ffffff;
	    cursor: pointer;
	    padding: 10px 30px;
	}
	#TB_window #TB_ajaxContent {
	    overflow-y: auto;
	}
	.sub__div > a {
	    text-decoration: none;
	}
	#TB_ajaxContent > #pop-window {
	    background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
	}
	#pop-window a {
		text-decoration: none;
	}
	p.ffd span {
	   width: 180px;
	   display: inline-block;
	   position: absolute;
	   top: -6px;
	}
	p.ffd {
	   position: relative;
	}
</style>

<!-- -------------------------------------- CROPPING IMAGE ------------------------------------------------------ -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/cp/new_css/jquery.Jcrop.css" type="text/css" />
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
.ing_pro_name_row td {
    padding: 10px 0;
}

.fc-first th {
    background: none repeat scroll 0 0 black !important;
    border: medium none !important;
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
table .cal_recipe {
    background: #caca8a none repeat scroll 0 0;
    color: #000000;
    margin: auto;
    max-width: 640px;
    padding: 10px;
    text-align: center;
    width: 100%;
    font-size:12px;
}

.cal_recipe span {
	color:red;
    display: block;
}
.ui-state-hover, .ui-widget-content .ui-state-hover, .ui-widget-header .ui-state-hover, .ui-state-focus, .ui-widget-content .ui-state-focus, .ui-widget-header .ui-state-focus {
   border: 1px solid #fff;
   background: transparent!important;
   font-weight: bold;
   color: #eb8f00!important ;
}
</style>
<script src="<?php echo base_url();?>assets/cp/new_js/jquery.Jcrop.js?version=<?php echo version;?>"></script>
<script type="text/javascript">
	var do_diasble = 0;

	<?php if(isset($product_information)){if(!empty($product_information)){ if($product_information['0']->direct_kcp == 1 ) {    ?>
		do_diasble = 1;
	<?php } } } ?>
	var jcrop_api,boundx,boundy,xsize,ysize,$preview,$pcnt,$pimg;
	$(document).ready(function(){
		//hide_repeated();
		$(".thickboxed").click(function(){
			tb_show("<?php echo _("Upload Image");?>", "<?php echo base_url(); ?>cp/image_upload/ajax_img_upload?height=400&width=600", "true");
		});
		$(".thickboxed_gal").click(function(){
			var num = $(this).attr('data-count');
			tb_show("<?php echo _("Upload Image");?> "+num, "<?php echo base_url(); ?>cp/image_upload/ajax_img_upload/cp/"+num+"?height=400&width=600", "true");
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

				var is_display_image = "1";

				$.post(
					"<?php echo base_url()?>cp/products/products_addedit",
					{"categories_id":$("#categories_id").val(),"subcategories_id":$("#subcategories_id").val(),"pro_art_num":$("#pro_art_num").val(),"proname":$("#proname").val(),"prodescription":$("#prodescription").val(),"product_type":0,"image_name":img_name,"image_display":is_display_image,"ajax_add_update":$("#product_info_add_update").val(),"action":"product_info","prod_id":$(".prod_id").val(),"action_val":$(this).val(),"rotated_image":rotated_image,"current_prod_img":current_prod_img},
					function(data){
						$('#loadingmessage').hide();

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
							window.location = base_url+"cp/products/products_addedit/product_id/"+response['id'];
						}
						$(".prod_id").val(response['id']);
					});
	       	}
		});

		$("#recipe_add,#recipe_update").click(function(){
			if(product_info_validate()){
				tb_show('','TB_inline?height=400&amp;width=800&amp;inlineId=upgrade_account',null);
				$("#TB_window").css("background-color", "#d2d269");
				$("#TB_title").css("background-color", "#d2d269");
				$("#pop-window td").css({"background-color" : "#d2d269", "border-style": "hidden"});
				$.post(
						"<?php echo base_url()?>cp/fooddesk/recipe_calculate",
						{},
						function(data){
							$('#count_recipe').text(data);
							if(data == 0)
								$("#recipe_add,#recipe_update").attr('disabled','disabled');
						});
			}
		});

		$("#allergence_add,#allergence_update,#allergence_info_savenext").click(function(){
			 var data = $('#allergence_container input:checkbox:checked').map(function(){
	             return this.value;
	        	}).get();
			 $('#loadingmessage').show();
			 if($("#allergence_add_update").val() == 'add')
				$("#allergence_add").attr("disabled","disabled");

			 if($("#allergence_add_update").val() == 'update')
				$("#allergence_update").attr("disabled","disabled");

			$("#allergence_info_savenext").attr("disabled","disabled");

			 if(product_info_validate()){
				$.post(
					"<?php echo base_url()?>cp/products/products_addedit",
					{"allergence_list":data,"ajax_add_update":$("#allergence_add_update").val(),"action":"allergence","prod_id":$(".prod_id").val(),"categories_id":$("#categories_id").val(),"subcategories_id":$("#subcategories_id").val(),"proname":$("#proname").val(),"product_type":0,"action_val":$(this).val()},
					function(data){
						$('#loadingmessage').hide();



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

						if($("#allergence_add_update").val() == 'update'){
							$("#allergence_update").removeAttr("disabled");
							alert("<?php echo _('Allergence Updated')?>");
						}
						else{
							$("#allergence_add").removeAttr("disabled");
							alert("<?php echo _('Allergence Added')?>");
						}
						$("#allergence_info_savenext").removeAttr("disabled");
						var response = JSON.parse(data);
						if(response['is_next'] == 'true'){
							window.location = base_url+"cp/products/products_addedit/product_id/"+response['id'];
						}
						$(".prod_id").val(response['id']);
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
	$(document).ready(function(){
  		$('.img-check').click(function() {
  			if($(this).hasClass('chk-active')){
      			$(this).removeClass('chk-active');
      		    $(this).siblings('input').prop('checked',false);
  		    }
  		    else{
      			$(this).addClass('chk-active').siblings('input').prop('checked',true);
  		    }
  		});
	});
</script>
<!-- -------------------------------------------------------------------------------------------- -->
<style>
	/*#TB_window{
		margin-top: -270px !important;
	}*/
	.littletext {
		font-size: 10px;
	}
/* 	#TB_ajaxContent{ */
/* 		max-height: 400px !important; */
/* 	} */

	select {
	    margin-left: 0px;
	}
	.textlabel{
		width: 200px;
	}

	.ing_pro_name_row img {
    	cursor: pointer;
	}

	.tiny_txt{
		font-size: 10px;
	}
	.sub_div{
		border-color: #e3e3e3;
		border-style: solid;
		border-width: 1px 0 0;
	}
	.sub__div{
		text-align:right;
	}
	#s2id_sel_prod{
		min-width:400px;
	}
	.rel_p_f{
		display: block;
	    margin: 10px 0 !important;
	}
	.rel_p_f > select {
    	height: 25px;
    	min-width: 180px;
	}
	.more_img div {
	    border: thin solid -moz-cellhighlighttext;
    	margin-bottom: 10px;
    	margin-top: 10px;
	}

	.table > div {
	    padding: 15px;
	}
	#allergence_container > p{
		display:inline-block;
		height:25px !important;
		margin:0px !important;
	}
	.checkAller {
    	display:none;
	}
	.chk-active{
		border: medium solid red;
	}
	.img-check {
    	width: 50px;
	}
</style>
<link href="<?php echo base_url()?>assets/cp/css/qtip.css" rel="stylesheet" type="text/css">
<script src="<?php echo base_url();?>assets/cp/new_js/edit_group.js?version=<?php echo version;?>" language="javascript"></script>
<script src="<?php echo base_url()?>assets/cp/js/jquery.tooltip.js?version=<?php echo version;?>"></script>
<script src="<?php echo base_url()?>assets/cp/new_js/kcp_prod_new_custom.js?version=<?php echo version;?>"></script>
<div id="thick_bg"></div>
<div id="loadingmessage" style="display:none;margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;">
  <img src="<?php echo base_url(''); ?>assets/cp/images/ajax-loading.gif" style="position: absolute; color: White; top: 50%; left: 45%;"/>
</div>

<!-- MAIN -->
<div id="main">
	<div id="main-header">
		<h2><?php if($product_information): echo _('UPDATE PRODUCT');else: echo _('ADD PRODUCT'); endif;?></h2>
  		<span class="breadcrumb"><a href="<?php echo base_url()?>cp"><?php echo _('Home')?></a> &raquo; <a href="<?php echo base_url()?>cp/products"><?php echo _(' Products')?></a> &raquo;
  			<?php if($product_information): echo _('update product');else: echo _('add product'); endif;?>
  		</span>
	</div>

	<div id="content">
		<div id="content-container">
			<!--<form action="<?php echo base_url();?>cp/cdashboard/products_addedit" enctype="multipart/form-data" method="post" id="frm_products_addedit" name="frm_products_addedit">-->
			<?php if($product_information):?>
				<!--<input type="hidden" value="<?php echo $product_information['0']->id?>" name="prod_id" class="prod_id" >-->
			<input type="hidden" value="<?php echo $product_information['0']->direct_kcp?>" name="direct_kcp">
			<?php else:?>
				<input type="hidden" value="" name="prod_id" class="prod_id" >
			<?php endif;?>
	    	<div <?php if($this->session->flashdata('webshop')){?>class="boxed"<?php }else{?>class="box"<?php }?>>
				<h3 id="product_info"> <?php echo _('Product information ')?></h3>
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
					            <td style="padding-right:250px" colspan="2"><input type="text" class="text medium" size="30" id="proname" name="proname" <?php if($product_information):?>value="<?php echo stripslashes($product_information['0']->proname)?>"<?php endif;?> <?php if($this->session->userdata('menu_type') == 'fooddesk_light'){if(isset($product_information)){if(!empty($product_information)){ if($product_information['0']->direct_kcp == 1 ){if(!isset($fixed_pdf)){?>style="background:pink"<?php }}}}}?>></td>
					        </tr>
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
			                	<td style="padding-right:250px" id="current_prod_img">
			                	<?php if($product_information['0']->image) { ?>
			                		<img src="<?php echo base_url(''); ?>assets/cp/images/product/<?php echo $product_information['0']->image;?>" alt="<?php echo _('No product image available.Please upload one.')?>" style="height:300px"/>
			                  		<a href="#" class="remove_image" rel="<?php echo $product_information['0']->id;?>"><?php echo _('Remove'); ?></a>
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
      			<h3 id="allergen"> <?php echo _('Allergen')?></h3>
      			<div style="padding: 0px; display: block;" class="inside">
        			<div class="table">
        				<table border="0">
			       			<tbody>
			       			<div id="allergence_container">
								<?php $pro_aller = array();if(!empty($product_information) && ($product_information['0']->allergence != '')){ $k =0 ;
									$allr_exploded_val = explode("#", $product_information['0']->allergence);
									foreach ($allr_exploded_val as $val){
										$pro_aller[]= $val;
									}}?>
									<p>
										<img src="<?php echo base_url()?>assets/cp/allergence_images/3.png" class="img-check <?php if(!empty($pro_aller)){ if(in_array('3', $pro_aller)){echo "chk-active";}}?>">
										<input type="checkbox" name="allergence_arr[]" value="3" class="checkAller" <?php if(!empty($pro_aller)){ if(in_array('3', $pro_aller)){echo "checked='true'";}}?>/>
									</p>
									<p>
										<img src="<?php echo base_url()?>assets/cp/allergence_images/1.png" class="img-check <?php if(!empty($pro_aller)){ if(in_array('1', $pro_aller)){echo "chk-active";}}?>">
										<input type="checkbox" name="allergence_arr[]" value="1" class="checkAller" <?php if(!empty($pro_aller)){ if(in_array('1', $pro_aller)){echo "checked='true'";}}?> style="background-image:url(1.png);"/>
									</p>
									<?php /*<p>
										<img src="<?php echo base_url()?>assets/cp/allergence_images/15.png" class="img-check <?php if(!empty($pro_aller)){ if(in_array('15', $pro_aller)){echo "chk-active";}}?>">
										<input type="checkbox" name="allergence_arr[]" value="15" class="checkAller" <?php if(!empty($pro_aller)){ if(in_array('15', $pro_aller)){echo "checked='true'";}}?> style="background-image:url(15.png);"/>
									</p>*/?>
									<p>
										<img src="<?php echo base_url()?>assets/cp/allergence_images/13.png" class="img-check <?php if(!empty($pro_aller)){ if(in_array('13', $pro_aller)){echo "chk-active";}}?>">
										<input type="checkbox" name="allergence_arr[]" value="13" class="checkAller" <?php if(!empty($pro_aller)){ if(in_array('13', $pro_aller)){echo "checked='true'";}}?> style="background-image:url(13.png);"/>
									</p>
									<p>
										<img src="<?php echo base_url()?>assets/cp/allergence_images/7.png" class="img-check <?php if(!empty($pro_aller)){ if(in_array('7', $pro_aller)){echo "chk-active";}}?>">
										<input type="checkbox" name="allergence_arr[]" value="7" class="checkAller" <?php if(!empty($pro_aller)){ if(in_array('7', $pro_aller)){echo "checked='true'";}}?> style="background-image:url(7.png);"/>
									</p>
									<p>
										<img src="<?php echo base_url()?>assets/cp/allergence_images/10.png" class="img-check <?php if(!empty($pro_aller)){ if(in_array('10', $pro_aller)){echo "chk-active";}}?>">
										<input type="checkbox" name="allergence_arr[]" value="10" class="checkAller" <?php if(!empty($pro_aller)){ if(in_array('10', $pro_aller)){echo "checked='true'";}}?> style="background-image:url(10.png);"/>
									</p>
									<p>
										<img src="<?php echo base_url()?>assets/cp/allergence_images/8.png" class="img-check <?php if(!empty($pro_aller)){ if(in_array('8', $pro_aller)){echo "chk-active";}}?>">
										<input type="checkbox" name="allergence_arr[]" value="8" class="checkAller" <?php if(!empty($pro_aller)){ if(in_array('8', $pro_aller)){echo "checked='true'";}}?> style="background-image:url(8.png);"/>
									</p>
									<div class="clearfix"></div>
									<p>
										<img src="<?php echo base_url()?>assets/cp/allergence_images/5.png" class="img-check <?php if(!empty($pro_aller)){ if(in_array('5', $pro_aller)){echo "chk-active";}}?>">
										<input type="checkbox" name="allergence_arr[]" value="5" class="checkAller" <?php if(!empty($pro_aller)){ if(in_array('5', $pro_aller)){echo "checked='true'";}}?> style="background-image:url(5.png);"/>
									</p>
									<p>
										<img src="<?php echo base_url()?>assets/cp/allergence_images/2.png" class="img-check <?php if(!empty($pro_aller)){ if(in_array('2', $pro_aller)){echo "chk-active";}}?>">
										<input type="checkbox" name="allergence_arr[]" value="2" class="checkAller" <?php if(!empty($pro_aller)){ if(in_array('2', $pro_aller)){echo "checked='true'";}}?> style="background-image:url(2.png);"/>
									</p>
									<p>
										<img src="<?php echo base_url()?>assets/cp/allergence_images/9.png" class="img-check <?php if(!empty($pro_aller)){ if(in_array('9', $pro_aller)){echo "chk-active";}}?>">
										<input type="checkbox" name="allergence_arr[]" value="9" class="checkAller" <?php if(!empty($pro_aller)){ if(in_array('9', $pro_aller)){echo "checked='true'";}}?> style="background-image:url(9.png);"/>
									</p>
									<p>
									<img src="<?php echo base_url()?>assets/cp/allergence_images/11.png" class="img-check <?php if(!empty($pro_aller)){ if(in_array('11', $pro_aller)){echo "chk-active";}}?>">
										<input type="checkbox" name="allergence_arr[]" value="11" class="checkAller" <?php if(!empty($pro_aller)){ if(in_array('11', $pro_aller)){echo "checked='true'";}}?> style="background-image:url(11.png);"/>
									</p>
									<p>
									<img src="<?php echo base_url()?>assets/cp/allergence_images/6.png" class="img-check <?php if(!empty($pro_aller)){ if(in_array('6', $pro_aller)){echo "chk-active";}}?>">
										<input type="checkbox" name="allergence_arr[]" value="6" class="checkAller" <?php if(!empty($pro_aller)){ if(in_array('6', $pro_aller)){echo "checked='true'";}}?> style="background-image:url(6.png);"/>
									</p>
									<p>
									<img src="<?php echo base_url()?>assets/cp/allergence_images/12.png" class="img-check <?php if(!empty($pro_aller)){ if(in_array('12', $pro_aller)){echo "chk-active";}}?>">
										<input type="checkbox" name="allergence_arr[]" value="12" class="checkAller" <?php if(!empty($pro_aller)){ if(in_array('12', $pro_aller)){echo "checked='true'";}}?> style="background-image:url(12.png);"/>
									</p>
									<div class="clearfix"></div>
									<p>
									<img src="<?php echo base_url()?>assets/cp/allergence_images/4.png" class="img-check <?php if(!empty($pro_aller)){ if(in_array('4', $pro_aller)){echo "chk-active";}}?>">
										<input type="checkbox" name="allergence_arr[]" value="4" class="checkAller" <?php if(!empty($pro_aller)){ if(in_array('4', $pro_aller)){echo "checked='true'";}}?> style="background-image:url(4.png);"/>
									</p>
									<p>
									<img src="<?php echo base_url()?>assets/cp/allergence_images/14.png" class="img-check <?php if(!empty($pro_aller)){ if(in_array('14', $pro_aller)){echo "chk-active";}}?>">
										<input type="checkbox" name="allergence_arr[]" value="14" class="checkAller" <?php if(!empty($pro_aller)){ if(in_array('14', $pro_aller)){echo "checked='true'";}}?> style="background-image:url(14.png);"/>
									</p>
									<p class="ffd">
										<span>
											<input type="checkbox" <?php if( isset( $pro_aller ) && sizeof( $pro_aller ) == 0){ echo 'checked'; } ?>>
											<?php echo _( "Free of allergens" ); ?>
										</span>
									</p>
									<div class="clearfix"></div>
								</div>
			       			</tbody>
			       		</table>
			       		 <?php if($product_information):?>
							<div class="sub_div">
							    <div class="sub__div" colspan="2">
							        <input type="button" value="<?php echo _("Update");?>" class="submit" id="allergence_update" name="allergence_update">
							        <input type="hidden" value="add_edit" id="allergence_act" name="allergence_act">
							    	<input type="hidden" value="update" id="allergence_add_update" name="allergence_add_update">
							    	<input type="button" value="<?php echo _('Save & next')?>" class="submit" id="allergence_info_savenext" name="allergence_info_savenext">
								</div>
							</div>
							<?php else:?>
							<div class="sub_div">
							    <div class="sub__div" colspan="2">
							        <input type="button" value="<?php echo _('Send')?>" class="submit" id="allergence_add" name="allergence_add">
							      	<input type="hidden" value="add" id="allergence_add_update" name="allergence_add_update">
							      	<input type="button" value="<?php echo _('Save & next')?>" class="submit" id="allergence_info_savenext" name="allergence_info_savenext">
							   	</div>
							</div>
						<?php endif;?>
        			</div>
        		</div>
        	</div>
	    	<div class="boxed">
      			<h3 id="recipe"> <?php echo _('Recipe')?></h3>
      			<div style="padding: 0px; display: block;" class="inside">
        			<div class="table">
        				<table border="0">
			       			<tbody>
			       				<tr>
			       				<td colspan="2"><p class="cal_recipe"><b><?php echo _('Upgrade now and FoodDESK will gather all and update those sheets for you!');?><span><?php echo _('You can testdrive for');?> <b id="count_recipe"><?php echo (isset($count_left))?$count_left:5;?></b> <?php echo _('times');?></span></b></p></td>
			       				</tr>
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
				                		<img src="<?php echo base_url().'assets/cp/images/select_list_h.gif'; ?>" onclick="reset_wt()">
				                	<?php } ?>

				                	&nbsp;&nbsp;&nbsp;
				                	<span><?php echo _('Important: only with hot dishes, the weight must be weighted when it\'s hot (not cold)'); ?></span>
				                </td>
				            </tr>

							<tr id="recipe_contains_tr" <?php if(isset($product_information) && !empty($product_information) && $product_information[0]->direct_kcp == 1){ echo 'style="display: none"'; }?>>
				                <td class="textlabel">
				                </td>
				                <td>
				                	<div id="fdd_tools" <?php if(isset($product_information) && !empty($product_information)){ }else{  }?>>
			              				<br/>

					              		<?php if($fdd_credits > 0){?>

						              		<?php if(isset($product_information) && !empty($product_information)){?>
						              			<a href="<?php echo base_url().'cp/fooddesk/fdd_own_products_all/'.$product_information['0']->id.'?height=300&width=900'?>" title="<?php echo _('Add Recipe of PRODUCT ');  echo $product_information['0']->proname; ?>" class="thickbox"><?php echo _('FoodDESK/Own products')?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						              		<?php }else{?>
						              			<a href="<?php echo base_url().'cp/fooddesk/fdd_own_products_all?height=300&width=900'?>" title="<?php echo _('Add FoodDESK or Own products');?>" class="thickbox"><?php echo _('FoodDESK/Own products')?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						              		<?php }?>

					              		<?php }else{?>
					              			<a href="#TB_inline?height=300&width=500&inlineId=credit_require" title="<?php echo _('No credit left!');?>" class="thickbox"><?php echo _('FoodDESK/Own products')?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					              		<?php }?>
				              		</div>

				              		<div style="width: 100%; <?php if(isset($product_information)){if(!empty($product_information)){ if($product_information['0']->direct_kcp == 1 && ($product_information['0']->fdd_producer_id != 0 || $product_information['0']->fdd_supplier_id != 0)) { echo 'display:none;';  }}} ?>" >
			              				<table border="0" class="override" >
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
	                                						<?php }?>
		                                				<?php }else{ ?>
		                                					<input type="text" style="width:100%" class="text product_name_text" value="<?php echo stripslashes($fdd_info['p_name'.$sel_lang.'']); ?>" disabled>
		                                				<?php } ?>
		                                			</td>
		                                			<td width="3%" ></td>
		                                			<td width="12%">

			                                			<?php

// 			                                			if($fdd_info['quantity'] > 1){
// 															$fdd_quant = round($fdd_info['quantity'],0);
// 														}else{
															$fdd_quant = str_replace($search, $replace,round($fdd_info['quantity'],1));
//														}
			                                			?>
		                                				<input type="text" class="text fdd_product_quants" value="<?php echo $fdd_quant;?>" onkeyup="quant_change(this)" style="width: 60%; padding-bottom: 7px; margin-top: 5px;"><strong> <?php echo $fdd_info['unit'];?> </strong>
		                                				<input type="hidden" class="fdd_product_hidden_id" value="<?php echo $fdd_info['fdd_pro_id'];?>" >
		                                				<input type="hidden" class="ing_pro_name" value="<?php echo $fdd_info['p_name'.$sel_lang.''].'--'.$fdd_info['s_name'].'--EAN:'.$fdd_info['barcode'].'--PLU:'.$fdd_info['plu'];?>" >
		                                			</td>
		                                			<td width="4%" style="vertical-align: middle;">
														<img src="<?php echo base_url();?>assets/cp/images/Delete.gif" style="width: 20px;" onclick="remove_this_fdd_pro(<?php echo $fdd_info['fdd_pro_id'];?>)" />
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
																<?php 
																}else{ ?>
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
							                    				ing_datas.push({id:"<?php echo $product_ingredient->ki_name; ?>",text:"<?php echo $product_ingredient->ki_name; ?>"});
							                 				<?php }?>

						                    			<?php }else{?>
						                    				var str = "<?php if($product_ingredient->prefix == ''){ echo $product_ingredient->ki_name; }else{ echo $product_ingredient->ki_name.' ('.$product_ingredient->prefix.')';};?>";
							                    			var combine_id = "<?php echo $product_ingredient->prefix.'#'.$product_ingredient->ki_name.'#'.$product_ingredient->ki_id.'#'.$product_ingredient->kp_id.'#'.$product_ingredient->is_obs_ing.'#'.$key; ?>";
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
	                                    </tbody>
	                                </table>
			              		</div>

			              		<div style="width: 100%; <?php if(isset($product_information)){if(!empty($product_information)){ if($product_information['0']->direct_kcp == 1 && ($product_information['0']->fdd_producer_id != 0 || $product_information['0']->fdd_supplier_id != 0)) { echo 'display:none;';  }}} ?>" >
			              			<table border="0" class="override" >
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
		                                			<td width="3%"></td>
		                                			<td width="12%">
			                                			<?php
// 			                                			if($own_pro_info['quantity'] > 1){
// 															$own_quant1 = round($own_pro_info['quantity'],0);
// 														}else{
															$own_quant1 = str_replace($search, $replace,round($own_pro_info['quantity'],1));
//														}
			                                			?>
		                                				<input type="text" class="text own_product_quants" value="<?php echo $own_quant1;?>" onkeyup="quant_change(this)" style="width: 60%; padding-bottom: 7px; margin-top: 5px;"><strong> <?php echo $own_pro_info['unit'];?> </strong>
		                                				<input type="hidden" class="fdd_product_hidden_id" value="<?php echo $own_pro_info['fdd_pro_id'];?>" >
		                                				<input type="hidden" class="ing_pro_name" value="<?php echo $own_pro_info['proname'].' '.'--'.' '.$own_pro_info['s_name'];?>" >
		                                			</td>
		                                			<td width="4%" style="vertical-align: middle;">
														<img src="<?php echo base_url();?>assets/cp/images/Delete.gif" style="width: 20px;" onclick="remove_this_fdd_pro(<?php echo $own_pro_info['fdd_pro_id'];?>)" />
													</td>
													<td width="4%" style="vertical-align: middle;">

		                                			</td>
		                                			<td width="37%" style="vertical-align: middle;">

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
			              			<p style="padding-top: 25px;"><b><?php echo _('Mark this product as semi-product')?></b>&nbsp;&nbsp;&nbsp;<input type="checkbox" name="is_custom_semi" id="is_custom_semi" <?php if(isset($product_information)){if(!empty($product_information)){if($product_information['0']->semi_product){echo 'checked="checked"';}}}?> value="1"></p>
			              			<h1 id="is_semi_alert" style="color:red;"></h1>
				            	</td>
				            </tr>

				               <tr id="recipe_method_tr" <?php if(isset($product_information) && !empty($product_information) && $product_information[0]->direct_kcp == 1){ echo 'style="display: none"'; }?>>
				                  <td class="textlabel">
				                	<?php echo _('How to make')?> :
				                  </td>
				               	  <td>
				               	  	<textarea id="recipe_method_txt" name="recipe_method_txt" rows="5" cols="80"><?php if($product_information){ echo $product_information['0']->recipe_method; };?></textarea>
				               	  </td>
				               </tr>

			              		<tr id="ing_container"  >
			              			<td class="textlabel">
			              				<?php echo _("Ingredients");?> :
			              			</td>
			              			<td>
			              			<?php if( $this->session->userdata('menu_type') == 'fooddesk_light'){?>
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
					              	<?php if(  $this->session->userdata('menu_type') == 'fooddesk_light'){?>
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
			                    					<?php $conuter = 0;?>
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
																<td width="5%" style="text-align:right;">
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

						                    						var str = '<?php echo '(';?>';
			                    									var combine_id = '<?php echo '#(#0#'.$product_allergence->kp_id.'#'.$product_allergence->ka_id;?>';
						                    						allg_datas.push({id:combine_id,text:stripslashes(str)});
						                    				<?php if(!empty($product_sub_allergences)){?>
								                    				<?php foreach ($product_sub_allergences as $product_sub_allergence){?>
					                    						<?php if(($product_sub_allergence->kp_id == $product_allergence->kp_id) && ($product_allergence->ka_id == $product_sub_allergence->parent_ka_id)){?>
								                    					var str = "<?php echo $product_sub_allergence->sub_ka_name;?>";
								                    					var combine_id = "<?php echo '#'.$product_sub_allergence->sub_ka_name.'#'.$product_sub_allergence->sub_ka_id.'#'.$product_sub_allergence->kp_id.'#'.$product_sub_allergence->parent_ka_id;?>";
								                    					allg_datas.push({id:combine_id,text:stripslashes(str)});
						                    				<?php }}}?>
						                    						var str = '<?php echo ')';?>';
			                    									var combine_id = '<?php echo '#)#0#'.$product_allergence->kp_id.'#'.$product_allergence->ka_id;?>';
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
			              		<?php if( $this->session->userdata('menu_type') == 'fooddesk_light'){?>
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
			                    					<?php if(!$product_trace->kp_id){
				                    					if($product_trace->kt_name == '('){ ?>
				                    						$('#lp_count_t').val(parseInt($('#lp_count_t').val()) + 1);
				                    						traces_datas.push({id:'lp#'+parseInt($('#lp_count_t').val()),text:'. ( .'});
				                    					<?php }else if($product_trace->kt_name == ')'){ ?>
				                    						$('#rp_count_t').val(parseInt($('#rp_count_t').val()) + 1);
				                    						traces_datas.push({id:'rp#'+parseInt($('#rp_count_t').val()),text:'. ) .'});
				                    					<?php }else{ ?>
				                    						traces_datas.push({id:"<?php echo $product_trace->kt_name; ?>',text:'<?php echo $product_trace->kt_name; ?>"});
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

			            <?php if( $this->session->userdata('menu_type') == 'fooddesk_light'){?>
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
			              				<td><?php echo _("Energy Value (Kcal)");?></td>
			              				<td id="e_val_1"><?php if(isset($nutri_values) && !empty($nutri_values)){ echo defined_money_format($nutri_values['e_val_1'],0); } ?></td>
			              				<td id="e_val_1_x"><?php if(isset($nutri_values) && !empty($nutri_values)){ echo defined_money_format($nutri_values['e_val_1']/100*$recipe_wt,0); } ?></td>
			              			</tr>
			              			<tr>
			              				<td><?php echo _("Energy Value (KJ)");?></td>
			              				<td id="e_val_2"><?php if(isset($nutri_values) && !empty($nutri_values)){ echo defined_money_format($nutri_values['e_val_2'],0); } ?></td>
			              				<td id="e_val_2_x"><?php if(isset($nutri_values) && !empty($nutri_values)){ echo defined_money_format($nutri_values['e_val_2']/100*$recipe_wt,0); } ?></td>
			              			</tr>
			              			<tr>
			              				<td><?php echo _("Proteins (gm)");?></td>
			              				<td id="proteins"><?php if(isset($nutri_values) && !empty($nutri_values)){ echo defined_money_format($nutri_values['protiens'],1); } ?></td>
			              				<td id="proteins_x"><?php if(isset($nutri_values) && !empty($nutri_values)){ echo defined_money_format($nutri_values['protiens']/100*$recipe_wt,1); } ?></td>
			              			</tr>
			              			<tr>
			              				<td>
			              					<p><?php echo _("Carbohydrates (gm)");?></p></br>
			              					<p>&nbsp;&nbsp;&nbsp;<?php echo '-'._("Sugar (gm)");?></p></br>
			              					<p>&nbsp;&nbsp;&nbsp;<?php echo '-'._("Polyolen (gm)");?></p></br>
			              					<p>&nbsp;&nbsp;&nbsp;<?php echo '-'._("Farina (gm)");?></p>
			              				</td>
			              				<td>
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
			              					<p>&nbsp;&nbsp;&nbsp;<?php echo '-'._("Saturated Fats (gm)");?></p></br>
			              					<p>&nbsp;&nbsp;&nbsp;<?php echo '-'._("Single Unsaturated Fats (gm)");?></p></br>
			              					<p>&nbsp;&nbsp;&nbsp;<?php echo '-'._("Multi Unsaturated Fats (gm)");?></p>
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
					              	<td id="fats"><img src="<?php echo base_url();?>assets/images/pdf2.jpeg"><a href="<?php echo  $this->config->item('fdd_url').'assets/cp/uploads/'.$fixed_pdf;?>"> <?php echo $fixed_pdf;?></a></td>
				              	</tr>
				          	<?php }?>
			        		</tbody>
			        	</table>
        			</div>
        			<?php if($product_information):?>
					<div class="sub_div" style="padding: 15px;">
			    		<div class="sub__div" colspan="2">
			        		<input type="button" value="<?php echo _("Update");?>" class="submit" id="recipe_update" name="recipe_update" <?php if(isset($count_left) && $count_left == 0){echo 'disabled="disabled"';}?>>
			        		<input type="hidden" value="add_edit" id="recipe_act" name="recipe_act">
			    			<input type="hidden" value="update" id="recipe_add_update" name="recipe_add_update">
						</div>
					</div>
					<?php else:?>
					<div class="sub_div" style="padding: 15px;">
			    		<div class="sub__div" colspan="2">
			        		<input type="button" value="<?php echo _('Send')?>" class="submit" id="recipe_add" name="recipe_add" <?php if(isset($count_left) && $count_left == 0){echo 'disabled="disabled"';}?>>
			      			<input type="hidden" value="add" id="recipe_add_update" name="recipe_add_update">
			   			</div>
					</div>
					<?php endif;?>
        		</div>
        	</div>

			<input type="hidden" value="" id="hidden_fdds_quantity" name="hidden_fdds_quantity">
		    <input type="hidden" value="" id="hidden_own_pro_quantity" name="hidden_own_pro_quantity">
		    <input type="hidden" value="0" id="hidden_fdd_total" name="hidden_fdd_total">
			<input type="hidden" value="0" id="hidden_own_total" name="hidden_own_total">
		<!--</form>-->
	</div>
</div>
<?php if($product_information){?>
        <input type="hidden" value="<?php echo $product_information['0']->id?>" name="prod_id" class="prod_id" >
   <?php }?>
<!-- /content -->
<script>
	/* FOR SORTABLE */
	var fixHelper = function(e, ui) {
		ui.children().each(function() {
			$(this).width($(this).width());
		});
		return ui;
	};

	function open_tip(id){
		id = "#tooltip_"+id;
		$(id).addClass('myTooltipBlock');
		$(id).removeClass('myTooltipNone');
	}
	function close_tip(id){
		id = "#tooltip_"+id;
		$(id).addClass('myTooltipNone');
		$(id).removeClass('myTooltipBlock');
	}

	function select_all(id1,id2,counter){
		var isChecked = $('#'+id1).is(':checked');

		if(isChecked){
			for(var i =1;i <= parseInt(counter);i++){
				var id= id2+i;
				document.getElementById(id).checked = true;
			}
		}else{
			for(var i =1;i <= parseInt(counter);i++){
				var id= id2+i;
				document.getElementById(id).checked = false;
			}
		}
	}

  	jQuery(document).ready(function($) {
    	// producten
    	$('#help-prod2').tipsy({gravity: 'w'});
	    $('#help-prod3').tipsy({gravity: 'w'});
	    $('#help-prod4').tipsy({gravity: 'w'});
		$('.help-prod').tipsy({gravity: 'w'});

  		$('.remove_image').click(function(){

  	  		$.post('<?php echo base_url();?>cp/cdashboard/delete_image',{product_id: $(this).attr('rel') },function(response){
  	  	  		if(response.trim() == 'success'){
  	  	  	  		window.location.reload();
  	  	  	  	}else{
  	  	  	  		alert("<?php echo _('Image can not be deleted successfully');?>");
  	  	  	  	}
			});
  		});

  	   	// Sortable
		$(".sortable").sortable({
			helper: fixHelper,
			handle: '.handle',
			cursor: "move"
		});

		rename_product_short_name();
		update_fdd_pro_values();
  	});

  	function attach_ingredients(pro_id, all, ingi, traces){
  	  	alert(all);

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
<script type="text/javascript">
   	$('form#frm_products_addedit #add,form#frm_products_addedit #update').click(function(){
   		if(form_validate()){
			$('form#frm_products_addedit').submit();
       	}
    });

   	function form_validate(){
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

	function check_crop(){
		if($("#crop_button").length){
			alert("<?php echo _('Please crop image before adding/updating product else it will be distorted in shop');?>");
			return false;
		}else{
			return true;
		}
	}

	var discount_option = 0;
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
	}

	function upload_sheet(fdd_pro_id){
		tb_show("<?php echo _('New product sheet');?>", '#TB_inline?height=200&amp;width=300&amp;inlineId=pdf_uploader');
		$("#TB_ajaxContent #fdd_pro_pdf_id").val(fdd_pro_id);
	}

	$("#remark_mail_button").on("click",function(){
		var valid = true;
		var emailRegex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/,
			sender = $("#TB_ajaxContent").find("input[name='sender_name']"),
			subject = $("#TB_ajaxContent").find("input[name='sender_subject']"),
			message = $("#TB_ajaxContent").find("#sender_msg");
		var	prod_id = $(".prod_id");
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

	$('#rel_cat').change(function(){
		var cat_id = $(this).val();
		var subcat_id = -1;
		var cu_pid = ($('.prod_id').length > 0)?$('.prod_id').val():0;

		$.post(base_url+"cp/cdashboard/get_sub_category_product",
			{'cat_id':cat_id, 'subcat_id':subcat_id, 'cu_pid':cu_pid},
    		function(data){
	    		$("#rel_subcat option").remove();//this is to remove the previous values of drop down menu //
	    		$("#rel_subcat").append("<option value='-1' selected='selected'>-- <?php echo _('Select subcategory');?> --</option>");
	    		$.each(data.subcat,function(index,element){
	    			$("#rel_subcat").append($("<option></option>").val(index).html(element));
		    	});
	    		$("#rel_prod option").remove();//this is to remove the previous values of drop down menu //
	    		$("#rel_prod").append("<option value='-1' selected='selected'>-- <?php echo _('Select product');?> --</option>");
	    		$.each(data.product,function(index,element){
	    			$("#rel_prod").append($("<option></option>").val(index).html(stripslashes(element)));
		    	});
			},'json');
	});

	$('#rel_subcat').change(function(){
		var cat_id = $('#rel_cat').val();
		var subcat_id = $(this).val();
		var cu_pid = ($('.prod_id').length > 0)?$('.prod_id').val():0;

		$.post(base_url+"cp/cdashboard/get_sub_category_product",
				{'cat_id':cat_id, 'subcat_id':subcat_id, 'cu_pid':cu_pid},
    			function(data){
	    			$("#rel_prod option").remove();//this is to remove the previous values of drop down menu //
	    			$("#rel_prod").append("<option value='-1' selected='selected'>-- <?php echo _('Select product');?> --</option>");
	    			$.each(data.product,function(index,element){
	    				$("#rel_prod").append($("<option></option>").val(index).html(stripslashes(element)));
		    		});
				},'json');
	});

	$('#rel_prod').change(function(){
		pid = $(this).val();
		if(pid != -1){
			text = $(this).find('option:selected').text();

			var data = $("#sel_prod").select2('data');
			data.push({id:pid,text:text});
			$("#sel_prod").select2("data", data, true);
		}
	});

	$("#sel_prod").select2({
   		placeholder: "<?php echo _('selected products');?>",
   		separator:"#",
   		createSearchChoice:function(term, data) { if ($(data).filter(function() { return this.text.localeCompare(term)===0; }).length===0) {return {id:term, text:term};} },
       	multiple: true,
        tags: true
    });
	$("#sel_prod").select2("container").find("ul.select2-choices").sortable({
		containment: 'parent',
		start: function() { $("#sel_prod").select2("onSortStart"); },
		update: function() { $("#sel_prod").select2("onSortEnd"); }
	});

	<?php if(isset($product_information) && !empty($product_information[0]->related_products)):?>
		var preload_data = new Array();
    	<?php $p_count = 0;?>
    	<?php foreach ($rel_prod as $p):?>
			preload_data[<?php echo $p_count; ?>] = {'id':"<?php echo $p->id;?>",'text':"<?php echo stripslashes($p->proname);?>"};
    		<?php $p_count++;?>
    	<?php endforeach;?>
    	$('#sel_prod').select2('data', preload_data );
	<?php endif;?>
</script>
<script>
	function rotcw(obj) {
      		$("#uploaded_image").html('<img src="'+base_url+'assets/cp/images/loader.gif" alt=""/>');
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
      		$("#uploaded_image").html('<img src="'+base_url+'assets/cp/images/loader.gif" alt=""/>');

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
  		function srotcw(obj) {
			$.ajax({
					type:'POST',
					url: base_url+'cp/image_upload/rotate_uploaded_image',
					data:{src:$(obj).attr('data-img1'),angle:'cw'},
					success: function(response){
						$(obj).parent().children('a').eq(0).attr('data-img1',response);
						$(obj).parent().children('a').eq(1).attr('data-img2',response);
						$("#current_prod_img").children('img').replaceWith('<img id="suploaded_image" style="height:300px" src="'+base_url+"assets/temp_uploads/"+response+'"/>');
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
						$("#current_prod_img").children('img').replaceWith('<img id="suploaded_image" style="height:300px" src="'+base_url+"assets/temp_uploads/"+response+'"/>');
						$('.rotated_image_hid').val(response);
					},
				});
  		}



</script>
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
	<div id="upgrade_account" style="display:none;">
		<input type="hidden" name="company_id" id="company_id" value="<?php echo $company[0]->id; ?>" />
        <input type="hidden" name="current_ac_type_id" id="current_ac_type_id" value="<?php echo $curr_account_type->id; ?>" />
        <table cellspacing="8" id="pop-window">
	       	<tr>
	       	<td style="text-align: center; font-size: 17px;font-weight: bolder;">
          		<?php echo _("Calculate the alleregens, ingredients, nutritionvalues of your custom product in just 2 minutes? For only 40€ /mnth this featuree will be activated - Upgrade now!");?>
	          	</td>
          	</tr>
          	<tr>
          	<td style="text-align: center; font-size: 15px;font-weight: bolder;">
          	<a href="javascript:;" style="color: blue!important"><?php echo _("Video");?></a>
          	</td>
          	</tr>
          	<tr><td colspan="2" style="text-align:center;">
          		<a title="Upgrade Now" onclick="tb_show('<?php echo _("Upgrade Now")?>','TB_inline?height=400&amp;width=500&amp;inlineId=upgrade',null);" href="javascript:void(0);"><input type="button" name="upgrade_pop" id="upgrade_pop" class="pop-btn"  value="<?php echo _('Account Upgrade'); ?>" /></a>
          	</td></tr>
	    </table>
 	</div>

 	<div id="upgrade" style="display:none;">
		<script type="text/javascript">
	        $("#TB_window").css("background-color", "");
			$("#TB_title").css("background-color", "");
	    </script>
        <form method="post" action="<?php echo base_url(); ?>cp/cdashboard/upgrade" id="request_upg_form">
          	<input type="hidden" name="company_id" id="company_id" value="<?php echo $company[0]->id; ?>" />
          	<input type="hidden" name="current_ac_type_id" id="current_ac_type_id" value="<?php echo $curr_account_type->id; ?>" />
          	<table cellspacing="8" id="pop-window-thick">
             	<tr>
                	<td style="text-align:right;"><?php echo _('Please select a package'); ?></td>
                	<td style="text-align:left;">
                    	<?php if(!empty($account_types)) { ?>
                     	<select name="requested_ac_type_id" id="requested_ac_type_id" class="required">

                     	<?php foreach($account_types as $at) { ?>

                     		<?php //if($at->id > $company[0]->ac_type_id){?>
                     			<!-- <option value=" <?php echo $at->id; ?>"><?php echo $at->ac_title.' '._('Package').' ('.$at->ac_price.'&euro;/'._('month').')'; ?></option> -->
                     		<?php //}?>
                     		<?php if ($at->id == 5 || $at->id == 6){?>
                     			<option value=" <?php echo $at->id; ?>"><?php echo $at->ac_title.' '._('Package').' ('.$at->ac_price.'&euro;/'._('month').')'; ?></option>
                     		<?php } ?>
                     	<?php } ?>
                     	</select>
                     	<?php } ?>
                	</td>
             	</tr>
             	<tr>
                	<td colspan="2">
                		<iframe id="terms" src="<?php echo base_url();?>terms_conditions" ></iframe>
                	</td>
             	</tr>
             	<tr>
                	<td colspan="2" style="text-align:center;"><input type="checkbox" name="agree" id="agree" value="1" checked="checked" />&nbsp;<?php echo _('I agree with the terms & conditions.'); ?></td>
             	</tr>
          		<tr><td colspan="2" style="text-align:center;"><input type="submit" name="upgrade_package" id="upgrade_package" onClick="return validation(this);" class="pop-btn" value="<?php echo _('Account Upgrade'); ?>" /></td></tr>
	    	</table>
		</form>
    </div>