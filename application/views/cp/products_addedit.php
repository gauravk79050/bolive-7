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

var move_to_sp_txt = '<?php echo _('Are you sure want to move this product to semi products');?>';
var move_to_esp_txt = '<?php echo _('Are you sure want to move this product to extra semi products');?>';
var move_success = "<?php echo _('Successfully Moved');?>";
var move_fail = "<?php echo _('Can not be Moved');?>";
var do_diasble = 0;

	<?php if(isset($product_information)){if(!empty($product_information)){ if($product_information['0']->direct_kcp == 1 ) {    ?>
		do_diasble = 1;
	<?php } } } ?>

var upload="<?php echo _("Upload Image");?>";
var crop_region="<?php echo _('Please select a crop region then press submit.');?>";
var cropping="<?php echo _("Cropping");?>....";
var saving="<?php echo _('If you leave before saving, your changes will be lost.');?>";
var save_next="<?php echo _('Save & next')?>";
var refresh="<?php echo _('Please refresh the page again');?>";
var cat="<?php echo _('Select a categoy.');?>";
var pro_name="<?php echo _('please give the product name.');?>";
var image="<?php echo _('Image cannot be deleted successfully.');?>";
var discount="<?php echo _('Single digits at discount')?>";
var group_size="<?php echo _('In group size, the rate must have +0.00 or -0.00')?>";
var max_amt="<?php echo _('Maximum amount is less than minimum amount.'); ?>";
var product_updated = "<?php echo _("Product Information Updated");?>";
var product_added = "<?php echo _("Product Information Added");?>";
var recipe_updated = "<?php echo _("Recipe Updated");?>";
var recipe_added = "<?php echo _("Recipe Added");?>";
var labeler_updated = "<?php echo _("Labeler Updated");?>";
var labeler_added = "<?php echo _("Labeler Added");?>";
</script>

<script src="<?php echo base_url();?>assets/kcp/js/select2/select2.min.js?version=<?php echo version;?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/kcp/js/select2/select2.css?version=<?php echo version;?>" media="screen">
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/kcp/js/select2/select2.css" media="screen">

<link rel="stylesheet" href="<?php echo base_url();?>assets/css/ui/jquery.ui.all.css?version=<?php echo version;?>">
<!--<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.core.js"></script>
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.widget.js"></script>
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.sortable.js"></script>
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.position.js"></script>
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.draggable.js"></script>
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.droppable.js"></script>
-->

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


.unapproved td:nth-child(3) input{
	background: salmon;
}

.ui-state-active{

    border:0 !important;
}
 .ui-widget-content {
    background: #ffffff none repeat scroll 0 0 !important;
}
 #TB_ajaxContent {
  clear: both;
  height: 450px !important;
  line-height: 1.4em;
  overflow-x: hidden;
  overflow-y: scroll;
  padding: 2px 15px;
  text-align: left;
}
#TB_window {
	position: fixed;
	background: #ffffff;
	z-index: 102;
	color:#000000;
	display:none;
	border: 4px solid #525252;
	text-align:left;
	top:40%;
	left:50%;
}

.unapproved td:nth-child(3) input{
	background: salmon;
}
</style>
<script src="<?php echo base_url();?>assets/cp/new_js/jquery.Jcrop.js?version=<?php echo version;?>"></script>
<script src="<?php echo base_url();?>assets/cp/new_js/products_addedit.js?version=<?php echo version;?>"></script>

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
		padding:20px 60px 20px 20px;
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
	.rotate_div {
    position: absolute;
    left: -120px;
    top: 0;
}

#uploaded_image {
    position: relative;
}

</style>
<link href="<?php echo base_url()?>assets/cp/css/qtip.css" rel="stylesheet" type="text/css">
<script src="<?php echo base_url();?>assets/cp/new_js/edit_group.js?version=<?php echo version;?>" language="javascript"></script>
<script src="<?php echo base_url()?>assets/cp/js/jquery.tooltip.js?version=<?php echo version;?>"></script>

