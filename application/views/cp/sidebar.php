<?php if($this->router->class != "mail_manager"){ ?>
<link rel='stylesheet' type='text/css' href='<?php echo base_url()?>assets/cp/css/theme.css?version=<?php echo version;?>'/>
<link rel='stylesheet' type='text/css' href='<?php echo base_url()?>assets/cp/css/fullcalendar.css?version=<?php echo version;?>'/>
<link rel='stylesheet' type='text/css' href='<?php echo base_url()?>assets/cp/css/thickbox.css?version=<?php echo version;?>'/>

<script src='<?php echo base_url()?>assets/cp/js/lib/moment.min.js?version=<?php echo version;?>'></script>
<!--<script src='<?php echo base_url()?>assets/cp/js/lib/jquery.min.js'></script>
<script src="<?php echo base_url()?>assets/cp/js/lib/jquery-ui.custom.min.js"></script>-->
<script src='<?php echo base_url()?>assets/cp/js/fullcalendar.min.js?version=<?php echo version;?>'></script>
<script src="<?php echo base_url()?>assets/cp/js/lang-all.js?version=<?php echo version;?>"></script>

<script type='text/javascript'>

	//---1.this ias to add holiday class on the dates which are declared as holiday---//

	function jscalls(){
 		jQuery.post("<?php echo base_url()?>cp/calender/get_calender_holiday_close_dates",
			{'month':jQuery('.fc-center h2').text().split(" ")[0],'year':jQuery('.fc-center h2').text().split(" ")[1]},
			function(data){
  				jQuery('.fc-day-number').each(function(){
					if(jQuery(this).hasClass('holiday')){
						jQuery(this).removeClass('holiday');
					}
					for(var i=0;i<data['holidays'].length;i++){
						if(parseInt(data['holidays'][i]) == parseInt(jQuery(this).text()) && !jQuery(this).hasClass('fc-other-month')){
							jQuery(this).addClass('holiday');
						}
					}
					if(jQuery(this).hasClass('closed')){

						jQuery(this).removeClass('closed');

					}
					for(var i=0;i<data['shop_closed'].length;i++){
						if(parseInt(data['shop_closed'][i]) == parseInt(jQuery(this).text()) && !jQuery(this).hasClass('fc-other-month')){
							jQuery(this).addClass('closed');
						}
					}
				});
 		},'json');
   //-----------------------------------------------------------------------------//

   //--------2.this is to add pickup delivery closed days in calender--------//

		<?php
			$pickup_delivery_closed = get_pickup_delivery_closed();
			if(isset($pickup_delivery_closed) && $pickup_delivery_closed ):foreach ($pickup_delivery_closed as $key=>$val): ?>
				jQuery('td.fc-<?php echo strtolower($key);?>').each(function(){
				if(!jQuery(this).hasClass('fc-other-month')){
					jQuery(this).addClass('<?php echo $val;?>');
				}else{
					if(jQuery(this).hasClass('delivery')){
						jQuery(this).removeClass('delivery');

					}
					if(jQuery(this).hasClass('BOth')){

						jQuery(this).removeClass('BOth');

					}
					if(jQuery(this).hasClass('pickup')){

						jQuery(this).removeClass('pickup');
					}
				}
			});
		<?php endforeach;endif; ?>

		jQuery('.fc-day-number').each(function(){
			if(jQuery(this).hasClass('fc-today')){
				jQuery(this).addClass('today');
			}else if(jQuery(this).hasClass('today')){
				jQuery(this).removeClass('today');
			}
		});

		<?php
				$custom_pickup_closed = get_custom_pickup_closed();
				if( isset($custom_pickup_closed) && $custom_pickup_closed ) {
			 		foreach ($custom_pickup_closed as $key => $value) { ?>
			 			jQuery('.fc-day-number').each(function() {
			 				var val = "<?php echo $key; ?>";
			 				var clas = "<?php echo strtolower($value); ?>";
							if( jQuery(this).attr('data-date') == val && !jQuery(this).hasClass('fc-other-month') ) {
								if(jQuery(this).hasClass( 'delivery' ) &&  clas == 'both' ) {
									jQuery(this).removeClass( 'delivery' );

								}
								if(jQuery(this).hasClass('BOth')){
									jQuery(this).removeClass('BOth');

								}
								if(jQuery(this).hasClass('pickup')){
									jQuery(this).removeClass('pickup');
								}
								jQuery(this).addClass(clas);
							}
						});
		<?php 		}
				}
			?>


	}
//----------3.this is to show calender using jquery calender--------------//
	jQuery(document).ready(function() {
		var date = new Date();
		//alert(date);
		var d = date.getDate();
		//alert(d);
		var m = date.getMonth();
		var y = date.getFullYear();
			if(Get_Cookie('locale') == "en_US"){
				jQuery('#calendar').fullCalendar({
					theme: true,
					header: {
						left: 'prev',
						center: 'title',
						right: 'next'
					},
					editable: true
				});
			}else{
				jQuery('#calendar').fullCalendar({
					theme: true,
					header: {
						left: 'prev',
						center: 'title',
						right: 'next'
					},
					editable: true,
					monthNames:['Januari','Februari','Maart','April','Mei','Juni','Juli','Augustus','September','Oktober','November','December'],
					monthNamesShort: ['Jan','Feb','Maa','Apr','Mei','Jun','Jul','Aug','Sep','Okt','Nov','Dec'],
					dayNames: ['Zondag','Maandag','Dinsdag','Woensdag','Donderdag','Vrijdag','Zaterdag'],
					dayNamesShort: ['Zo','Ma','Di','Wo','Do','Vr','Za']
				});
			}

		jscalls();
		// calling this function to show holidays when sidebar gets loaded -->

		jQuery('.ui-widget-content').each(function(){
		   jQuery(this).css('position','relative');
		});

        //-------4.this will show the thickbox when a date is clicked--------//
		jQuery('#sidebar').on("click",".fc-content-skeleton table tr td.fc-day-number",function(){
		    if(!jQuery(this).hasClass('td.fc-other-month')){
				if(jQuery(this).hasClass('delivery')||jQuery(this).hasClass('pickup')||jQuery(this).hasClass('BOth')){
	   			  alert("Can\'t modify.Change from site settings.");
				}else{

                    jQuery('.fc-day-content').fadeOut();

					var date=jQuery(this).text();

					//alert(date);
					//date1 = unescape(date);//this command to remove space(cming in internet explorer)
					date = date.replace(/^[\s]+/,'').replace(/[\s]+$/,'').replace(/[\s]{2,}/,'').replace(/&nbsp;/g,'');
					//alert(escape(date));
					//alert(unescape(date));

					var month=jQuery('.fc-center h2').text().split(" ")[0];
					var year=jQuery('.fc-center h2').text().split(" ")[1];

					var months=new Array(12);
					months["January"]="01";
					months["February"]='02';
					months["March"]="03";
					months["April"]="04";
					months["May"]="05";
					months["June"]="06";
					months["July"]="07";
					months["August"]="08";
					months["September"]="09";
					months["October"]="10";
					months["November"]="11";
					months["December"]="12";

					months["Januari"]="01";
					months["Februari"]='02';
					months["Maart"]="03";
					months["April"]="04";
					months["Mei"]="05";
					months["Juni"]="06";
					months["Juli"]="07";
					months["Augustus"]="08";
					months["September"]="09";
					months["Oktober"]="10";
					months["November"]="11";
					months["December"]="12";

					if(date.length == 1)
						date = "0"+date;
					var date_str = date+'/'+months[month]+'/'+year;
					var html_txt = "<a  style='float:right;' onclick='close_dialog(this,event)'><img src='<?php echo base_url()?>assets/cp/images/icon_close.gif'></a><br style='clear:both;'><ul><li style='text-align:left;'><a href='javascript:void(0)' onClick='set_closed(\""+date_str+"\",\""+date+"\",this,event)'><?php echo _('Closed')?></a></li><li style='text-align:left;'><a href='javascript:void(0)' onclick='set_holiday(\""+date_str+"\",\""+date+"\",this,event)'><?php echo _('Holiday')?></a></li></ul>";

					jQuery(this).find('.fc-day-content').html(html_txt);
					jQuery(this).find('.fc-day-content').fadeIn();

					//tb_show('choose option',"<?php echo site_url('cp/calender/get_option'); ?>/"+date+"/"+month+"/"+year+"/?keepThis=true&TB_iframe=true&height=100&width=100",null);

				}
			}
		});

		jQuery('.fc-button-prev').click(function(){jscalls();});<!--jscalls() to show holidays on the previous month-->
		jQuery('.fc-button-next').click(function(){jscalls();});<!--jscalls() to show holidays in next month-->
	});

	function close_dialog(obj,e){
		e.stopPropagation();
		jQuery(obj).parent().hide();
	}

	function set_holiday(date,d,obj,e){
		e.stopPropagation();
	    d = parseInt(d)-1;

	    //alert('set holiday : '+d);

		jQuery.post("<?php echo  base_url();?>cp/calender/set_holiday",
				{'holiday_date':date},
				function(data){
					if(data.trim()!="successsfully_updated"){
						alert("<?php echo _('Error Occured')?>");
					}
					else
					{
					    //parent.location.reload();

						jscalls();

						jQuery('.fc-day-content').css('display','none');

						/*if( jQuery('td.fc-day'+d+' div div.fc-day-number').hasClass('holiday') ){

							jQuery('td.fc-day'+d+' div div.fc-day-number').removeClass('holiday');
				            alert("<?php echo _('Holiday removed successfully.'); ?>");
						}
						else
						{
				            jQuery('td.fc-day'+d+' div div.fc-day-number').addClass('holiday');
						    alert("<?php echo _('Holiday set successfully.'); ?>");
						}*/
		  				var shop_version = jQuery('#shop_version').val();
						if(shop_version == 2 || shop_version == 3){
							jQuery.post(
				    			base_url+"cp/shop_all/update_json_files/"+shop_version,
				    			{'action':'general_setting_json'},
				    			function(data){},
				    			'json'
				    		);
						}
					}
				}
		);

	}

	function set_closed(date,d,obj,e){
		e.stopPropagation();
		d = parseInt(d)-1;

		//alert('set closed : '+d);

		jQuery.post("<?php echo  base_url();?>cp/calender/set_closed",
				{'close_date':date},
				function(data){
					if(data.trim()!="successsfully_updated"){
						alert("<?php echo _('Error Occured');?>");
					}
					else
					{
					    //parent.location.reload();

						jscalls();

						jQuery('.fc-day-content').css('display','none');

						/*if( jQuery('td.fc-day'+d+' div div.fc-day-number').hasClass('closed') ){

							jQuery('td.fc-day'+d+' div div.fc-day-number').removeClass('closed');
				            alert("<?php echo _('Date is not closed now.'); ?>");
						}
						else
						{
				            jQuery('td.fc-day'+d+' div div.fc-day-number').addClass('closed');
						    alert("<?php echo _('Date closed set successfully.'); ?>");
						}*/
		  				var shop_version = jQuery('#shop_version').val();
						if(shop_version == 2 || shop_version == 3){
							jQuery.post(
				    			base_url+"cp/shop_all/update_json_files/"+shop_version,
				    			{'action':'general_setting_json'},
				    			function(data){},
				    			'json'
				    		);
						}
					}
				}
		);
	}

	/*jQuery(document).ready(function(){
		var shop_version = jQuery('#shop_version').val();
		if(shop_version == 2 || shop_version == 3){
			jQuery('#upd_shop_drct').parent().show();

			jQuery('#upd_shop_drct').click(function(){
				jQuery('#loadmsg').show();
				jQuery.post(
					base_url+"cp/shop_all/update_json_files/"+shop_version,
				    {'action':'general_setting_json'},
				    function(data){
				    	jQuery('#loadmsg').hide();
					},
				    'json'
				);
			});
		}
	});*/
</script>
<style type='text/css'>

	body {
		margin-top: 40px;
		text-align: center;
		font-size: 13px;
		font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
		}

	#calendar {
		width: 240px;

		margin: 0 auto;
	}
	.holiday{
			background-image:url("<?php echo base_url()?>assets/cp/images/holiday.jpg") !important;
			background-repeat: no-repeat !important;
			font-weight: normal;
	}
	.closed{

			background-image:url("<?php echo base_url()?>assets/cp/images/closed.jpg") !important;
			background-repeat: no-repeat !important;
			font-weight: normal;
	}
	.delivery {
   		 	background-image: url("<?php echo base_url()?>assets/cp/images/calBgDelivery.jpg") !important;
			background-repeat: no-repeat !important;

	}
	.pickup {
   			 background-image: url("<?php echo base_url()?>assets/cp/images/calBgpickup.jpg") !important;
			 background-repeat: no-repeat !important;

	}
	.BOth {
   			 background-image: url("<?php echo base_url()?>assets/cp/images/calBgBoth.jpg") !important;
			 background-repeat: no-repeat !important;
   	}
	.today {
			background-color: transparent !important;
			background-image: url("<?php echo base_url()?>assets/cp/images/calBg.jpg") !important;
			background-repeat: no-repeat !important;
	}
