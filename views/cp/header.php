<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<title><?php echo _('Admin Panel')?></title>
<meta content="OBS, online bestellen, bestellingssysteem, bestelsysteem, bakker, bakkerij, brood en banket, broodjeszaak, broodjeszaken, online, beheren, beheersysteem, goedkoop" name="keywords">
<meta content="Online bestellen met OBS (online bestelsysteem) voor bakkers / broodjeszaken /Traiteurs / Groente- en fruitwinkels / etc... Wees de eerste in uw buurt, verwen uw klanten!" name="description">
<meta content="8xMT5ro3-13nEZPiQ5gvi_CwjTc7kQeENeZlKT05aiE" name="google-site-verification">
<meta http-equiv="X-UA-Compatible" content="IE=8" />
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/cp/css/calender.css?version=<?php echo version;?>">
<!-- <link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/jquery-ui-1.12.0.custom/jquery-ui.css"> -->

<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/cp/css/tipsy.css">
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/cp/new_css/style.css?version=<?php echo version;?>">
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/cp/new_css/table.css?version=<?php echo version;?>">
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/cp/new_css/form.css?version=<?php echo version;?>">
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/cp/new_css/dhtmlgoodies_calendar.css?random=20051112">
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/cp/new_css/colorbox.css?version=<?php echo version;?>">
<script type="text/javascript">
	var base_url="<?php echo base_url();?>";<?php //this is for the js files included in header ?>
</script>
<script type="text/javascript">
function Set_Cookie( name, value, expires, path, domain, secure )
{
	// set time, it's in milliseconds
	var today = new Date();
	today.setTime( today.getTime() );

	/*
	if the expires variable is set, make the correct
	expires time, the current script below will set
	it for x number of days, to make it for hours,
	delete * 24, for minutes, delete * 60 * 24
	*/
	if ( expires )
	{
	expires = expires * 1000 * 60 * 60 * 24;
	}
	var expires_date = new Date( today.getTime() + (expires) );

	document.cookie = name + "=" +escape( value ) +
	( ( expires ) ? ";expires=" + expires_date.toGMTString() : "" ) +
	( ( path ) ? ";path=" + path : "" ) +
	( ( domain ) ? ";domain=" + domain : "" ) +
	( ( secure ) ? ";secure" : "" );
}

// this fixes an issue with the old method, ambiguous values
// with this test document.cookie.indexOf( name + "=" );
function Get_Cookie( check_name )
{
	// first we'll split this cookie up into name/value pairs
	// note: document.cookie only returns name=value, not the other components
	var a_all_cookies = document.cookie.split( ';' );
	var a_temp_cookie = '';
	var cookie_name = '';
	var cookie_value = '';
	var b_cookie_found = false; // set boolean t/f default f

	for ( i = 0; i < a_all_cookies.length; i++ )
	{
		// now we'll split apart each name=value pair
		a_temp_cookie = a_all_cookies[i].split( '=' );

		// and trim left/right whitespace while we're at it
		cookie_name = a_temp_cookie[0].replace(/^\s+|\s+$/g, '');

		// if the extracted name matches passed check_name
		if ( cookie_name == check_name )
		{
			b_cookie_found = true;
			// we need to handle case where cookie has no value but exists (no = sign, that is):
			if ( a_temp_cookie.length > 1 )
			{
				cookie_value = unescape( a_temp_cookie[1].replace(/^\s+|\s+$/g, '') );
			}
			// note that in cases where cookie is initialized but no value, null is returned
			return cookie_value;
			break;
		}
		a_temp_cookie = null;
		cookie_name = '';
	}
	if ( !b_cookie_found )
	{
		return null;
	}
}
</script>

<script src="<?php echo base_url(); ?>assets/cp/js/jquery-3.1.0.min.js?version=<?php echo version;?>" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/plugins/jquery-ui-1.12.0.custom/jquery-ui.min.js?version=<?php echo version;?>" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/cp/js/SpryMenuBar.js?version=<?php echo version;?>" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/cp/js/color_functions.js?version=<?php echo version;?>" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/cp/js/general_functions.js?version=<?php echo version;?>" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/cp/js/validator.js?version=<?php echo version;?>" type="text/javascript"></script>
<!--<script src="<?php echo base_url(); ?>assets/plugins/jquery-ui-1.12.0.custom/jquery-ui.min.js" type="text/javascript"></script>-->


<!-- <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/jquery-ui-1.12.0.custom/jquery-ui.theme.min.css"> -->

<!--for intro and sidebar-->
<!--<script type='text/javascript' src='<?php echo base_url()?>assets/cp/js/jquery-1.5.2.min.js'></script>-->


<?php if( ($this->router->class == "cdashboard" && $this->router->method == 'products_addedit') || ($this->router->class == "fooddesk" && $this->router->method == 'fdd_own_products_all') ){?>
<script src="<?php echo base_url(); ?>assets/cp/new_js/jquery.showhide-pro-add.js?version=<?php echo version;?>" type="text/javascript"></script>
<?php }else{?>
<script src="<?php echo base_url(); ?>assets/cp/js/tiny_mce/tiny_mce.js?version=<?php echo version;?>" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/cp/new_js/tinymceInit.js?version=<?php echo version;?>" type="text/javascript"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/cp/js/tiny_mce/themes/advanced/skins/default/ui.css?version=<?php echo version;?>">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/cp/js/tiny_mce/plugins/inlinepopups/skins/clearlooks2/window.css?version=<?php echo version;?>">
<script src="<?php echo base_url(); ?>assets/cp/js/jquery.showhide.js?version=<?php echo version;?>" type="text/javascript"></script>
<?php }?>
<link rel="stylesheet" href="<?php echo base_url().'assets/cp/js/thickbox/css/thickbox.css?version='.version ?>" type="text/css"/>
<script type="text/javascript" src="<?php echo base_url().'assets/cp/js/thickbox/javascript/thickbox.js?version='.version ?>"></script>