<?php if( $this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium'){?>
	<script src="<?php echo base_url()?>assets/cp/new_js/kcp_prod_new_custom.js?version=<?php echo version;?>"></script>
<?php }?>
<div id="loadingmessage" style="display:none;margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;">
  <img src="<?php echo base_url(''); ?>assets/cp/images/ajax-loading.gif" style="position: absolute; color: White; top: 50%; left: 45%;"/>
</div>
<!-- MAIN -->
<div id="main">
	<div id="main-header">
		<h2><?php if($product_information): echo _('UPDATE PRODUCT');else: echo _('ADD PRODUCT'); endif;?></h2>
  		<span class="breadcrumb"><a href="<?php echo base_url()?>cp/cdashboard/"><?php echo _('Home')?></a> &raquo; <a href="<?php echo base_url()?>cp/products/lijst"><?php echo _(' Products')?></a> &raquo;
  			<?php if($product_information): echo _('update product');else: echo _('add product'); endif;?>
  		</span>
	</div>
	<div id="content">
		<div id="content-container">
			<!--<form action="<?php echo base_url();?>cp/products/products_addedit" enctype="multipart/form-data" method="post" id="frm_products_addedit" name="frm_products_addedit">-->

<script type="text/javascript">
function rotateimage(obj){
	var rad=$(".radio:checked").val();
	$("#uploaded_image").html('<img src="'+base_url+'assets/cp/images/loader.gif" alt="'+cropping+'"/>');

	$.ajax({
					type:'POST',
					url: base_url+'cp/image_upload/rotate_image',
					data:{src:$(obj).attr('data-name'),angle:rad},
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
</script>


		<?php include 'product_information.php'; ?>
	    	<?php include 'recipe.php'; ?>
	    	<?php include 'labeler.php'; ?>
        	<?php include 'webshop.php'; ?>

<!-- /content -->

<script type="text/javascript">


	var discount_option = 0;

	<?php if($product_information && (isset($product_information['0']->discount) || isset($product_information['0']->discount_person))){?>
		<?php if ($product_information['0']->sell_product_option == 'per_unit' || $product_information['0']->sell_product_option == 'client_may_choose'){?>
				discount_option = '<?php echo $product_information['0']->discount; ?>';
		<?php }elseif($product_information['0']->sell_product_option == 'per_person'){?>
				discount_option = '<?php echo $product_information['0']->discount_person; ?>';
		<?php }?>
	<?php }?>

	if(discount_option == "multi"){
	<?php if(isset($product_information['0']->sell_product_option) && $product_information['0']->sell_product_option == 'per_person'){?>
			discount_show('2',false,true);
	<?php }else{?>
		discount_show('2');
	<?php }?>
	}else if(discount_option != ""){
			if(discount_option != 0){
		<?php if(isset($product_information['0']->sell_product_option) && $product_information['0']->sell_product_option == 'per_person'){?>
				discount_show('1',false,true);
		<?php }else{?>
				discount_show('1');
		<?php }?>
			}else{
				discount_show('0');
			}
		}else{
			discount_show('0');
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
	}

	function upload_sheet(fdd_pro_id){
		tb_show("<?php echo _('New product sheet');?>", '#TB_inline?height=200&amp;width=300&amp;inlineId=pdf_uploader');
		$("#TB_ajaxContent #fdd_pro_pdf_id").val(fdd_pro_id);
	}
	$(document).ready(function (){

		$("body").on("click","#remark_mail_button",function(){


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
					"<?php echo base_url()?>cp/products/send_remark_by_mail",
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

		$.post(base_url+"cp/products/get_sub_category_product",
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
					$str .= $ing->ki_name;
					$str .= ', ';
				}
				elseif($ing->ki_name == '(' ){
					$str = substr($str, 0, -2);
					$str .= $ing->ki_name;
					$str .= '  ';
				}
				elseif($ing->ki_name == ':' ){
				 	$str = substr($str, 0, -2);
					$str .= $ing->ki_name;
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