/* 	.fc-today { */
/* 			background-color: transparent !important; */
			background-image: url("<?php //echo base_url()?>assets/cp/images/calBg.jpg") !important;
/* 			background-repeat: no-repeat !important; */
/* 	} */

    .fc-day-content {
	        display:none;
	        background: none repeat scroll 0 0 #FFFFFF;
			border: 1px solid #CCCCCC;
			box-shadow: 2px 2px 2px #CCCCCC;
			/*height: 40px;*/
			left: 20px;
			padding: 5px;
			position: absolute;
			top: 18px;
			width: 80px;
			z-index: 1000;

	}
/* 	.fc-row{ */
/* 		height: 0px; */
/* 	} */
/* 	.fc-day-number{ */
/* 		text-align: center !important; */
/* 	} */
	.fc-view{
	       overflow:visible;
	}
	.box, .boxed{
		   overflow:visible;
	}

	.fc th, .fc td {
   		border-style: solid;
    	border-width: 1px;
    	padding: 5px 0 4px;
    	vertical-align: top;
	}
	.fc-scroller.fc-day-grid-container {
    	height: auto !important;
    	overflow: hidden !important;
	}
	table tbody tr{
		padding-right: 7px!important;
	}
	.fc-basic-view .fc-body .fc-row {
    	min-height: 0em !important;
	}
	.fc-basic-view td.fc-week-number span, .fc-basic-view td.fc-day-number {
  		padding-bottom: 2px;
    	padding-top: 2px;
    	padding-right: 11px !important;
	}
	.fc-head th {
    	background-color: black !important;
	}
	.fc-toolbar{
		margin-bottom: 0em!important;
	}
	.fc-toolbar h2{
		margin-top: 6px!important;
	}
	.fc-row .fc-content-skeleton {
    	padding-bottom: 0px!important;
    	position: relative;
    	z-index: 4;
	}
	.fc-basic-view td.fc-week-number span, .fc-basic-view td.fc-day-number {
    	line-height: 30px;
    	padding-bottom: 0;
   	 	padding-right: 11px !important;
    	padding-top: 0;
	}
	.fc-content-skeleton .fc-day-number {
    	cursor: pointer;
	}
	.ui-widget-header {
	    background: #e9e9e9 none repeat scroll 0 0 !important;
	    border: 1px solid #ddd;
	    color: #e9e9e9;
	    font-weight: bold;
	}
	#sidebar .fc-view.fc-month-view.fc-basic-view table tr td div {
    	position: static;
	}
	#sidebar .fc-content-skeleton {
	    border-bottom: 1px solid #eee;
	    padding-top: 5px;
	}
	#sidebar table tr td .fc-row.fc-week.ui-widget-content{
		position:static!important;
	}
	#sidebar table tr td .fc-day-content {
	    background-color: #fff;
	    position: absolute !important;
	    width: 70px;
	    z-index: 9999;
	}
	#sidebar .fc-row.fc-week.ui-widget-content > .fc-bg {
	    display: none;
	}
	.ui-widget-content{
		background:  #fff!important;
	}
	.fc-day-content{
		border:1px solid #cccccc;
		box-shadow:2px 2px 2px #cccccc;
		line-height: 15px;
		padding: 2px 2px 1px;
	}
