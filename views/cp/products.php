<link rel="stylesheet" href="<?php echo base_url();?>assets/css/ui/jquery.ui.all.css">
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.core.js?version=<?php echo version;?>"></script>
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.widget.js?version=<?php echo version;?>"></script>
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.sortable.js?version=<?php echo version;?>"></script>
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.position.js?version=<?php echo version;?>"></script>

<script src="<?php echo base_url(); ?>assets/plugins/jquery-ui-1.11.2.custom/jquery-ui.min.js?version=<?php echo version;?>" type="text/javascript"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/jquery-ui-1.11.2.custom/jquery-ui.theme.min.css">

<style>
	.ui-autocomplete {
		max-height: 100px;
		overflow-y: auto;
	}
	#TB_ajaxContent{
		max-height: 400px !important;
	}
	
	#fdd_credits {
    color: red;
    text-align: right;
}
.fc-first th {
    background: none repeat scroll 0 0 black !important;
    border: medium none !important;
}	
#update_product_status {
    background: #d1ecb8 none repeat scroll 0 0;
    border: 1px solid #81c445;
    clear: both;
    line-height: 25px;
    margin: 0 0 25px;
    padding: 0 10px;
}
#shared_prod{
	float: left;
}

#com_cat{
	width: 100%;
}
#com_subcat{
	width: 100%;
}
#content-container1 > table tbody tr td {
    background: #EBF7C5 none repeat scroll 0 0;
}

#content-container1 > table {
    border-collapse: collapse;
    border-spacing: 0;
}
#content-container1{
	float: left;
    width: 100%;
    margin-bottom: 10px;
}

</style>
<script>
var availableTags2 = new Array();
var xhr;
$(function() {
	$.post(
		'<?php echo base_url();?>cp/cdashboard/get_product_name',
		{},
		function(response){
			availableTags = response; 
			$( "#prosearch" ).autocomplete({
				minLength: 0,
				source: availableTags,
				focus: function( event, ui ) {
					$( "#prosearch" ).val( ui.item.label );
					return false;
				},
				select: function( event, ui ) {
					window.location = base_url+"cp/cdashboard/products_addedit/product_id/"+ui.item.value;
					return false;
				}
			});
// 			.data( "autocomplete" )._renderItem = function( ul, item ) {
// 				return $( "<li></li>" )
// 				.data( "item.autocomplete", item )
// 				.append( "<a>" + item.label + "</a>" )
// 				.appendTo( ul );
// 			};

		},
		'json'	
	);
	$(".proreciname").keyup(function(e){
		if(e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40){
			show_reci_ingre_suggestion();
		}
	});

	/* FOR SORTABLE */
	var fixHelper = function(e, ui) {
		ui.children().each(function() {
			jQuery(this).width($(this).width());
		});
		return ui;
	};

	$(".sortable").sortable({
		helper: fixHelper,
		handle: '.handle',
		cursor: "move"
	});
	if($("#serach_opt").val() == 1 )
		change_search_type(1);
	else if($("#serach_opt").val() == 2 )
		change_search_type(2);
});

function show_reci_ingre_suggestion(){
	availableTags = [];
	
	var search_str = addslashes($('#prosearchreci').val());
				
	if(search_str.length > 1){
		$('#loding_gif').show();
		if(xhr && xhr.readystate != 4){
			xhr.abort();
		}
				        
		xhr = $.ajax({type:"POST",
				url: base_url+'cp/cdashboard/get_recipe_AjaxIngre/1',
				data: {
					'search_str': search_str
				},
				dataType: "json",
				success: function(response){
					$('#loding_gif').hide();
					availableTags2 = response;
					autocomplete_intializes();
				},
			});
	}else{
		autocomplete_intializes();
	}
}