<script src="<?php echo base_url(); ?>assets/cp/new_js/dhtmlgoodies_calendar.js?random=20060118" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/cp/new_js/jquery.colorbox.js?version=<?php echo version;?>" type="text/javascript"></script>
</head>
<body>
<input type="hidden" id="set_language" name="set_language" value="<?php echo $_COOKIE['locale'];?>" />
<input id="shop_version" type="hidden" value="<?php echo $this->company->shop_version;?>"/>
<input id="infodesk_status" type="hidden" value="<?php echo $this->company->obsdesk_status;?>"/>
<?php if($this->session->userdata('action')){?>
<script type="text/javascript">
	var action = "<?php echo $this->session->userdata('action')?>";
	var shop_version = jQuery('#shop_version').val();
	if(shop_version == 2 || shop_version == 3){
		jQuery.post(
			base_url+"cp/shop_all/update_json_files/"+shop_version,
		    {'action':action},
		    function(data){},
		    'json'
		);
	}

	var infodesk_status = jQuery('#infodesk_status').val();
	if(infodesk_status == 1){
		jQuery.post(
			base_url+"cp/shop_all/update_desk_files/"+infodesk_status,
			{'action':action},
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
</script>
<?php $this->session->unset_userdata('action');
}?>
<div id="cboxOverlay" style="display: none;"></div>
<div id="colorbox" style="padding-bottom: 20px; padding-right: 0px; display: none;">
  <div id="cboxWrapper">
    <div>
      <div id="cboxTopLeft" style="float: left;"></div>
      <div id="cboxTopCenter" style="float: left;"></div>
      <div id="cboxTopRight" style="float: left;"></div>
    </div>
    <div>
      <div id="cboxMiddleLeft" style="float: left;"></div>
      <div id="cboxContent" style="float: left;">
        <div id="cboxLoadedContent" style="width: 0px; height: 0px;" class=""></div>
        <div id="cboxLoadingOverlay" class=""></div>
        <div id="cboxLoadingGraphic" class=""></div>
        <div id="cboxTitle" class=""></div>
        <div id="cboxCurrent" class=""></div>
        <div id="cboxSlideshow" class=""></div>
        <div id="cboxNext" class=""></div>
        <div id="cboxPrevious" class=""></div>
        <div id="cboxClose" class=""></div>
      </div>
      <div id="cboxMiddleRight" style="float: left;"></div>
    </div>
    <div>
      <div id="cboxBottomLeft" style="float: left;"></div>
      <div id="cboxBottomCenter" style="float: left;"></div>
      <div id="cboxBottomRight" style="float: left;"></div>
    </div>
  </div>
  <div style="position: absolute; top: 0pt; left: 0pt; width: 9999px; height: 0pt;"></div>
</div>
<style type="text/css">.textlabel{	padding-left:20px;	font-size:14px;	font-weight:bold;}</style>
<script type="text/javascript">
	function confirmation_det(id){
		var answer = confirm("<?php echo _('Are you sure you want to delete this?'); ?>");
		if ("<?php echo $this->router->method?>"=="settings" && answer){
			jQuery.post("<?php echo base_url();?>cp/cdashboard/delete_delivery_settings",
					{'id':id},
					function(data){
						window.location.reload();
			});
		}else{
			return false;
		}
  	}

	function intro_show_hide(checkobj){
		if(checkobj.checked){
			$(".hide_intro").css({'display':'block'});
		}else{
			$(".hide_intro").css({'display':'none'});
		}
	}
	function bar_show_hide(divid){
		var division_id='#'+divid;

		jQuery(division_id).next('.inside').slideToggle();

	}

	function show_hide(trid,section,value,sid){
    	var id = section+"_"+trid;
		if(value == "CLOSED" || value == "ALL DAY"){
			document.getElementById(id).style.display = "none";
		}else{
			document.getElementById(id).style.display = "block";
    	}
	}

	function show_hide_delivery(){
		var delivery = document.getElementById("delivery_service");
		if(delivery.checked == true){
			document.getElementById("delivery").style.display = "block";
		}else if(delivery.checked == false){
			document.getElementById("delivery").style.display = "none";
		}
	}

	function show_hide_pickup(){
  		var pickup = document.getElementById("pickup_service");
		if(pickup.checked == true){
			document.getElementById("pickup").style.display = "block";
		}else if(pickup.checked == false){
			document.getElementById("pickup").style.display = "none";
		}
	}

	function samedayDelivery(){
	  	var sameDayDeliveryOrder = document.getElementById("same_day_orders_delivery");

		if(sameDayDeliveryOrder)
		{
			if(sameDayDeliveryOrder.checked == true){
				document.getElementById("samedayDelivery").style.display = "block";
			}else if(sameDayDeliveryOrder.checked == false){
				document.getElementById("samedayDelivery").style.display = "none";
			}
		}
	}
	function samedayPickup(){
	  	var sameDayPickupOrder = document.getElementById("same_day_orders_pickup");

		if(sameDayPickupOrder)
		{
		  if(sameDayPickupOrder.checked == true){
			document.getElementById("samedayPickup").style.display = "block";

		  }else if(sameDayPickupOrder.checked == false){
			document.getElementById("samedayPickup").style.display = "none";
		  }
		}
	}

	function confirmation_order(order,cat_id){
	   if("<?php echo $this->router->method?>"=="subcategories"){
	   		jQuery.post("<?php echo base_url(); ?>cp/cdashboard/change_subcategory_order",
					{'order':order,'id':cat_id},
	    			function(data){
						if(data.trim()=="successfully updated"){
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
							jQuery("#succeed_order_update").css({'display':'block'});
						}else{
							jQuery("#error_order_update").css({'display':'block'});
						}
	   		});
		}else if("<?php echo $this->router->method?>"=="categories"){
	   		jQuery.post("<?php echo base_url(); ?>cp/cdashboard/change_category_order",
					{'order':order,'id':cat_id},
	    			function(data){
						if(data.trim()=="Status successfully updated"){
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

							jQuery('#succeed_order_update').css({'display':'block'});
							//window.location.reload();
						}else if(data="<?php echo _('Error occurred while updating status.'); ?>"){
							jQuery('#error_order_update').css({'display':'block'});
						}
	  	 	});
		}
	}

	 function setServiceType(service_type,cat_id){
	 	jQuery.post("<?php echo base_url(); ?>cp/categories/change_category_service_type",
					{'id':cat_id,'service_type':service_type},
	    			function(data){
					    if(data.trim()=='successfully updated'){
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

						  	alert("<?php echo _('Category service updated successfully.'); ?>");
					    }
					else{
						  alert("<?php echo _('Some error occurred ! Please try again.'); ?>");
						  window.location.reload();
	  			        }
				   });
	}
	function confirmation( id, company_id ){
		var answer = confirm("<?php echo _('Are you sure you want to delete this ?')?>");
		if(("<?php echo $this->router->method?>"=="subcategories")&&answer&&("<?php echo $this->router->fetch_class()?>"!="subcategories")){
			jQuery.post("<?php echo base_url();?>cp/cdashboard/delete_subcategory",
					{'id':id},
					function(data){
							if(data){
							  window.location="<?php echo base_url();?>cp/cdashboard/subcategories/category_id/"+data['categories_id'];
							}else{
							  jQuery("#result").css({'display':'block'});
							}
			},'json');

		}else if(("<?php echo $this->router->method?>"=="lijst")&&answer&&("<?php echo $this->router->fetch_class()?>"=="subcategories")){
			jQuery.post("<?php echo base_url();?>cp/subcategories/delete_subcategory",
					{'id':id},
					function(data){
							if(data){
							  window.location="<?php echo base_url();?>cp/subcategories/lijst/category_id/"+data['categories_id'];
							}else{
							  jQuery("#result").css({'display':'block'});
							}
			},'json');

			}else if(("<?php echo $this->router->method?>"=="index")&&answer&&("<?php echo $this->router->fetch_class()?>"=="orders")){
			jQuery.post("<?php echo base_url(); ?>cp/orders/index",
					{'id':id,'act':'delete_order','delete_row':'single'},
	    			function(data){
						if(data.success){
							alert(data.success);
					    	window.location="<?php echo base_url();?>cp/orders";
	  			   		}else if(data.error){
							alert(data.error);
						}
				   },'json');
		}
		else if(("<?php echo $this->router->method?>"=="index")&&answer&&("<?php echo $this->router->fetch_class()?>"=="categories")){
			jQuery.post("<?php echo base_url(); ?>cp/categories/delete_category",
					{'id':id},
	    			function(data){
					    window.location="<?php echo base_url();?>cp/categories";
	  			   });

	  	}else if(("<?php echo $this->router->method?>"=="categories")&&answer){
			jQuery.post("<?php echo base_url(); ?>cp/cdashboard/delete_category",
					{'id':id},
	    			function(data){
					    window.location="<?php echo base_url();?>cp/cdashboard/categories";
	  			   });
		}else if("<?php echo $this->router->method?>"=="clients"&&answer){
			//jQuery.post("<?php echo base_url();?>cp/cdashboard/delete_clients",
			jQuery.post("<?php echo base_url();?>cp/cdashboard/remove_client",
					{ 'id': id,
					  'company_id' : company_id
					},
					function(data){
						window.location.reload();
					});
		}else if(("<?php echo $this->router->method?>"=="index"&&answer&&("<?php echo $this->router->fetch_class()?>"=="clients")) || ("<?php echo $this->router->method?>"=="lijst"&&answer&&("<?php echo $this->router->fetch_class()?>"=="clients"))){
			//jQuery.post("<?php echo base_url();?>cp/clients/delete_clients",
			jQuery.post("<?php echo base_url();?>cp/clients/remove_client",
					{ 'id': id,
					  'company_id' : company_id
					},
					function(data){
						window.location.reload();
					});
		}else if(("<?php echo $this->router->method?>"=="lijst")&&answer&&("<?php echo $this->router->fetch_class()?>"=="products")){
			jQuery.post("<?php echo base_url(); ?>cp/products/delete_product",
					{
					'id' 				: id,
					'categories_id' 	: $('#categories_id').val(),
					'subcategories_id' 	: $('#subcategories_id').val()
					},
	    			function(data){
					    window.location="<?php echo base_url();?>cp/products/lijst/category_id/"+data['categories_id']+"/subcategory_id/"+data['subcategories_id'];
	  			   },'json');

		}else if(("<?php echo $this->router->method?>"=="index")&&answer&&("<?php echo $this->router->fetch_class()?>"=="products")){
			jQuery.post("<?php echo base_url(); ?>cp/products/delete_product",
					{
						'id' 				: id,
						'categories_id' 	: $('#categories_id').val(),
						'subcategories_id'	: $('#subcategories_id').val()

					},
	    			function(data){
					    window.location="<?php echo base_url();?>cp/products/lijst/category_id/"+data['categories_id']+"/subcategory_id/"+data['subcategories_id'];
	  			   },'json');
		}
		else if("<?php echo $this->router->method?>"=="settings"&&answer){
			jQuery.post("<?php echo base_url();?>cp/cdashboard/delete_delivery_areas",
					{'id':id},
					function(data){
						window.location="<?php echo base_url();?>cp/settings";
					});
		}else if(("<?php echo $this->router->method?>"=="products")&&answer){
			jQuery.post("<?php echo base_url(); ?>cp/cdashboard/delete_product",
					{'id':id},
	    			function(data){
					    window.location="<?php echo base_url();?>cp/cdashboard/products/category_id/"+data['categories_id']+"/subcategory_id/"+data['subcategories_id'];
	  			   },'json');
		}else if(("<?php echo $this->router->method?>"=="orders")&&answer){
			jQuery.post("<?php echo base_url(); ?>cp/cdashboard/orders",
					{'id':id,'act':'delete_order','delete_row':'single'},
	    			function(data){
						if(data.success){
							alert(data.success);
					    	window.location="<?php echo base_url();?>cp/cdashboard/orders";
	  			   		}else if(data.error){
							alert(data.error);
						}
				   },'json');
		}else if(("<?php echo $this->router->method?>"=="semi_products")&&answer&&("<?php echo $this->router->fetch_class()?>"!="products")){
			jQuery.post("<?php echo base_url(); ?>cp/cdashboard/delete_product",
					{'id':id},
	    			function(data){
					    window.location='<?php echo base_url();?>cp/cdashboard/semi_products';
	  			   },'json');
		}else if(("<?php echo $this->router->method?>"=="semi_products")&&answer&&("<?php echo $this->router->fetch_class()?>"=="products")){
			jQuery.post("<?php echo base_url(); ?>cp/products/delete_product",
					{
						'id'				: id,
						'categories_id'		: $('#categories_id').val(),
						'subcategories_id' 	: $('#subcategories_id').val()
					},
	    			function(data){
					    window.location='<?php echo base_url();?>cp/products/semi_products';
	  			   },'json');
		}else if(("<?php echo $this->router->method?>"=="semi_products_extra")&&answer&&("<?php echo $this->router->fetch_class()?>"!="products")){
			jQuery.post("<?php echo base_url(); ?>cp/cdashboard/delete_product",
					{'id':id},
	    			function(data){
					    window.location='<?php echo base_url();?>cp/cdashboard/semi_products_extra';
	  			   },'json');
		}
		else if(("<?php echo $this->router->method?>"=="semi_products_extra")&&answer&&("<?php echo $this->router->fetch_class()?>"=="products")){
			jQuery.post("<?php echo base_url(); ?>cp/products/delete_product",
					{
						'id' 				: id,
						'categories_id'		: $('#categories_id').val(),
						'subcategories_id'	: $('#subcategories_id').val()
					},
	    			function(data){
					    window.location='<?php echo base_url();?>cp/products/semi_products_extra';
	  			   },'json');
		}else{
			return false;
		}
	}
	//--function to set sub categories --//
	function inCategory(cat_id){

		if("<?php echo $this->router->fetch_method()?>"=="products"){
			window.location="<?php echo base_url();?>cp/products/lijst/category_id/"+cat_id;
		}else if("<?php echo $this->router->fetch_method()?>"=="products_addedit" || "<?php echo $this->router->fetch_method()?>"=="add_empty_ingredient_product"){
				jQuery.post("<?php echo base_url(); ?>cp/products/get_sub_category",
					{'cat_id':cat_id},
	    			function(data){
						jQuery("#subcategories_id option").remove();//this is to remove the previous values of drop down menu //
						jQuery("#subcategories_id").append("<option value='-1' selected='selected'>-- <?php echo _('Select Subcategory'); ?> --</option>");
						for(var i=0;i<data.length;i++){
							jQuery("#subcategories_id").append(jQuery("<option></option>").val(data[i].id).html(data[i].subname));
						}
					},'json');
		}else if("<?php echo $this->router->fetch_method()?>"=="subcategories"){
			window.location="<?php echo base_url();?>cp/subcategories/category_id/"+cat_id;
		}
		// start
		else if("<?php echo $this->router->fetch_class() ?>" == "categories" && "<?php echo $this->router->fetch_method() ?>"=="assign_category"){
			if(cat_id != '0')
				window.location="<?php echo base_url();?>cp/categories/assign_category/category_id/"+cat_id;
			else
				window.location="<?php echo base_url();?>cp/products/assign_category";
		}
		// end

		else if("<?php echo $this->router->fetch_method() ?>"=="assign_category"){
			if(cat_id == '-1' )
				window.location="<?php echo base_url();?>cp/products/assign_category/category_id/-1";
			else
				window.location="<?php echo base_url();?>cp/products/assign_category/category_id/"+cat_id;
		}
		else if("<?php echo $this->router->fetch_class() ?>" == "products" && ("<?php echo $this->router->fetch_class()?>"!="subcategories")){
			window.location="<?php echo base_url();?>cp/products/lijst/category_id/"+cat_id;
		}
		else if("<?php echo $this->router->fetch_class() ?>" == "subcategories"){
			window.location="<?php echo base_url();?>cp/subcategories/lijst/category_id/"+cat_id;
		}
		else if("<?php echo $this->router->fetch_class() ?>"=="shared"){
			if(cat_id != '0'){
				window.location="<?php echo base_url();?>cp/shared/ljist/category_id/"+cat_id;
			}
			else{
				window.location="<?php echo base_url();?>cp/shared";}
		}
	}
	function inSubcategory(cat_id,subcat_id){
		if("<?php echo $this->router->fetch_method() ?>"=="lijst"){
			if(subcat_id>0){
			  window.location ="<?php echo base_url();?>cp/products/lijst/category_id/"+cat_id+"/subcategory_id/"+subcat_id;
		  	}else{
			  window.location ="<?php echo base_url();?>cp/products/lijst/category_id/"+cat_id;
			}

		}
		else{
			if(subcat_id>0){
			  window.location ="<?php echo base_url();?>cp/cdashboard/products/category_id/"+cat_id+"/subcategory_id/"+subcat_id;
		  	}else{
			  window.location ="<?php echo base_url();?>cp/cdashboard/products/category_id/"+cat_id;
			}
		}
	}
	function change_product_sequence(order,prod_id){
	    jQuery.post("<?php echo base_url(); ?>cp/cdashboard/change_product_sequence",
				{'order':order,'id':prod_id},
	    		function(data){
					if(data.trim()=='successfully updated'){
						//alert("<?php echo _('Product order changed successfully.');?>"");
					}
					else{
						alert("<?php echo _('Some errro occured ! Please try again.'); ?>");
					    window.location.reload();
	  			    }
		});
	}
	function confirmation_product_deletion(id){
		var answer = confirm("<?php echo _('Are you sure you want to delete this product?'); ?>");
		if(answer){
			jQuery.post("<?php echo base_url(); ?>cp/cdashboard/delete_product",
					{'id':id},
	    			function(data){
					    window.location.reload();
	  			   });
		}else{
			return false;
		}
	}

	jQuery(document).ready(function($){//	var $jq=jQuery.noConflict();	//Examples of how to assign the ColorBox event to elements
		jQuery(".colorbox").css({
			width:'400px',
			height:'350px',
			opacity:'0.7'
		});
		jQuery(".colorbox_price").css({
			opacity:'0.7'
		});

		/*jQuery(".my_shop").mouseover(function(){
			jQuery('.shop_alert').show();
		});

		jQuery(".my_shop").mouseout(function(){
			var isHovered = jQuery('.shop_alert').is(":hover");
			alert(isHovered);
			if(!isHovered){
				jQuery('.shop_alert').hide();
			}
		});*/
	});

	function shop_prompt(){
		jQuery('.shop_alert').toggle();
	}
</script>
<!-- WRAPPER -->
<div id="wrapper">
<!-- HEADER -->
<div id="header">
  <h1><a target="_blank" href="http://www.fooddesk.net"><img height="50" src="<?php echo base_url(); ?>assets/cp/images/rs_logo.png"></a></h1>
  <div id="head-nav">

	<?php if($this->session->userdata('cp_is_logged_in') == true && $this->session->userdata('cp_username')):?>
		<?php echo _('Logged in as'); ?>
		<?php /*?><a href="<?php if(substr($this->company->website,0,7) != 'http://' && substr($this->company->website,0,8) != 'https://'){echo 'http://'.$this->company->website;}else{echo $this->company->website;} ?>" target="_blank"><b><?php echo $this->session->userdata('cp_username');?></b></a><span class="line">|</span><?php */?>
		<a href="<?php echo base_url()?>cp/cdashboard/profile"><b><?php echo $this->session->userdata('cp_username');?></b></a><span class="line">|</span>
	<?php endif;?>

	<?php if( $this->company_role == 'sub' ) { ?>
	    <a href="#TB_inline?height=200&width=300&inlineId=inlineSuper" class="linkSuper thickbox" title="<?php echo _('Super Admin Login'); ?>"><?php echo _('Super Admin Login'); ?></a> <span class="line">|</span>
		<div style="display:none;">
			<div id="inlineSuper" style="padding:30px; background:#fff; margin:2px;">
			<form name="frm_access" id="frm_access" method="post" enctype="multipart/form-data" action="<?php echo base_url()?>cp/cdashboard/loginSuper">
		    <input type="hidden" name="company_id" value="<?php echo $this->company_id; ?>">
			<input type="hidden" name="company_parent_id" value="<?php echo $this->company_parent_id; ?>">
			<p style="height:40px; font-size:13px"><strong><?php echo _('Please enter your code'); ?> : </strong>
			<input name="access" id="access" type="text" class="short" value="" style="width:70px" maxlength="4">
			&nbsp;&nbsp;
			<input name="but_upd" id="but_upd" type="submit" class="submit" value="<?php echo _('Login'); ?>"></p>
			</form>
			</div>
		</div>
		<script type="text/javascript">
		  $(document).ready(function(){
			$(".linkSuper").colorbox({ inline:true, href:"#inlineSuper"});
			var frmValidator = new Validator("frm_access");
			frmValidator.EnableMsgsTogether();
			frmValidator.addValidation("access","req","<?php echo _('Please enter your 4-digit code'); ?>");
		  });
		</script>
	<?php } ?>

	<?php if($this->session->userdata('cp_website') && $this->session->userdata('cp_website') != ''){?>
	<a target="_blank" href="<?php echo $this->session->userdata('cp_website');?>"><?php echo _('My Shop'); ?></a> <span class="line">|</span>
	<?php }else{?>
	<a href="javascript:void(0);" class="my_shop" onClick="shop_prompt();" style="position: relative;"><?php echo _('My Shop'); ?></a> <span class="line">|</span>
	<div class="shop_alert">
		<span><?php echo _("Currently no shop link have been provide by you. please provide this from ")?><a href="<?php echo base_url();?>cp/ftp_settings"><?php echo _("here");?></a></span>
	</div>
	<?php }?>

	<?php if( $this->company_role == 'master' || $this->company_role == 'super' ) { ?>
	<!-- <a target="_blank" href="http://www.onlinebestelsysteem.net/FAQ/" target="_blank"><?php echo _('FAQ'); ?></a> -->
	<a href="<?php echo base_url()?>cp/cdashboard/faq_new"><?php echo _('FAQ'); ?></a>
	<span class="line">|</span>
	<?php } ?>

	<!--<?php if( $this->company_role == 'master' || $this->company_role == 'super' ) { ?>
	<a href="<?php echo base_url()?>cp/cdashboard/myaccount"><?php echo _('My Account'); ?></a><span class="line">|</span>
	<?php } ?>

	<?php if( $this->company_role == 'master' || $this->company_role == 'super' ) { ?>
	<a href="<?php echo base_url()?>cp/cdashboard/addons"><?php echo _('Addons'); ?></a> <span class="line">|</span>
	<?php } ?> -->

	<a title="Uitloggen" href="<?php echo base_url(); ?>cp/login/logout"><?php echo _('Logout'); ?></a> </div>
</div>
<!-- /HEADER -->

<!-- MENU -->
<ul id="navigation">
<!--
**if user selects into page to  stay hidden in settings page
**the session variable 'show hide' is being set on the index page;
**if it is set then intro would not be seen in nevigation bar
-->
<?php $menu_type = $this->session->userdata('menu_type'); ?>
<!--  FREE MENU  -->
<?php if( $menu_type == 'free' ) { ?>
	<!-- <?php if(!$this->session->userdata('show_hide')):?> -->
  	<li <?php if($this->router->method == 'intro' || $this->router->method == 'index'): ?>class="current"<?php endif; ?> class="intro"><a title="<?php echo _('Intro'); ?>" href="<?php echo base_url(); ?>cp/"><?php echo _('Intro'); ?></a></li>
	<!-- <?php endif;?> -->

	<?php if( $this->company_role == 'master' || $this->company_role == 'sub' || $this->company_role == 'super' ) { ?>
  	<li <?php if($this->router->method == 'orders'): ?>class="current"<?php endif; ?>><a title="<?php echo _('Orders'); ?>" href="<?php echo base_url(); ?>cp/cdashboard/page_not_found/orders"><?php echo _('Orders'); ?></a></li>
	<?php } ?>

	<?php if( $this->company_role == 'master' || $this->company_role == 'super' ) { ?>
  	<li <?php if($this->router->method == 'categories'): ?>class="current"<?php endif; ?>><a title="<?php echo _('Manage Categories'); ?>" href="<?php echo base_url(); ?>cp/cdashboard/page_not_found/categories"><?php echo _('Categories'); ?></a></li>
	<?php } ?>

	<?php if( $this->company_role == 'master' || $this->company_role == 'super' ) { ?>
  	<li <?php if($this->router->method == 'subcategories'): ?>class="current"<?php endif; ?>><a title="<?php echo _('Manage Subcategories'); ?>" href="<?php echo base_url(); ?>cp/cdashboard/page_not_found/subcategories"><?php echo _('Subcategories'); ?></a></li>
	<?php } ?>

	<?php if( $this->company_role == 'master' || $this->company_role == 'super' ) { ?>
  	<li <?php if($this->router->method == 'products'): ?>class="current"<?php endif; ?>><a title="<?php echo _('Manage Products'); ?>" href="<?php echo base_url(); ?>cp/cdashboard/page_not_found/products"><?php echo _('Products'); ?></a></li>
	<?php } ?>

  	<li <?php if($this->router->method == 'clients'): ?>class="current"<?php endif; ?>><a title="<?php echo _('Manage Clients'); ?>" href="<?php echo base_url(); ?>cp/clients"><?php echo _('Clients'); ?></a></li>

	<?php if( $this->company_role == 'super' ) { ?>
  	<li <?php if($this->router->class == 'sub_admins'): ?>class="current"<?php endif; ?>><a title="<?php echo _('Manage Sub Admins'); ?>" href="<?php echo base_url(); ?>cp/cdashboard/page_not_found/sub_admins"><?php echo _('Sub Admins'); ?></a></li>
	<?php } ?>

  	<li <?php if($this->router->method == 'settings'): ?>class="current"<?php endif; ?>><a title="<?php echo _('Manage Site Settings'); ?>" href="<?php echo base_url(); ?>cp/cdashboard/page_not_found/settings"><?php echo _('Site Settings'); ?></a></li>

	<?php /*if( $this->company_role == 'master' || $this->company_role == 'super' ) { ?>
  	<li <?php if($this->router->method == 'changepassword'): ?>class="current"<?php endif; ?>><a title="<?php echo _('Change Password'); ?>" href="<?php echo base_url(); ?>cp/cdashboard/page_not_found/changepassword"><?php echo _('Change Password'); ?></a></li>
	<?php } ?>

	<?php if( $this->company_role == 'master' || $this->company_role == 'super' ) { ?>
  	<li <?php if($this->router->method=="section_designs"):?>class="current" <?php endif;?>><a title="<?php echo _('Change Design')?>" href="<?php echo base_url()?>cp/cdashboard/page_not_found/section_designs"><?php echo _('Change Design')?></a></li>
	<?php }*/ ?>

<!--  	<li <?php if($this->router->method == 'bp_settings'): ?>class="current"<?php endif; ?> class="intro"><a title="<?php echo _('Bestelonline.nu'); ?>" href="<?php echo base_url(); ?>cp/bestelonline/bp_settings"><?php echo _('Bestelonline.nu'); ?></a></li>
 -->
<!--  BASIC MENU  -->
<?php } elseif( $menu_type == 'basic' ) { ?>

	<?php if(!$this->session->userdata('show_hide')):?>
  	<li <?php if( $this->router->class == 'cdashboard' && $this->router->method == 'index' ): ?>class="current"<?php endif; ?> class="intro"><a title="<?php echo _('Intro'); ?>" href="<?php echo base_url(); ?>cp/"><?php echo _('Intro'); ?></a></li>
	<?php endif;?>

	<?php if( $this->company_role == 'master' || $this->company_role == 'sub' || $this->company_role == 'super' ) { ?>
  	<li <?php if($this->router->class == 'orders'): ?>class="current"<?php endif; ?>><a title="<?php echo _('Orders'); ?>" href="<?php echo base_url(); ?>cp/orders"><?php echo _('Orders'); ?></a></li>
	<?php } ?>

	<?php if( $this->company_role == 'master' || $this->company_role == 'super' ) { ?>
  	<li <?php if($this->router->class == 'categories'): ?>class="current"<?php endif; ?>><a title="<?php echo _('Manage Categories'); ?>" href="<?php echo base_url(); ?>cp/categories"><?php echo _('Categories'); ?></a></li>
	<?php } ?>

	<?php if( $this->company_role == 'master' || $this->company_role == 'super' ) { ?>
  	<li <?php if($this->router->class == 'subcategories'): ?>class="current"<?php endif; ?>><a title="<?php echo _('Manage Subcategories'); ?>" href="<?php echo base_url(); ?>cp/subcategories"><?php echo _('Subcategories'); ?></a></li>
	<?php } ?>

	<?php if( $this->company_role == 'master' || $this->company_role == 'super' ) { ?>
  	<li <?php if($this->router->class == 'products'): ?>class="current"<?php endif; ?>><a title="<?php echo _('Manage Products'); ?>" href="<?php echo base_url(); ?>cp/products"><?php echo _('Products'); ?></a></li>
	<?php } ?>

 	<li <?php if($this->router->class == 'clients'): ?>class="current"<?php endif; ?>><a title="<?php echo _('Manage Clients'); ?>" href="<?php echo base_url(); ?>cp/clients"><?php echo _('Clients'); ?></a></li>

	<?php if( $this->company_role == 'super' ) { ?>
  	<li <?php if($this->router->method == 'sub_admins'): ?>class="current"<?php endif; ?>><a title="<?php echo _('Manage Sub Admins'); ?>" href="<?php echo base_url(); ?>cp/cdashboard/sub_admins"><?php echo _('Sub Admins'); ?></a></li>
	<?php } ?>

  	<li <?php if($this->router->class == 'settings'): ?>class="current"<?php endif; ?>><a title="<?php echo _('Manage Site Settings'); ?>" href="<?php echo base_url(); ?>cp/settings"><?php echo _('Site Settings'); ?></a></li>

	<?php /*if( $this->company_role == 'master' || $this->company_role == 'super' ) { ?>
  	<li <?php if($this->router->method == 'changepassword'): ?>class="current"<?php endif; ?>><a title="<?php echo _('Change Password'); ?>" href="<?php echo base_url(); ?>cp/cdashboard/changepassword"><?php echo _('Change Password'); ?></a></li>
	<?php } ?>

	<?php if( $this->company_role == 'master' || $this->company_role == 'super' ) { ?>
  	<li <?php if($this->router->method=="section_designs"):?>class="current" <?php endif;?>><a title="<?php echo _('Change Design')?>" href="<?php echo base_url()?>cp/cdashboard/page_not_found/section_designs"><?php echo _('Change Design')?></a></li>
	<?php }*/ ?>

	<?php if($this->company->obsdesk_status) { ?><li <?php if($this->router->class == 'desk'): ?>class="current"<?php endif; ?>><a title="<?php echo _('Desk Setting'); ?>" href="<?php echo base_url(); ?>cp/desk/settings"><strong>infoDESK</strong></a></li><?php }?>
<!--  PRO MENU  -->
<?php }
	elseif ($menu_type == 'fooddesk_light'){
		if(!$this->session->userdata('show_hide')):?> <!-- correct -->
	  	<li <?php if( $this->router->class == 'cdashboard' && $this->router->method == 'index' ): ?>class="current"<?php endif; ?> class="intro"><a title="<?php echo _('Intro'); ?>" href="<?php echo base_url(); ?>cp/"><?php echo _('Intro'); ?></a></li>
		<?php endif;?>

		<?php if( $this->company_role == 'master' || $this->company_role == 'sub' || $this->company_role == 'super' ) { ?>
	  	<li <?php if($this->router->class == 'orders'): ?>class="current"<?php endif; ?>><a title="<?php echo _('Orders'); ?>" href="<?php echo base_url(); ?>cp/orders"><?php echo _('Orders'); ?></a></li>
		<?php } ?>

		<?php if( $this->company_role == 'master' || $this->company_role == 'super' ) { ?>
	  	<li <?php if($this->router->class == 'categories'): ?>class="current"<?php endif; ?>><a title="<?php echo _('Manage Categories'); ?>" href="<?php echo base_url(); ?>cp/categories"><?php echo _('Categories'); ?></a></li>
		<?php } ?>

		<?php if( $this->company_role == 'master' || $this->company_role == 'super' ) { ?>
	  	<li <?php if($this->router->class == 'subcategories'): ?>class="current"<?php endif; ?>><a title="<?php echo _('Manage Subcategories'); ?>" href="<?php echo base_url(); ?>cp/subcategories"><?php echo _('Subcategories'); ?></a></li>
		<?php } ?>

		<?php if( $this->company_role == 'master' || $this->company_role == 'super' ) { ?>
	  	<li <?php if($this->router->class == 'products'): ?>class="current"<?php endif; ?>><a title="<?php echo _('Manage Products'); ?>" href="<?php echo base_url(); ?>cp/products"><?php echo _('Products'); ?></a></li>
		<?php } ?>

	  	<li <?php if($this->router->class == 'clients'): ?>class="current"<?php endif; ?>><a title="<?php echo _('Manage Clients'); ?>" href="<?php echo base_url(); ?>cp/clients"><?php echo _('Clients'); ?></a></li>

		<?php if( $this->company_role == 'super' ) { ?>
	  	<li <?php if($this->router->class== "cdashboard" && $this->router->method == 'sub_admins'): ?>class="current"<?php endif; ?>><a title="<?php echo _('Manage Sub Admins'); ?>" href="<?php echo base_url(); ?>cp/cdashboard/sub_admins"><?php echo _('Sub Admins'); ?></a></li>
		<?php } ?>

	  	<li <?php if($this->router->class == 'settings'): ?>class="current"<?php endif; ?>><a title="<?php echo _('Manage Site Settings'); ?>" href="<?php echo base_url(); ?>cp/settings"><?php echo _('Site Settings'); ?></a></li>

		<?php if($this->company->obsdesk_status) { ?><li <?php if($this->router->class == 'desk'): ?>class="current"<?php endif; ?>><a title="<?php echo _('Desk Setting'); ?>" href="<?php echo base_url(); ?>cp/desk/settings"><strong><?php echo _('INFOdesk')?></strong></a></li><?php }?>
	 						<!-- fdd_premium -->
		<?php
	}elseif( $menu_type == 'pro' || $menu_type == 'fdd_pro' || $menu_type == 'fdd_light' || $menu_type == 'fdd_premium') { ?>

	<?php if(!$this->session->userdata('show_hide')):?><!-- chech -->
  	<li <?php if( $this->router->class == 'cdashboard' && $this->router->method == 'index' ): ?>class="current"<?php endif; ?> class="intro"><a title="<?php echo _('Intro'); ?>" href="<?php echo base_url(); ?>cp/"><?php echo _('Intro'); ?></a></li>
	<?php endif;?>

	<?php if( $this->company_role == 'master' || $this->company_role == 'sub' || $this->company_role == 'super' ) { ?>
  	<li <?php if($this->router->class == 'orders' || $this->router->method == 'orders' ): ?>class="current"<?php endif; ?>><a title="<?php echo _('Orders'); ?>" href="<?php echo base_url(); ?>cp/orders"><?php echo _('Orders'); ?></a></li>
	<?php } ?>

	<?php if( $this->company_role == 'master' || $this->company_role == 'super' ) { ?>
  	<li <?php if($this->router->class == 'categories'): ?>class="current"<?php endif; ?>><a title="<?php echo _('Manage Categories'); ?>" href="<?php echo base_url(); ?>cp/categories"><?php echo _('Categories'); ?></a></li>
	<?php } ?>

	<?php if( $this->company_role == 'master' || $this->company_role == 'super' ) { ?>
  	<li <?php if($this->router->class == 'subcategories'): ?>class="current"<?php endif; ?>><a title="<?php echo _('Manage Subcategories'); ?>" href="<?php echo base_url(); ?>cp/subcategories"><?php echo _('Subcategories'); ?></a></li>
	<?php } ?>

	<?php if( $this->company_role == 'master' || $this->company_role == 'super' ) { ?>
  	<li <?php if($this->router->class == 'products'): ?>class="current"<?php endif; ?>><a title="<?php echo _('Manage Products'); ?>" href="<?php echo base_url(); ?>cp/products"><?php echo _('Products'); ?></a></li>
	<?php } ?>

  	<li <?php if($this->router->class == 'clients'): ?>class="current"<?php endif; ?>><a title="<?php echo _('Manage Clients'); ?>" href="<?php echo base_url(); ?>cp/clients"><?php echo _('Clients'); ?></a></li>

	<?php if( $this->company_role == 'super' ) { ?>
  	<li <?php if($this->router->class == 'cdashboard' && $this->router->method == 'sub_admins'): ?>class="current"<?php endif; ?>><a title="<?php echo _('Manage Sub Admins'); ?>" href="<?php echo base_url(); ?>cp/cdashboard/sub_admins"><?php echo _('Sub Admins'); ?></a></li>
	<?php } ?>

  	<li <?php if($this->router->class == 'settings'): ?>class="current"<?php endif; ?>><a title="<?php echo _('Manage Site Settings'); ?>" href="<?php echo base_url(); ?>cp/settings"><?php echo _('Site Settings'); ?></a></li>

	<?php /*if( $this->company_role == 'master' || $this->company_role == 'super' ) { ?>
  	<li <?php if($this->router->method == 'changepassword'): ?>class="current"<?php endif; ?>><a title="<?php echo _('Change Password'); ?>" href="<?php echo base_url(); ?>cp/cdashboard/changepassword"><?php echo _('Change Password'); ?></a></li>
	<?php } ?>

	<?php if( $this->company_role == 'master' || $this->company_role == 'super' ) { ?>
  	<li <?php if($this->router->method=="section_designs"):?>class="current" <?php endif;?>><a title="<?php echo _('Change Design')?>" href="<?php echo base_url()?>cp/cdashboard/section_designs"><?php echo _('Change Design')?></a></li>
	<?php }*/ ?>

	<?php if($this->company->obsdesk_status) { ?><li <?php if($this->router->class == 'desk' && $this->router->method == 'settings' ): ?>class="current"<?php endif; ?>><a title="<?php echo _('Desk Setting'); ?>" href="<?php echo base_url(); ?>cp/desk/settings"><strong><?php echo _('infoDESK')?></strong></a></li><?php }?>

<?php } ?>
</ul>
<ul id="sub-nav">
<!-- <li class="current"><a href="#">Submenu1</a></li>  <li><a href="#">Submenu2</a></li>  <li><a href="#">Submenu3</a></li>-->
</ul>
<div style="display:none" name="upgrade_ie" id="upgrade_ie">
  <div style="padding-top:5px;color:#FF0000; font-size:14px; font-weight:bold;padding-left:10px;"><img width="25" height="25" border="0" src="<?php echo base_url(); ?>assets/cp/images/warning.gif">&nbsp;&nbsp;<?php echo _('You are using an older version of IE. Please check your browser first to upgrade to IE8')?></div>
</div>
<!-- MENU -->
<script type="text/javascript">if(navigator.userAgent){	if(navigator.userAgent.version<8){		var id = jQuery('#upgrade_ie').attr('id');		jQuery('#upgrade_ie').show();	}else{		jQuery('#upgrade_ie').hide();	}}</script>
<style type="text/css">
	.rotate_div {
    position: absolute;
    left: -120px;

    top: 0;
}

#uploaded_image {
    position: relative;
}

</style>