/* 	#sidebar table tbody  .fc-day-content{ */
/* 		border: 1px solid #cccccc; */
/*     } */
	/* thead > tr > td, tfoot > tr > td {
    padding: 0px  !important;

	}*/
	.ui-widget-header {
  background: rgba(0, 0, 0, 0) none repeat scroll 0 0 !important;
  border: medium none;
}
#calendar .ui-widget-header table thead tr th ,
#calendar table tbody div.fc-content-skeleton {
  border: medium none;
}
#calendar table thead tr td.fc-head-container ,
#calendar table tbody tr td.ui-widget-content,
#calendar table tbody .fc-content-skeleton td,
#calendar table tbody .fc-content-skeleton {
  padding: 0;
}
#calendar table td {
  padding: 0 !important;
  text-align: center;
}
#calendar table tr .closed,
#calendar table tr .holiday{
  font-weight: bold !important;
}
#calendar table td.fc-other-month > .fc-day-content {
  display: none !important;
}
#calendar table tbody tr td .fc-scroller.fc-day-grid-container {
  overflow: visible !important;
}
</style>
<?php }?>
<body>
<div id="sidebar">
<div id="loadmsg" style="display:none;margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;">
  <img src="<?php echo base_url(''); ?>assets/cp/images/ajax-loading.gif" style="position: absolute; color: White; top: 50%; left: 45%;"/>