function autocomplete_intializes(){
	$( "#prosearchreci" ).autocomplete({
		minLength: 0,
		appendTo: '#recipe_product',
		source: availableTags2,
		focus: function( event, ui ) {
			//$( "#prosearchreci" ).val( ui.item.label );
			return false;
		},
		select: function( event, ui ) {
			window.location = base_url+"cp/cdashboard/product_recipe/"+ui.item.value;
			return false;
		}
	})
	.data( "ui-autocomplete" )._renderItem = function( ul, item ) {
		return $( "<li></li>" )
		.data( "item.autocomplete", item )
		.append( item.label )
		.appendTo( ul );
	};

	$('#prosearchreci').autocomplete("search");
}
function set_checkbox_value(name,ctr,product_id){
	var checkbox = ctr.checked;
	if(checkbox == true){
		var value=ctr.value;
	}else if(checkbox == false){
		var value=0;
	}
	jQuery.post("<?php echo base_url()?>cp/cdashboard/update_checkbox",
		{'id':product_id,'key':name,'value':value},
		function(data){
			var shop_version = jQuery('#shop_version').val();
			if(shop_version == 2 || shop_version == 3){
	    		jQuery.post(
	    			base_url+"cp/shop_all/update_json_files/"+shop_version,
	    			{'action':'category_json'},
	    			function(data){},
	    			'json'
	    		);
    		}

			var infodesk_status = jQuery('#infodesk_status').val();
        	if(infodesk_status == 1){
        		jQuery.post(
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
        	
			jQuery('div#update_checkbox').html('<?php echo _('Your changes saved successfully.'); ?>');
			jQuery("#error_status").css("display","none");
			jQuery("#succeed_status").css("display","none");
			jQuery("#update_product_status").css("display","none");
		   	jQuery('div#update_checkbox').css({display:'block'});
		}
	);
}

function update_product_status(id_value, status_value, status_label, data_name){
	var $jq=jQuery.noConflict();	
	$jq.ajax({
		url:base_url+"cp/cdashboard/update_product_status",
		global: false,
		type: "POST",
		data: ({id : id_value, status:status_value, method:data_name}),
		dataType: "html",
		success: function(msg){
			var shop_version = $jq('#shop_version').val();
    		if(shop_version == 2 || shop_version == 3){
    			$jq.post(
	    			base_url+"cp/shop_all/update_json_files/"+shop_version,
	    			{'action':'category_json'},
	    			function(data){},
	    			'json'
	    		);
    		}

    		var infodesk_status = $jq('#infodesk_status').val();
        	if(infodesk_status == 1){
        		$jq.post(
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
			if(msg == "OK"){
				$jq("#update_product_status").css("display","none");
				$jq("#error_status").css("display","none");
				$jq("div#update_checkbox").css("display","none");
				$jq("#succeed_status").css("display","block");
				status_value_id = "#status_value_"+id_value;
				$jq(status_value_id).html(status_label);
			}else{
				$jq("#update_product_status").css("display","none");
				$jq("#succeed_status").css("display","none");
				$jq("div#update_checkbox").css("display","none");
				$jq("#error_status").css("display","block");
		   }
		   option_id = "#"+data_name+"_option_div_"+id_value;	  
		   show_id = "#show_"+id_value;
		   hide_id = "#hide_"+id_value;
		   if(status_value == "Show"){
			   $jq(show_id).removeClass('cm-ajax').addClass('cm-active');
			   $jq(hide_id).removeClass('cm-active').addClass('cm-ajax');
		   }else{
			   $jq(show_id).removeClass('cm-active').addClass('cm-ajax');
			   $jq(hide_id).removeClass('cm-ajax').addClass('cm-active');		  
		   }
		   $jq(option_id).hide('slow');
      }
   });
}

// Adding Keurslager Product
function add_prod_here(kp_id){
	var cat_id = jQuery('#categories_id').val();
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
		jQuery.post(
				base_url+'cp/fooddesk/add_k_prods',
				{
					'cat_id': cat_id,
					'subcat_id' : subcat_id,
					'kp_id' : kp_id
				},
				function(response){
					if(response){
						window.location = base_url+'cp/cdashboard/products_addedit/product_id/'+response;
					}else{
						alert("<?php echo _('This product cannot be added into current category. please try again later');?>");
					}
				}
			);
	}
}

function add_i_prod_here(ip_id){
	var cat_id = jQuery('#categories_id').val();
	if(cat_id == "-1"){
		alert("<?php echo _('Please select any category first');?>");
		self.parent.tb_remove();
		jQuery('#categories_id').focus();
	}else{
		var subcat_id = jQuery('#subcategories_id').val();
		jQuery.post(
				base_url+'cp/i_system/add_i_prods',
				{
					'cat_id': cat_id,
					'subcat_id' : subcat_id,
					'ip_id' : ip_id
				},
				function(response){
					if(response){
						window.location = base_url+'cp/cdashboard/products_addedit/product_id/'+response;
					}else{
						alert("<?php echo _('This product cannot be added into current category. Please try again later');?>");
					}
				}
			);
	}
}

function print_these(){
	var pro_ids = '';
	
	jQuery("input[name='ids[]']").each(function(){
		//pro_ids.push(jQuery(this).val());
		pro_ids += '.'+jQuery(this).val();
	});

	var cat_id = jQuery('#categories_id').val();
	var subcat_id = jQuery('#subcategories_id').val();
	
	window.location = '<?php echo base_url(); ?>cp/cdashboard/print_product/print_these/'+cat_id+'/'+subcat_id+'/'+pro_ids;
	/*
	var pro_ids = [];
	
	jQuery("input[name='ids[]']").each(function(){
		pro_ids.push(jQuery(this).val());
	});

	if(pro_ids.length > 0 ){
		jQuery.post(
					base_url+'cp/cdashboard/print_product',
					{
						'action' : 'print_these',
						'ids' : pro_ids
					},
					function(response){
						if(!response){
							alert('<?php echo _('Information could not be printed succesfully. Please try again later');?>');
						}
					}
				);
	}*/
}

function print_all(){
	window.location = '<?php echo base_url(); ?>cp/cdashboard/print_product/print_all';
	/*if(pro_ids.length > 0 ){
		jQuery.post(
					base_url+'cp/cdashboard/print_product',
					{
						'action' : 'print_all'
					},
					function(response){
						if(!response){
							alert('<?php echo _('Information could not be printed succesfully. Please try again later');?>');
						}
					}
				);
	}*/
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

function change_search_type(val){
	if(val == 1){
		$('#all_product').show();
		$('#recipe_product').hide();
	}else if(val == 2){
		$('#all_product').hide();
		$('#recipe_product').show();
	}
}

function addslashes(str) {
    str = str.replace(/\\/g, '\\\\');
    str = str.replace(/\'/g, '\\\'');
    str = str.replace(/\"/g, '\\"');
    str = str.replace(/\0/g, '\\0');
    return str;
}
</script>
<style type="text/css">
.inp_fld{
	background: none repeat scroll 0 0 #FCFCFC;
    border: 1px solid #CCCCCC;
    color: #333333;
    display: inline;
    padding: 5px;
}
#filtered_key_product{
	width: 30px;
	height: 30px;
	position: relative;
	top: 10px;
	cursor: pointer;
}
</style>
<script>
jQuery(document).ready(function($){
	$('#prosheet thead tr').live( 'click', '.checkall', function () {
		if($(this).find('input[type="checkbox"]').is(':checked')){
			var table_row=$('#prosheet').find('tbody').children('tr');
			table_row.each(function(){
				 if($(this).hasClass("odd"))
				 {
					 $(this).addClass('selected_row');
					 $('input:checkbox[class=check]').prop('checked', true);
				 }
			});
		}
		else{
			$('#prosheet tbody tr').removeClass('selected_row');
			$('input:checkbox[class=check]').prop('checked', false);
		}
	});

	$('#prosheet tbody tr .check').live( 'click', function () {
		if($(this).is(':checked')){
			$(this).parent().parent().addClass('selected_row');
		}
		else{
			$(this).parent().parent().removeClass('selected_row');
			$('#prosheet thead tr .checkall input[type="checkbox"]').prop('checked', false);
		}
    } );

	var products=<?php echo json_encode($products);?>;
	var products_arr_length=products.length;
	$('#remove').live('click', function(){
		 var remove_selected=new Array();
		 var select=false;
		 var table_data=$('#prosheet').find('tbody .check');
		 var confrm = confirm('Continue delete?');
		 	    if(confrm){
		 	    	 table_data.each(function(){
		 	         var $this = $(this);
		 	            if($this.is(':checked')) {
		 	            		remove_selected.push($(this).attr('value'));
		 	            	    select = true; 
		 	               	 	$this.parents('tr').fadeOut(function(){
		 	               	 	$this.remove();
		 	                });
		 	            }
		 	      });
	 	          if(!select){
			 	      alert("<?php echo _('No Option selected');?>");
		 	      }
	 	          else{
		 	          $('#loadmsg').show();
	 				  $.post(
	 						   base_url+'cp/cdashboard/delete_product',
	 						   {'remove_product_arr':remove_selected},
	 						   function(response){
	 							  $('#loadmsg').hide();
	 							   if(response.trim() != null){
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
										alert("<?php echo _("Product Removed Successfully");?>");
	 							   	}
	 						   	}
	 					);
		 	      	}
		 	  }
	});

	$('#save').live('click', function(){
		var pro_detail=new Array();
		var product_id=new Array();
		var product_id_list=new Array();
		var select=false;
		var table_pro_id=$('#prosheet').children('tbody').children('tr.odd');
		table_pro_id.each(function(){
			var product_id=$(this).children('td').children('input').val();
			product_id_list.push(product_id);
		});
		var table_data=$('#prosheet').find('tbody tr');
 	    table_data.each(function(){
	 	    var $this = $(this);
	 	    if($this.find('.check').is(':checked')) {
			     
	 			var table_body_row=$(this).children('td');
	 	    	var pro_id=table_body_row.eq(0).find('input').val();
	 	    	var pro_art_no=table_body_row.eq(2).children('input').next('input').val();
	 	    	var pro_name=table_body_row.eq(3).find('input').val();
	 	    	var pro_desc=table_body_row.eq(4).find('input').val();
	 	    	var pro_price_row=table_body_row.eq(5).find('input');
	 	    	pro_price_per_unit = '';
	 	    	pro_weight = '';
	 	    	pro_price_per_person = '';
	 	    	pro_price_row.each(function(){
	 	    		price_name = $(this).attr('name');
	 	    		if(price_name.indexOf('price_per_unit') != -1){
	 	    			pro_price_per_unit = $(this).val();
		 	    	}
	 	    		else if(price_name.indexOf('price_weight') != -1){
	 	    			pro_weight = $(this).val();
		 	    	}
	 	    		else if(price_name.indexOf('price_per_person') != -1){
	 	    			pro_price_per_person = $(this).val();
		 	    	}
	 	    		 //pro_detail.push($(this).val());
	 	    	 });

	 	    	 //pro_detail=[];
	 	         select = true; 
	 	         var product_values={
	 		 	 		'pro_id':pro_id,
	 		 	 		'pro_art_no':pro_art_no,
				        'pro_name':pro_name,
				        'pro_desc':pro_desc,
				        'pro_price_per_unit':pro_price_per_unit,
				 		'pro_weight':pro_weight,
				 		'pro_price_per_person' : pro_price_per_person
				 };
	 	         product_id.push(product_values);
	 	    }
		});
	 	if(!select){
			alert("<?php echo _('No Option selected !');?>");
		}
	 	else{
	 		$('#loadmsg').show();
		    $.post(
				base_url+'cp/cdashboard/products',
				{
				   'save':1,
				   'ids' :product_id,
				   'product_id_list':product_id_list
				},
				function(result_data){
				   	$('#loadmsg').hide();
				   	if(result_data.trim() != ""){
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
				        	
						  	$("div#update_checkbox").css("display","none");
						  	$("#error_status").css("display","none");
						  	$("#succeed_status").css("display","none");
						  	$("#update_product_status").css("display","block");
						  	var disable_checkall=$('#prosheet').find('thead .checkall').children('input');
						  	if(disable_checkall.is(':checked')){
							  disable_checkall.prop('checked', false);
							  var disable_check=$('#prosheet').find('.check');
							  disable_check.each(function(){
				 	           	if($(this).is(':checked')) {
				 	           		$(this).prop('checked', false);
				 	            }
							  });
						  }
						  else{
							  var disable_check=$('#prosheet').find('.check');
							  disable_check.each(function(){
				 	           	if($(this).is(':checked')) {
				 	           		$(this).prop('checked', false);
				 	            }
							  });
						}
						  
						$('#prosheet tbody').find('tr').each(function(){
							if(typeof $(this).find('td:eq(3)').children('input').val() != 'undefined'){
		    
				 	           		if($(this).find('td:eq(3)').children('input').val().trim() == '') {
				 	           			$(this).find('td:eq(3)').children('input').css('border','1px solid #ff0000');
				 	            	}
				 	           		else{
				 	           			$(this).find('td:eq(3)').children('input').attr('style','');
					 	           	
					 	         }
							}
						});
					}
				});
	 	 }
	});

	var anc_url=$('#technical_sheets').attr('data-link');
	
	$('#technical_sheets').live( 'click',function () {
		 var pro_id=new Array();
		 var select=false;
		 var table_data=$('#prosheet').find('tbody').children('tr');
		 if(pro_id.length == 0){
			 var pro_val=$('#technical_sheets').attr('data-link');
			 pro_id.push(anc_url);
		 }
		
		 table_data.each(function(){
			 if($(this).hasClass("selected_row")){
				 var product_id=$(this).children('td').children('input').attr('value');
				 select = true; 
				 if(pro_id[1] == undefined){
					 var href_new=pro_id[0]+product_id+"-";
	 	             $('#technical_sheets').attr('data-link',href_new);
	 	             pro_id.push(href_new);
				 }
				 else{
					 href_new=pro_id[pro_id.length-1]+product_id+"-";
					 pro_id.push(href_new);
					 $('#technical_sheets').attr('data-link',href_new);
				 }
			 }
 	      });
		 if(!select){
	 	     alert("<?php echo _('No Option selected !');?>");
	 	     $('#technical_sheets').attr('data-link','javascript:;');
	     }
		 else{
			 window.location.href = $('#technical_sheets').attr('data-link');
		}
	});

	var anc_url_new=$('#recipe_sheets').attr('data-link');
		
	$('#recipe_sheets').live( 'click',function () {
		 var pro_id=new Array();
		 var select=false;
		 var table_data=$('#prosheet').find('tbody').children('tr');
		 if(pro_id.length == 0){
			 var pro_val=$('#recipe_sheets').attr('data-link');
			 pro_id.push(anc_url_new);
		 }
		
		 table_data.each(function(){
			 if($(this).hasClass("selected_row")){
				 var product_id=$(this).children('td').children('input').attr('value');
				 select = true; 
				 if(pro_id[1] == undefined){
					 var href_new=pro_id[0]+product_id+"-";
	 	             $('#recipe_sheets').attr('data-link',href_new);
	 	             pro_id.push(href_new);
				 }
				 else{
					 href_new=pro_id[pro_id.length-1]+product_id+"-";
					 pro_id.push(href_new);
					 $('#recipe_sheets').attr('data-link',href_new);
				 }
			 }
 	      });
		 if(!select){
			 alert("<?php echo _('No Option selected');?>");
	 	     $('#recipe_sheets').attr('data-link','javascript:;');
	      }
		 else{
			 window.location.href = $('#recipe_sheets').attr('data-link');
		}
	});
	
	$('.dat_format_export').live('click', function(){
		$('.loadimg').show();
		$.post(base_url+'cp/fooddesk/dat_format_export/',{}, 
			function(response){
			$('.loadimg').hide();
			if(response.trim() !== '' ){
				window.location.href = base_url+'cp/fooddesk/download_dat_file/'+response;
			} 
		});
	});
	<?php if($this->session->userdata('login_via') == 'mcp'){?>
	$('.inp_fld').blur(function(){
		field_arr = $(this).attr('name').substring($(this).attr('name').lastIndexOf('_'));
		product_id = field_arr.split('_')[1];
		field = $(this).attr('name').split(field_arr)[0];
		value = $(this).val();
		obj = $(this);
		$('#loadmsg').show();
		$.post(
			base_url+'cp/cdashboard/rename_product',
			{"field_val":value,"product_id":product_id,"field":field},
			function(data){
				$('#loadmsg').hide();
				if(data.trim() != product_id)
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
	
	$('#filtered_key_product').live('click', function(){
		var product_keyword=$("#prosearch").val().trim();
		if(product_keyword != '')
		window.location="<?php echo base_url();?>cp/cdashboard/products/filtered_product/"+product_keyword;
	});
	
});
function close_this_noti(noti_id){
	var id = parseInt(noti_id.substring(5));
	$.ajax({
		url:'<?php echo base_url()?>cp/cdashboard/close_notification/'+id,
		dataType: 'json',
		success:function(response){
			
		}
	});
	$("#"+noti_id).parent('div').hide('slow');
}
function get_subcat_list(cat_id,obj){
	$.post(
		base_url+'cp/cdashboard/get_subcategory',
		{'catid': cat_id},
		function(result_data){
			var arr=JSON.parse(result_data);
			arr = arr.success;
			if(Array.isArray(arr)){
				var new_html = '';
				new_html += "<option value='-1'>-- <?php echo _('Select Subcategory')?> --</option>";
				for(var i = 0; i < arr.length; i++){
					new_html += '<option value="'+arr[i].id+'">'+arr[i].subname+'</option>';
				}
				$(obj).parent('td').next('td').children('select').html(new_html);
			}
			else{
				$("#comp_subcategories_id").html("");
				var new_html = '';
				new_html += "<option value='-1'>-- <?php echo _('Select Subcategory')?> --</option>";
				$(obj).parent('td').next('td').children('select').html(new_html);
			}
		}
	);
}
function assign_share_prod(obj){
	var parent_product_id = $(obj).parents().eq(1).attr('id');
	var cat_id = $(obj).parents().eq(1).children('td').eq(1).children('select').val();
	var sub_cat_id = $(obj).parents().eq(1).children('td').eq(2).children('select').val();
	var product_name = $(obj).parents().eq(1).children('td').eq(0).text();
	if(cat_id != -1){
		$.post(
			base_url+'cp/shared/assign_share_product',
			{'parent_product_id': parent_product_id,'cat_id':cat_id,'sub_cat_id':sub_cat_id,'product_name':product_name},
			function(result_data){
				if($(obj).parents().eq(2).children().length == 3){
					$(obj).parents().eq(2).remove();
				}
				else{
					$(obj).parents().eq(1).remove();
				}
			}
		);
	}
	else{
		alert("<?php echo _("Please select category first !");?>");
		$(obj).parents().eq(1).children('td').eq(1).children('select').focus();
	}
}

function reject_share_prod(obj){
	var from_comp_id = $(obj).parents().eq(2).find('input[type=hidden]').val();
	var prodid = $(obj).parents().eq(1).attr('id');
	$.post(
			base_url+'cp/shared/reject_share_product',
			{'proid': prodid,'from_comp_id':from_comp_id},
			function(result_data){
				if($(obj).parents().eq(2).children().length == 3){
					$(obj).parents().eq(2).remove();
				}
				else{
					$(obj).parents().eq(1).remove();
				}
			}
		);
}
</script>
<!-- MAIN -->
<div id="main">
	<div id="main-header">
    	<h2><?php echo _('Products')?></h2>
     	<span class="breadcrumb"><a href="<?php echo base_url();?>cp/cdashboard/"><?php echo _('Home')?></a> &raquo; <?php echo _('Products')?></span>
     	
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
	            	<form action="<?php echo base_url()?>cp/cdashboard/products_addedit/add" method="post" name="product_add" id="product_add">
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
                    			<td colspan="4" style="text-align:left;"><?php if($no_cat != 0){?><a href="<?php echo base_url()?>cp/cdashboard/assign_category"><?php echo _('Products without category')?><?php echo '('.$no_cat.')';?></a><?php }?></td>
                    			<?php if(($this->session->userdata('menu_type') == 'fdd_light') || ($this->session->userdata('menu_type') == 'fdd_pro') || ($this->session->userdata('menu_type') == 'fdd_premium') || ($this->session->userdata('menu_type') == 'fooddesk_light')){?>
                    			<td colspan="9" style="text-align:right;">
                    				<!--<a href="<?php echo base_url()?>cp/cdashboard/product_recipe"><?php echo _('Product Recipe')?></a>&nbsp;&nbsp;&nbsp;&nbsp;-->
	                    			<?php if ($this->session->userdata('menu_type') != 'fooddesk_light'){?>
	                    				<a href="<?php echo base_url().'cp/shared'?>" id="shared_prod" title="<?php echo _('Share Products');?>"><?php echo _('Share Products')?></a>&nbsp;&nbsp;&nbsp;&nbsp;
		                    			<?php if($fdd_credits > 0){?>
		                    				<a href="<?php echo base_url().'cp/fooddesk/products?height=300&width=700'?>" title="<?php echo _('Add product from FoodDESK (If not found, add new)');?>" class="thickbox"><?php echo _('Add FoodDESK Product')?></a>&nbsp;&nbsp;&nbsp;&nbsp;
		                    			<?php  
	                    				}else{?>
		                    				<a href="#TB_inline?height=300&width=500&inlineId=credit_require" title="<?php echo _('No credit left!');?>" class="thickbox"><?php echo _('Add FoodDESK Product')?></a>&nbsp;&nbsp;&nbsp;&nbsp;
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
                    			<th></th>
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
				  				<?php if($ord_col){?>
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
									<a href="<?php echo base_url().'cp/cdashboard/products_addedit/product_id/'.$product->id?>" class="edit" title="<?php echo _('Edit');?>"> <img width="16" height="16" border="0" alt="Edit" src="<?php echo base_url(); ?>assets/cp/images/edit.gif"></a> | 
									<a onclick="return confirmation('<?php echo $product->id; ?>');" href="#" class="delete" title="<?php echo _('Delete');?>"><img width="16" height="16" border="0" alt="remove" src="<?php echo base_url(); ?>assets/cp/images/delete.gif"></a>
									<?php if(!$product->parent_proid){?> | <a href="<?php echo base_url().'cp/cdashboard/product_clone/'.$product->id?>" class="delete" title="<?php echo _('Clone');?>"><img width="16" height="16" border="0" alt="clone" src="<?php echo base_url(); ?>assets/cp/images/arrow-turn-180.png"></a><?php }?> 
									<?php if( ($this->session->userdata('menu_type') == 'fdd_light') || ($this->session->userdata('menu_type') == 'fdd_pro') || ($this->session->userdata('menu_type') == 'fdd_premium') ){?>
										<?php if($product->direct_kcp == 1 && $product->direct_kcp_id != 0 && isset($product->pdf_name)){?>
											| <a target="_blank" class="producer_sheet" href="<?php echo $this->config->item('fdd_url').'dwpdf/?pdf='.$product->pdf_name;?>" title="<?php echo _('Product Sheet');?>"><img src="<?php echo base_url();?>assets/cp/images/01-pdf.png" style="width: 15px;" /></a>
										
										<?php }elseif($product->direct_kcp == 1 && $product->direct_kcp_id == 0){ ?>
											
											| <a target="_blank" class="technical_sheet" href="<?php echo base_url().'cp/fooddesk/technical_sheet/'.$product->id;?>" title="<?php echo _('Product Sheet');?>"><img src="<?php echo base_url();?>assets/cp/images/01-pdf.png" style="width: 15px;" /></a>
											
									<?php }elseif($product->direct_kcp == 0 && $product->recipe_weight != 0){?>
									
											| <a target="_blank" class="technical_sheet" href="<?php echo base_url().'cp/fooddesk/technical_sheet/'.$product->id;?>" title="<?php echo _('Technical Sheet');?>"><img src="<?php echo base_url();?>assets/cp/images/02-pdf.png" style="width: 15px;" /></a>
											| <a target="_blank" class="recipe_sheet" href="<?php echo base_url().'cp/fooddesk/recipe_sheet/'.$product->id;?>" title="<?php echo _('Recipe Sheet');?>"><img src="<?php echo base_url();?>assets/cp/images/03-pdf.png" style="width: 15px;" /></a>
										<?php }?>
									<?php }?>
									<?php if( $product->prod_sent == 1 ){ ?>
											<br>
											<input type="checkbox" data-id="<?php echo $product->id;?>" class="styled mark_as_approved"></input>&nbsp
											<span style="padding-bottom: 5px;"><?php echo _( 'Recipe approved' );?></span>
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
								  	<button type="button" id="recipe_sheets" data-link='<?php echo base_url()."cp/fooddesk/all_recipe_sheets/".$cat_id."/".$sub_cat_id."/"; ?>'><?php echo _('PRINT RECIPES');?></button> -->
									<img src="<?php echo base_url();?>assets/cp/images/01-pdf.png" style="margin-left:120px;width: 15px;" /> - <b><?php echo _('Producer Sheet(pdf)');?> </b>&nbsp;&nbsp;&nbsp;&nbsp;
									<img src="<?php echo base_url();?>assets/cp/images/02-pdf.png" style="width: 15px;" /> - <b><?php echo _('Techinal Sheet(pdf)');?> </b>&nbsp;&nbsp;&nbsp;&nbsp;
									<img src="<?php echo base_url();?>assets/cp/images/03-pdf.png" style="width: 15px;" /> - <b><?php echo _('Recipe Sheet(pdf)');?> </b>&nbsp;&nbsp;&nbsp;&nbsp;
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