</div>
<!-- RIGHT -->
	<?php if($this->router->class != "mail_manager"){?>
	<div class="box">
    	<h3  id="r_links"><?php echo _('Links')?> </h3>
        <div style="display: block;" class="inside">
          	<ul>
         	<?php
         	$show_video = false;
         	if(
         		(
					$this->company->ac_type_id == 4 ||
					$this->company->ac_type_id == 5 ||
					$this->company->ac_type_id == 6
				)
         		&&
         		(
					(
						$this->router->fetch_class() == 'cdashboard'
						&&
						(
							$this->router->fetch_method()=='semi_products' ||
							$this->router->fetch_method()=='semi_product_addedit' ||
							$this->router->fetch_method()=='products' ||
							$this->router->fetch_method()=='products_addedit' ||
							$this->router->fetch_method()=='empty_ingredients' ||
							$this->router->fetch_method()=='custom_pending' ||
							$this->router->fetch_method()=='product_recipe'||
							$this->router->fetch_method()=='login_details' ||
							$this->router->fetch_method()=='semi_products_extra'||
							$this->router->fetch_method()=='semi_product_addedit_new'
						)
					)
         			||
         			(
         				$this->router->fetch_class() == 'fooddesk'
						&&
						(
							$this->router->fetch_method()=='add_new_product' ||
							$this->router->fetch_method()=='log'
						)
					)
					||
					(
						$this->router->fetch_class() == 'products'
						&&
						(
							$this->router->fetch_method()=='semi_products' ||
							$this->router->fetch_method()=='semi_product_addedit' ||
							$this->router->fetch_method()=='lijst' ||
							$this->router->fetch_method()=='products_addedit' ||
							$this->router->fetch_method()=='empty_ingredients' ||
							$this->router->fetch_method()=='custom_pending' ||
							$this->router->fetch_method()=='product_recipe'||
							$this->router->fetch_method()=='login_details' ||
							$this->router->fetch_method()=='index' ||
							$this->router->fetch_method()=='semi_products_extra'||
							$this->router->fetch_method()=='semi_product_addedit_new' ||
							$this->router->fetch_method()=='favourite_products'
						)
					)
				)
			){
			$show_video = true;
			?>
         		<li><a target="_blank" href="https://sitematic.wetransfer.com/"  title="<?php echo _('Uploads productsheets as suggestion to add new FoodDESK produts');?>"  ><strong><?php echo _('Uploads productsheets');?></strong></a></li>
         		<li><a href="<?php echo base_url().'cp/products/empty_ingredients'?>"  title="<?php echo _('products having no ingredients');?>"  ><strong><?php echo _('List of Products without sheets');?>(<?php echo get_pending_product_count($this->company_id); ?>)</strong></a></li>
         		<li><a href="<?php echo base_url().'cp/products/favourite_products'?>"  title="<?php echo _('Semi-products');?>"  ><strong><?php echo _('Favourite-products');?></strong></a></li>
         		<li><a href="<?php echo base_url().'cp/products/semi_products'?>"  title="<?php echo _('Semi-products');?>"  ><strong><?php echo _('Semi-products');?></strong></a></li>
         		<li><a href="<?php echo base_url().'cp/products/semi_products_extra'?>"  title="<?php echo _('Semi-products EXTRA');?>"  ><strong><?php echo _('Semi-products EXTRA');?></strong></a></li>
         		<li><a href="<?php echo base_url().'cp/cdashboard/login_details'?>"  title="<?php echo _('Login Details');?>"  ><strong><?php echo _('Login Details');?></strong></a></li>
         		<li><a href="<?php echo base_url().'cp/fooddesk/log'?>"  title="<?php echo _('List of Fooddesk products approved within 7 days');?>"  ><strong><?php echo _('FoodDESK Log');?></strong></a></li>

         	<?php }else{?>
	            <li><a target="_blank" href="https://www.facebook.com/groups/FoodDESK/"><strong><?php echo _('Latest News')?> (FB)</strong></a></li>
            	<!-- <li><a href="<?php //echo base_url()?>cp/cdashboard/version"><strong><?php //echo _('Changelog: Latest version')?><span style="color:#AA0000">&nbsp;2.6.7</span></strong></a></li>  -->
				<li><a href="<?php echo base_url()?>cp/cdashboard/dwnld_labels"><strong><?php echo _('Downloads - Labels')?></strong></a></li>


            <?php /*if(($this->router->fetch_class() == 'cdashboard') && ($this->router->fetch_method()=='categories' || $this->router->fetch_method()=='subcategories' || $this->router->fetch_method()=='assign_category')){?>
         		<li><a href="<?php echo base_url()?>cp/cdashboard/assign_category"><strong><?php echo _('Snel product toewijzen aan categorie')?></strong></a></li>
         	<?php } */?>

	         	 <?php if($this->router->fetch_class() == 'categories' || $this->router->fetch_class()=='subcategories' || $this->router->fetch_method()=='assign_category'){?>
	         		<li><a href="<?php echo base_url()?>cp/products/assign_category/"><strong><?php echo _('Snel product toewijzen aan categorie')?></strong></a></li>
	         	<?php }?>
            <?php }?>

			<?php if($this->ibsoft_active) { ?><li><a href="<?php echo base_url()?>cp/cdashboard/ibsoft_module"><strong><?php echo _('IBSoft Module'); ?></strong></a></li><?php }
               else { ?><!-- <li><a href="<?php echo base_url()?>cp/cdashboard/addons"><strong><?php echo _('ADDONS'); ?></strong></a></li>--><?php } ?>
           <?php if($this->router->fetch_class() == 'products'){?>

           <?php if($this->company->ac_type_id == 4 || $this->company->ac_type_id == 5 || $this->company->ac_type_id == 6){?>
            	  <li><a target="_blank" href="<?php echo base_url()?>cp/cdashboard/report_export"><strong><?php echo _('Report Export'); ?></strong></a></li>
            <?php }?>
           <?php }else{?>
           		<li><a href="<?php echo base_url()?>cp/ftp_settings"><strong><?php echo _('FTP Settings'); ?></strong></a></li>
           <?php }?>
            <?php if($show_video){?>
            	<li><a target="_blank" href="<?php echo base_url().'cp/cdashboard/video_tutorial'; ?>"><strong><?php echo _('Video Link'); ?></strong></a></li>
            <?php }?>
            <li style="display:none;"><a id="upd_shop_drct" href="javascript:;"><strong><?php echo _('Update Shop'); ?></strong></a></li>
          	</ul>
        </div>
	</div>

		<!-- <div class="box">
	        <div style="display: block;" class="inside">
	        	<ul>
	        		<li>
	        			<img src="<?php echo base_url();?>assets/cp/images/clients_mail.png" style="vertical-align: middle;" alt="<?php echo _("Mail Manager");?>" /> <a href="<?php echo base_url()?>cp/mail_manager"><strong><?php echo _("MAIL MANAGER");?></strong></a>
	        		</li>
	        	</ul>
	        </div>
		</div> -->
	<?php if( $this->company_role == 'master' || $this->company_role == 'sub' ) { ?>
    <div class="box">
    	<h3  id="r_calender"><?php echo _('Calendar')?></h3>
        <div style="padding: 0px; display: block;" class="inside">
        	<table width="100%" cellspacing="0" cellpadding="0" border="1" class="calender">
            	<tbody>
              		<tr>
			 			<div id='calendar'></div>
              		</tr>
					<tr>
                		<td style="text-align:right" colspan="7"><span style="cursor:pointer; color:#6666FF; font-size:12px;" onclick="javascript: jQuery('#legends').toggle();" id="legend"><strong><?php echo _('Legend')?></strong></span>
                  			<div id="legends" style="padding: 0px; display: none;" class="inside">
                    			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="override">
                      				<tbody>
                        				<tr>
                          					<td style="padding:0px 0px 0px 0px;text-align:center" colspan="1"><img width="30" height="25" border="0" src="<?php echo base_url(); ?>assets/cp/images/calBg.jpg"></td>
                          					<td align="left" style="padding:0px 0px 0px 0px;text-align:left" colspan="6">&nbsp;&nbsp;<span style="vertical-align:middle"> <strong><?php echo _('Current Date')?></strong></span></td>
                        				</tr>
                        				<tr>
                          					<td style="padding:0px 0px 0px 0px;text-align:center;" colspan="1"><img width="30" height="25" border="0" src="<?php echo base_url(); ?>assets/cp/images/calBgpickup.jpg"></td>
                          					<td align="left" style="padding:0px 0px 0px 0px;text-align:left" colspan="6">&nbsp;&nbsp;<span style="vertical-align:middle"> <strong><?php echo _('Pick Closed')?></strong></span></td>
                        				</tr>
                        				<tr>
                          					<td style="padding:0px 0px 0px 0px;text-align:center;" colspan="1"><img width="30" height="25" border="0" src="<?php echo base_url(); ?>assets/cp/images/calBgDelivery.jpg"></td>
                          					<td align="left" style="padding:0px 0px 0px 0px;text-align:left" colspan="6">&nbsp;&nbsp;<span style="vertical-align:middle"> <strong><?php echo _('Delivery Closed')?></strong></span></td>
                        				</tr>
                        				<tr>
                          					<td style="padding:0px 0px 0px 0px;text-align:center;" colspan="1"><img width="30" height="25" border="0" src="<?php echo base_url(); ?>assets/cp/images/calBgBoth.jpg"></td>
                          					<td align="left" style="padding:0px 0px 0px 0px;text-align:left" colspan="6">&nbsp;&nbsp;<span style="vertical-align:middle"><strong><?php echo _('Pickup and delivery closed')?></strong></span></td>
                        				</tr>
                        				<tr>
                          					<td style="padding:0px 0px 0px 0px;text-align:center;" colspan="1"><img width="30" height="25" border="0" src="<?php echo base_url(); ?>assets/cp/images/closed.jpg"></td>
                          					<td align="left" style="padding:0px 0px 0px 0px;text-align:left" colspan="6">&nbsp;&nbsp;<span style="vertical-align:middle"><strong><?php echo _('Manually closed')?></strong></span></td>
                        				</tr>
                        				<tr>
                          					<td style="padding:0px 0px 0px 0px;text-align:center;" colspan="1"><img width="30" height="25" border="0" src="<?php echo base_url(); ?>assets/cp/images/holiday.jpg"></td>
                          					<td align="left" style="padding:0px 0px 0px 0px;text-align:left" colspan="6">&nbsp;&nbsp;<span style="vertical-align:middle"><strong><?php echo _('Holiday')?></strong></span></td>
                        				</tr>
                      				</tbody>
                    			</table>
                  			</div></td>
              		</tr>
            	</tbody>
          	</table>
        </div>
	</div>

		<?php } ?>
	<?php }else{?>

		<div class="box">
	    	<h3  id="r_links"><?php echo _('Links')?> </h3>
	        <div style="display: block;" class="inside">
	          <ul>
	            <li><a href="<?php echo base_url()?>cp/mail_manager"><strong><?php echo _('Dashboard')?></strong></a></li>
	            <li><a href="<?php echo base_url()?>cp/mail_manager/subscribers"><strong><?php echo _('Subscribers')?></strong></a></li>
	            <li><a href="<?php echo base_url()?>cp/mail_manager/templates"><strong><?php echo _('Templates')?></strong></a></li>
				<li><a href="<?php echo base_url()?>cp/mail_manager/newsLetters"><strong><?php echo _('News Letters')?></strong></a></li>
				<li><a href="<?php echo base_url()?>cp/mail_manager/faq"><strong><?php echo _('FAQ')?></strong></a></li>
				<li><a href="<?php echo base_url()?>cp/mail_manager/image_manager"><strong><?php echo _('Image Manager')?></strong></a></li>
				<li><a href="<?php echo base_url()?>cp/mail_manager/doc_manager"><strong><?php echo _('Doc Manager')?></strong></a></li>
	          </ul>
	        </div>
		</div>

		<div class="box">
	        <div style="display: block;" class="inside">
	        	<ul>
	        		<li>
	        			<a href="javascript:;"><strong><?php echo _("STATS");?></strong></a>
	        			<span style="display: block; margin: 2px auto;">
	        				<?php echo _("Status");?> :
	        				<?php
	        					if($this->status == 'free'){
	        						echo _('Trial (first 20 for free)');
	        					}elseif($this->status == 'not_active'){
	        						echo _('Not activated (please').' <a href="'.base_url().'cp/mail_manager">'._('select an option').'</a>)';
	        					}elseif($this->status == 'credits'){
									if(!empty($this->subscription)){
										$subscription = $this->subscription['0'];
										if(($subscription['credits'] - $subscription['mail_sent_for_current_type']) >= 0 ){
											echo ($subscription['credits'] - $subscription['mail_sent_for_current_type'])._(' credits left');
										}else{
											echo '0'._(' credits left');
										}
									}
	        					}else{
	        						echo _('Monthly');
	        					}
	        				?>
	        			</span>
	        			<span style="display: block; margin: 2px auto;"><?php echo _("Subscribers");?> : <?php echo $this->subscriber_count;?></span>
	        			<span style="display: block; margin: 2px auto;"><?php echo _("Templates");?> : <?php echo count($this->templates); ?></span>
	        		</li>
	        	</ul>
	        </div>
		</div>

	<?php }?>
	<?php if($this->router->class == "mail_manager"){?>
		<div style="font-size: 14px;padding:20px;text-align:center;width:200px;height:30px;<?php if($this->subscription['0']['mail_type'] == 'not_active'){ echo 'background-color:#EEC1C1;color:#E13838;border:1px solid #D90101;';}else{ echo 'background-color:#DBDBB9;color:#666600;border:1px solid #8C8C01;'; }?>">
			<?php if($this->subscription['0']['mail_type'] != 'credits'){?>
				<p><strong>Unlimited mails&nbsp;<?php if($this->subscriber_count > 20){echo $this->subscriber_count; }?></strong></p>
				<?php if($this->subscription['0']['mail_type'] == 'not_active'){?>
					<p><strong> Not Activated</strong></p>
				<?php }else{?>
					<p><strong>Activated</strong></p>
				<?php }?>
			<?php }else{?>
				<p><strong><?php echo $this->subscription['0']['credits'];?>&nbsp;CREDITS</strong></p>
				<p><strong>LEFT!</strong></p>
			<?php }?>

			<?php /*echo $this->status;?>
			<?php print_r($this->subscription['0']['credits']);?>
			<?php echo $this->subscriber_count;*/?>
		</div>
	<?php }elseif($this->company->ac_type_id == 2 || $this->company->ac_type_id == 3){?>
	<div>
		<!-- <a href="<?php //echo base_url()?>cp/#tabs-4"><img src="<?php //echo base_url()?>assets/images/videotuts.jpg" width="237" height="92"/></a> -->
		<a href="<?php echo base_url()?>cp/cdashboard/video_tutorial"><img src="<?php echo base_url()?>assets/images/videotuts.jpg" width="237" height="92"/></a>
	</div>
    <?php }?>

  <div onclick="bar_show_hide(this.id)" class="box" id="r_photo_store">
        <!--  <h3>Foto shop</h3>
    <div class="inside" style="display:none">
      <div align="center"> <span style="font-size:18px;"><a href="<?php //echo base_url()?>photoscript/index.php" target="_blank">Koop hier uw FOTO's</a></span> </div>
      -->
	</div>
 </div><!--end of sidebar-->
</div><!--end of MAIN-->
<script>
var content=0;
	function getHolidays(){
		jQuery.post("<?php echo base_url()?>cp/calender/get_calender_holiday_close_dates",
			{'month':jQuery('.fc-center h2').text().split(" ")[0],'year':jQuery('.fc-center h2').text().split(" ")[1]},
			function(data){
		  		jQuery('.fc-day-number').each(function(){
					if(jQuery(this).hasClass('holiday')){
						jQuery(this).removeClass('holiday');
					}
					for(var i=0;i<data['holidays'].length;i++){
						if(parseInt(data['holidays'][i]) == parseInt(jQuery(this).text()) && !jQuery(this).hasClass('fc-other-month')){
							jQuery(this).addClass('holiday');
						}
					}
					if(jQuery(this).hasClass('closed')){

						jQuery(this).removeClass('closed');

					}
					for(var i=0;i<data['shop_closed'].length;i++){
						if(parseInt(data['shop_closed'][i]) == parseInt(jQuery(this).text()) && !jQuery(this).hasClass('fc-other-month')){
							jQuery(this).addClass('closed');
						}
					}
				});
	 	},'json');
	}

	function logArrayElements(element, index, array) {
		  console.log('a[' + index + '] = ' + element);
	}


   	jQuery(document).ready(function($){
   		jQuery('.fc-content-skeleton').find(".fc-day-number").each(function(){
   			jQuery(this).append('<div class="fc-day-content"><div style="position:relative"> </div></div>');
    		jQuery('.fc-day-content').css('display','none');

   		});

    	jQuery("#calendar").children().find('.fc-next-button').on('click',function(){
    		<?php $custom_pickup_closed = get_custom_pickup_closed(); ?>
    		jQuery('.fc-content-skeleton').find(".fc-day-number").each(function(){
    			jQuery(this).append('<div class="fc-day-content"><div style="position:relative"></div></div>');
    			jQuery('.fc-day-content').css('display','none');
   			});
    		getHolidays();

    		jQuery.post("<?php echo base_url()?>cp/calender/get_pickup_delivery_closed",
    				function(result_data){
    				var array = $.map(result_data, function(value, index) {
    					jQuery('td.fc-'+index+'').each(function(){
	    					if(!jQuery(this).hasClass('fc-other-month')){
	    						jQuery(this).addClass(value);
				 				var txt='';
				 				var custom_pickup_closed = '<?php echo json_encode($custom_pickup_closed); ?>';
				 				custom_pickup_closed = jQuery.parseJSON(custom_pickup_closed);
				 				var x;
								for (x in custom_pickup_closed) {
						 			jQuery('#calendar').find('.fc-day-number').each(function() {
						 				var val = custom_pickup_closed[x];
						 				var $this = jQuery(this);
										if( $this.attr('data-date') == x && !$this.hasClass('fc-other-month') )
										{
											var classes = $this.attr( 'class' );
											if( $this.hasClass( 'delivery' ) && custom_pickup_closed[x] == 'both' ) {

												$this.removeClass( 'delivery' );
											}
											if( $this.hasClass( 'BOth' ) ) {
												$this.removeClass( 'BOth' );
											}
											if( $this.hasClass( 'pickup' ) ) {
												$this.removeClass( 'pickup' );
											}
											$this.addClass(custom_pickup_closed[x]);
										}
									});
						 		}

	    					}else{
	    						if(jQuery(this).hasClass('delivery')){
	    							jQuery(this).removeClass('delivery');

	    						}
	    					}
    					});
    				});
    	 		},'json');

    		jQuery('.fc-day-number').each(function(){
    			if(!jQuery(this).hasClass('fc-day-number fc-sun fc-other-month')){
    				if(jQuery(this).hasClass('fc-day-number fc-sun')){
    					jQuery(this).removeClass('fc-day-number fc-sun').addClass('fc-day-number fc-sun');
    				}
    				if(jQuery(this).hasClass('fc-today')){
						jQuery(this).addClass('today');
					}else if(jQuery(this).hasClass('today')){
						jQuery(this).removeClass('today');
					}
    			}
    		});

    	});

    	jQuery("#calendar").children().find('.fc-prev-button').on('click',function(){
    		<?php $custom_pickup_closed = get_custom_pickup_closed(); ?>
	    	getHolidays();
	    	jQuery.post("<?php echo base_url()?>cp/calender/get_pickup_delivery_closed",
    			function(result_data){

    				var array = $.map(result_data, function(value, index) {
    					jQuery('td.fc-'+index+'').each(function(){
	    					if(!jQuery(this).hasClass('fc-other-month')){
	    						jQuery(this).addClass(value);
				 				var txt='';
				 				var custom_pickup_closed = '<?php echo json_encode($custom_pickup_closed); ?>';
				 				custom_pickup_closed = jQuery.parseJSON(custom_pickup_closed);
				 				var x;
								for (x in custom_pickup_closed) {
						 			jQuery('#calendar').find('.fc-day-number').each(function() {
						 				var val = custom_pickup_closed[x];
						 				var $this = jQuery(this);
										if( $this.attr('data-date') == x && !$this.hasClass('fc-other-month') )
										{
											if( $this.hasClass( 'delivery' ) && custom_pickup_closed[x] == 'both' ) {

												$this.removeClass( 'delivery' );
											}
											if( $this.hasClass( 'BOth' ) ) {
												$this.removeClass( 'BOth' );
											}
											if( $this.hasClass( 'pickup' ) ) {
												$this.removeClass( 'pickup' );
											}
											$this.addClass(custom_pickup_closed[x]);
										}
									});
						 		}

	    					}else{
	    						if(jQuery(this).hasClass('delivery')){
	    							jQuery(this).removeClass('delivery');

	    						}
	    					}
    					});
    				});
    	 		},'json');
	    	jQuery('.fc-content-skeleton').find(".fc-day-number").each(function(){
	    		jQuery(this).append('<div class="fc-day-content"><div style="position:relative"></div></div>');
	    		jQuery('.fc-day-content').css('display','none');
	   		});
	    	jQuery('.fc-day-number').each(function(){
	    		if(!jQuery(this).hasClass('fc-day-number fc-sun fc-other-month')){
	    			if(jQuery(this).hasClass('fc-day-number fc-sun')){
	    				jQuery(this).removeClass('fc-day-number fc-sun').addClass('fc-day-number fc-sun');
	    			}
	    			if(jQuery(this).hasClass('fc-today')){
						jQuery(this).addClass('today');
					}else if(jQuery(this).hasClass('today')){
						jQuery(this).removeClass('today');
					}
	    		}
	    	});


	     });

   	});
</script>