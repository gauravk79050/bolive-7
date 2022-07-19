<!-- -------------------------------------- CROPPING IMAGE ------------------------------------------------------ -->
<script src="<?php echo base_url();?>assets/cp/new_js/jquery.Jcrop.js"></script>
<script type="text/javascript">
var jcrop_api,
boundx,
boundy,xsize,ysize,$preview,$pcnt,$pimg;

$(document).ready(function(){

	$(".thickboxed").click(function(){
		tb_show("<?php echo _("Upload Image");?>", "<?php echo base_url(); ?>cp/image_upload/ajax_img_upload?height=400&width=600", "true");
	});
	
  });

  function updateCoords(c)
  {
    $('#x').val(c.x);
    $('#y').val(c.y);
    $('#w').val(c.w);
    $('#h').val(c.h);
  };

  function checkCoords()
  {
    if (parseInt($('#w').val())) return true;
    alert("Please select a crop region then press submit.");
    return false;
  };

  function updatePreview(c)
  {
    if (parseInt(c.w) > 0)
    {
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
		//alert("cropping");
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
  
</script>

<script>
	$(document).ready(function(){
		$('#head select').change(function(){
			var id = $(this).attr('id');
			var value = $(this).val();
			$('.'+id).val(value);
		});
	});
</script>

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
#TB_window{
	top: 80% !important;
	z-index: 999 !important;
}
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
</style>
<!-- -------------------------------------------------------------------------------------------- -->

<script type="text/javascript">
function show_hide_oh(select,value,class_name){
	if(value == "CLOSED"){
		$("#opening-hours").find("tr:eq("+(select)+")").find("td").hide();
		$("#opening-hours").find("tr:eq("+(select)+")").find("td").find("select").val("CLOSED");
		//$("#opening-hours").find("tr:eq("+(select-1)+")").find("td:eq(0)").find("select").val("CLOSED");
		$("#opening-hours").find("tr:eq("+(select)+")").find("td:eq(0)").show();
	}else{
		$("#opening-hours").find("tr:eq("+(select)+")").find("td").show();
	}

	if(value == "0"){
		$("."+class_name).val("0");
		if( $("."+select+"_1").val() == "0" && $("."+select+"_2").val() == "0"){
			$("#opening-hours").find("tr:eq("+(select)+")").find("td").hide();
			$("#opening-hours").find("tr:eq("+(select)+")").find("td").find("select").val("CLOSED");
			$("#opening-hours").find("tr:eq("+(select)+")").find("td:eq(0)").show();
		}
	}
	
}

jQuery(document).ready(function(){
	jQuery('#allow_orders_bo').click(function(){
		//if(jQuery(this).is(':checked'))
		jQuery('#allow_order_settings').toggle();
	});
});

</script>

<style type="text/css">
#sidebar, #sidebarSet{
	display:none;
}

#bp-settings{
}

#bp-settings td{
	vertical-align:top;
}

#opening-hours{
	width:auto;
}

#opening-hours td{
	border:0px;
}

#pay-method{
	width:auto;
}

#pay-method td{
	border:0px;
	vertical-align:middle;
}

#pay-method td.grey-td{
	background:#EFEFEF;
}

#save_bp_settings{
	background:#18517E;
	color:#fff;
	font-weight:bold;
	border:0px;
	padding:10px 25px;
}

</style>

<div id="main" style="text-transform:none;">		
 <div id="main-header">
 <?php  @$url;
 $trail_date1= strtotime($trail_date);
 $on_trial = $this->company->on_trial;
 ?>
	<h2><?php echo _('BESTELONINE SETTINGS')?></h2>
      <p class="breadcrumb" style="float:left;margin-left:20px;"><a href="<?php echo base_url()?>cp/bestelonline/bp_settings"><b><?php echo _('Settings')?></b></a>&nbsp;&nbsp;<a href="<?php echo base_url()?>cp/bestelonline/photogallery"><?php echo _('Photogallery')?></a></p>
    </div>
    
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
    
    
	<?php $messages = $this->messages->get();?>
	<?php if($messages != array()):?>
		<?php foreach($messages as $key => $val):?>
			<?php foreach($val as $v):?>
				<div  class = "<?php echo $key;?>"><strong><?php echo ucfirst($key);?> : </strong><?php echo $v;?></div>
			<?php endforeach;?>	
		<?php endforeach;?>
    <?php endif;?>
	
	<div id="content" style="width: 100%;">
      <div id="content-container">
	     
		 <div class="box">
			<h3><?php echo _('Settings')?></h3>
			<div class="table">
			
			   <form method="post" action="<?php echo base_url()?>cp/bestelonline/bp_settings" enctype="multipart/form-data">
			   <?php $general_settings = $general_settings[0]; ?>
			   <?php $company = $company[0]; ?>	
			   <table id="bp-settings" cellspacing="0" cellpadding="0" border="0">
			      <tbody>
			      
					 <tr>
					     <td><b><?php echo _('Your Logo Image'); ?></b></td>
						 <td>
						    <!-- <input type="file" name="company_img" id="company_img" /> -->
							<input type="hidden" name="old_company_img" id="old_company_img" value="<?php echo $company->company_img; ?>" />
							<?php if( $company->company_img != '' ) { ?>
							<img src="<?php echo base_url(); ?>assets/cp/images/company_img/<?php echo $company->company_img; ?>" style="max-height:200px; max-width:200px;" />
							<?php } else if( $company->website != '' ) { ?>
							<?php /*?><img src="http://api.thumbalizr.com/?url=<?php echo $company->website; ?>" style="max-height:200px; max-width:200px;" /><?php */?>
                            <?php $img_url = 'http://images.shrinktheweb.com/xino.php?stwembed=1&stwaccesskeyid='.$this->config->item('shrinktheweb_access_key').'&stwsize=lg&stwurl='.$company->website; ?>
                            <img src="<?php echo $img_url; ?>" style="max-height:200px; max-width:200px;" />
							<?php } ?>
							
							<div id="uploaded_image" style="margin-top: 15px;"></div>
                   			<input type="hidden" id="x" name="x" />
				  			<input type="hidden" id="y" name="y" />
				  			<input type="hidden" id="w" name="w" />
				  			<input type="hidden" id="h" name="h" />
                   			<!-- <div><a href="javascript:;" class="thickboxed" ><input type="button" name="upload_image" id="upload_image" value="<?php echo _("Upload Image Here");?>" /></a></div> -->
                   			<div><a href="javascript:;" class="thickboxed" ><img src="<?php echo base_url();?>assets/images/plus.png" /></a></div>
                   			
                   				
						 </td>
					 </tr>
					 <tr ><td colspan='2' style='color:red'><?php  if(@$wrong_url_status=='1'){?><!-- <td>  <div class="url_display" style="display:none;"> --><?php //echo _('Only webilyst domain is allowed')?><!-- </div> --><?php echo _('*You Have entered wrong format of url pls enter correct format as (http://www.example.com)"');}?></td></tr>
					 <tr ><td colspan='2' style='color:red'><?php  if(@$url_status=='1'){?><!-- <td>  <div class="url_display" style="display:none;"> --><?php //echo _('Only webilyst domain is allowed')?><!-- </div> --><?php echo _('*you cannot enter this domain"');}?></td></tr>
					 
					 <?php if(($company->ac_type_id == 3 || $company->ac_type_id == 2) && $this->company->on_trial == 0){?>
				     <tr>
					     <td><b><?php echo _('Show my shop as:'); ?></b></td>
						 <td>
						 		<p>
							 		<input type="radio" name="show_hide_bp_shop" value="detail" <?php echo (($general_settings->show_hide_bp_shop == 'detail')?'checked="checked"':''); ?> onclick="$('#order_online_tr').show();" />
							 		<?php echo _("Detail page only");?>
							 	</p>
							 	<p>
								 	<input type="radio" name="show_hide_bp_shop" value="detail_shop" <?php echo (($general_settings->show_hide_bp_shop == 'detail_shop')?'checked="checked"':''); ?> onclick="$('#order_online_tr').hide();" />
								 	<?php echo _("Detail page + Shop");?>
								</p>	
						 </td>
					 </tr>
					 <tr id="order_online_tr" <?php if($general_settings->show_hide_bp_shop == 'detail_shop'){?>style="display:none;"<?php }?>>
					     <td><b><?php echo _('Order Online URL'); ?></b></td>
						 <td>
							 <input type="text" name="existing_order_page" id="existing_order_page" value="<?php echo $company->existing_order_page; ?>" style="width: 500px; height: 25px;" /><span style="color:red;"></span>
						 </td>
					 </tr>
					 <?php }elseif(($this->company->ac_type_id == 2 || $this->company->ac_type_id == 3) && $this->company->on_trial == 1){?>
					 <tr>
	                     <td><b><?php echo _('Bestelonline Shop')?></b></td>
						 <td>
						      <select name="shop_testdrive" id="shop_testdrive" style="margin:0px;padding:2px;" onchange="$('#allow_order_tr').toggle();$('#order_online_tr').toggle();">
							 
							  <option value="1" <?php if($general_settings->shop_testdrive == '1') { echo 'selected="selected"'; } ?>><?php echo _("Test Drive"); ?></option>
							  <option value="0" <?php if($general_settings->shop_testdrive == '0') { echo 'selected="selected"'; } ?>><?php echo _("Active"); ?></option>
							  
							  </select>
						 </td>
					 </tr>
					 <?php }?>
					 
					 <?php if($this->company->ac_type_id == 1 || ($this->company->ac_type_id == 2 && $this->company->on_trial == 1) || ($this->company->ac_type_id == 3 && $this->company->on_trial == 1) ){?>
					 <tr id="allow_order_tr" <?php if($general_settings->shop_testdrive == '0'){?>style="display:none;"<?php }?>>
					     <td><b><?php echo _('Allow Orders from BO'); ?></b></td>
						 <td>
							 <input type="checkbox" name="allow_orders_bo" id="allow_orders_bo" value="1" <?php echo (($general_settings->allow_orders_bo==1)?'checked="checked"':''); ?> />
							 <div id="allow_order_settings" <?php if($general_settings->allow_orders_bo == 0){?>style="display:none;"<?php }?>>
							 	<p>
							 		<input type="radio" name="portal_free_order_type" value="pickup" <?php echo (($general_settings->portal_free_order_type == 'pickup')?'checked="checked"':''); ?> />
							 		<?php echo _("Show Pickuptime");?>
							 	</p>
							 	<p>
								 	<input type="radio" name="portal_free_order_type" value="delivery" <?php echo (($general_settings->portal_free_order_type == 'delivery')?'checked="checked"':''); ?> />
								 	<?php echo _("Show Deliverytime");?>
								</p>
							 	
							 	<p>
							 		<?php echo _('Time between order and delivery/pickup is');?> 
								 	<select id="portal_free_ordertime" name="portal_free_ordertime">
								 		<option value="0" <?php if($general_settings->portal_free_ordertime == '0'){?>selected="selected"<?php }?>><?php echo _("Same day");?></option>
								 		<option value="1" <?php if($general_settings->portal_free_ordertime == '1'){?>selected="selected"<?php }?>>1 <?php echo _("day");?></option>
								 		<option value="2" <?php if($general_settings->portal_free_ordertime == '2'){?>selected="selected"<?php }?>>2 <?php echo _("days");?></option>
								 		<option value="3" <?php if($general_settings->portal_free_ordertime == '3'){?>selected="selected"<?php }?>>3 <?php echo _("days");?></option>
								 		<option value="4" <?php if($general_settings->portal_free_ordertime == '4'){?>selected="selected"<?php }?>>4 <?php echo _("days");?></option>
								 		<option value="5" <?php if($general_settings->portal_free_ordertime == '5'){?>selected="selected"<?php }?>>5 <?php echo _("days");?></option>
								 		<option value="6" <?php if($general_settings->portal_free_ordertime == '6'){?>selected="selected"<?php }?>>6 <?php echo _("days");?></option>
								 		<option value="7" <?php if($general_settings->portal_free_ordertime == '7'){?>selected="selected"<?php }?>>7 <?php echo _("days");?></option>
								 	</select>
								</p>
							 </div>
						 </td>
					 </tr>
					 <tr id="order_online_tr" <?php if($general_settings->shop_testdrive == '0'){?>style="display:none;"<?php }?>>
					     <td><b><?php echo _('Order Online URL'); ?></b></td>
						 <td>
							 <input type="text" name="existing_order_page" id="existing_order_page" value="<?php echo $company->existing_order_page; ?>" style="width: 500px; height: 25px;" /><span style="color:red;"></span>
						 </td>
					 </tr>
					 <?php } ?>
					 
                     <tr>
					     <td><b><?php echo _('Company Facebook URL'); ?></b></td>
						 <td>
							 <input type="text" name="company_fb_url" id="company_fb_url" value="<?php echo $company->company_fb_url; ?>" style="width: 500px; height: 25px;" />
						 </td>
					 </tr>
					 <tr>
					     <td><b><?php echo _('Company short description'); ?></b></td>
						 <td>
						     (<?php echo _('maximum 500 characters'); ?>)
							 <br /><br />
							 <textarea name="company_desc" id="company_desc" rows="5" cols="100"><?php echo $company->company_desc; ?></textarea>
						 </td>
					 </tr>
					 <tr>
					     <td><b><?php echo _('Opening Hours'); ?></b></td>
						 <td>
						     <table id="opening-hours">
						     
							    <?php if(!empty($days)) { foreach($days as $d) { ?>
								<tr>
								   <th><?php echo _($d->name); ?></th>
								   <td>
								      <select name="time_1[<?php echo $d->id; ?>]" onchange="show_hide_oh('<?php echo $d->id;?>',this.value,'<?php echo $d->id; ?>_1')" class="<?php echo $d->id; ?>_1 first_col">
								      	<option value="CLOSED" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_1'] == "CLOSED" ) echo 'selected="selected"'; } ?> ><?php echo _("CLOSED"); ?></option>
								      	<option value="0" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_1'] == "0" ) echo 'selected="selected"'; } ?> ><?php echo _("NONE"); ?></option>
									     <?php for($i=0;$i<=23;$i++) { ?>
										 <?php for($j=0;$j<=30;$j=$j+30) { ?>
										 <?php if(strlen($i)==1){$i='0'.$i;} if(strlen($j)==1){$j='0'.$j;} ?>
										 <option value="<?php echo $i.':'.$j; ?>" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_1'] == ($i.':'.$j) ) echo 'selected="selected"'; } ?> ><?php echo $i.':'.$j; ?></option>
										 <?php } ?>
										 <?php } ?>
									  </select>
								   </td>
								   <td <?php if(!empty($opening_hours) && $opening_hours[$d->id]['time_1'] == "CLOSED"){?>style="display:none;"<?php }?>><?php echo _('to'); ?></td>
								   <td <?php if(!empty($opening_hours) && $opening_hours[$d->id]['time_1'] == "CLOSED"){?>style="display:none;"<?php }?>>
								      <select name="time_2[<?php echo $d->id; ?>]" onchange="show_hide_oh('<?php echo $d->id;?>',this.value,'<?php echo $d->id; ?>_1')" class="<?php echo $d->id; ?>_1 sec_col">
								      	<option value="CLOSED" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_2'] == "CLOSED" ) echo 'selected="selected"'; } ?> ><?php echo _("CLOSED"); ?></option>
								      	<option value="0" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_2'] == "0" ) echo 'selected="selected"'; } ?> ><?php echo _("NONE"); ?></option>
									     <?php for($i=0;$i<=23;$i++) { ?>
										 <?php for($j=0;$j<=30;$j=$j+30) { ?>
										 <?php if(strlen($i)==1){$i='0'.$i;} if(strlen($j)==1){$j='0'.$j;} ?>
										 <option value="<?php echo $i.':'.$j; ?>" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_2'] == ($i.':'.$j) ) echo 'selected="selected"'; } ?> ><?php echo $i.':'.$j; ?></option>
										 <?php } ?>
										 <?php } ?>
									  </select>
								   </td>
								   <td <?php if(!empty($opening_hours) && $opening_hours[$d->id]['time_1'] == "CLOSED"){?>style="display:none;"<?php }?>><?php echo _('and'); ?></td>
								   <td <?php if(!empty($opening_hours) && $opening_hours[$d->id]['time_1'] == "CLOSED"){?>style="display:none;"<?php }?>>
								      <select name="time_3[<?php echo $d->id; ?>]" onchange="show_hide_oh('<?php echo $d->id;?>',this.value,'<?php echo $d->id; ?>_2')" class="<?php echo $d->id; ?>_2 third_col">
									    <option value="CLOSED" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_3'] == "CLOSED" ) echo 'selected="selected"'; } ?> ><?php echo _("CLOSED"); ?></option>
								     	<option value="0" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_3'] == "0" ) echo 'selected="selected"'; } ?> ><?php echo _("NONE"); ?></option>
									     <?php for($i=0;$i<=23;$i++) { ?>
										 <?php for($j=0;$j<=30;$j=$j+30) { ?>
										 <?php if(strlen($i)==1){$i='0'.$i;} if(strlen($j)==1){$j='0'.$j;} ?>
										 <option value="<?php echo $i.':'.$j; ?>" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_3'] == ($i.':'.$j) ) echo 'selected="selected"'; } ?> ><?php echo $i.':'.$j; ?></option>
										 <?php } ?>
										 <?php } ?>
									  </select>
								   </td>
								   <td <?php if(!empty($opening_hours) && $opening_hours[$d->id]['time_1'] == "CLOSED"){?>style="display:none;"<?php }?>><?php echo _('to'); ?></td>
								   <td <?php if(!empty($opening_hours) && $opening_hours[$d->id]['time_1'] == "CLOSED"){?>style="display:none;"<?php }?>>
								      <select name="time_4[<?php echo $d->id; ?>]" onchange="show_hide_oh('<?php echo $d->id;?>',this.value,'<?php echo $d->id; ?>_2')" class="<?php echo $d->id; ?>_2 last_col">
								      	<option value="CLOSED" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_4'] == "CLOSED" ) echo 'selected="selected"'; } ?> ><?php echo _("CLOSED"); ?></option>
								      	<option value="0" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_4'] == "0" ) echo 'selected="selected"'; } ?> ><?php echo _("NONE"); ?></option>
									     <?php for($i=0;$i<=23;$i++) { ?>
										 <?php for($j=0;$j<=30;$j=$j+30) { ?>
										 <?php if(strlen($i)==1){$i='0'.$i;} if(strlen($j)==1){$j='0'.$j;} ?>
										 <option value="<?php echo $i.':'.$j; ?>" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_4'] == ($i.':'.$j) ) echo 'selected="selected"'; } ?> ><?php echo $i.':'.$j; ?></option>
										 <?php } ?>
										 <?php } ?>
									  </select>
								   </td>
								</tr>
								<?php } } ?>
							 </table>
						 </td>
					 </tr>
					 <?php /*?><tr>
					     <td><b><?php echo _('PAYMENT METHODS'); ?></b></td>
						 <td><?php if($general_settings->pay_methods!='') { $pay_methods = json_decode($general_settings->pay_methods); } else { $pay_methods = array(); } ?>
						     <table id="pay-method" cellpadding="0" cellspacing="0">
							    <!-- <tr>
								   <td></td>
								   <td class="grey-td"><b><?php echo _('PAYMENT METHODS'); ?></b></td>
								</tr> -->
								<tr>
								   <td><input type="checkbox" name="pay_methods[]" value="1" <?php if(in_array('1',$pay_methods)) { echo 'checked="checked"'; } ?> /></td>
								   <td><?php echo _('Cash on pickup'); ?></td>
								</tr>
								<tr>
								   <td><input type="checkbox" name="pay_methods[]" value="2" <?php if(in_array('2',$pay_methods)) { echo 'checked="checked"'; } ?> /></td>
								   <td>
								     <span style="float:left;margin-top: 5px;"><?php echo _('Paypal'); ?></span>
									 <img src="<?php echo base_url(); ?>assets/images/master-visa.PNG" style="margin-left: 5px;" />
								     <div style="clear:both;"></div>
								   </td>
								</tr>
								<tr>
								   <td><input type="checkbox" name="pay_methods[]" value="3" <?php if(in_array('3',$pay_methods)) { echo 'checked="checked"'; } ?> /></td>
								   <td><?php echo _('Bank Transfer'); ?>&nbsp;<input type="text" name="company_account_num" id="company_account_num" value="<?php echo $general_settings->company_account_num; ?>" size="10" /></td>
								</tr>
							 </table>
						 </td>
					 </tr><?php */ ?>
					 <tr>
					     <td><b><?php echo _('Conditions'); ?></b></td>
						 <td>
						     <input type="checkbox" name="set_terms_and_conditions" id="set_terms_and_conditions" value="1" <?php if($general_settings->set_terms_and_conditions) { echo 'checked="checked"'; } ?> />
							 <br /><br />
							 <textarea name="terms_and_conditions" id="terms_and_conditions" rows="5" cols="100"><?php echo $general_settings->terms_and_conditions; ?></textarea>
							 <br /><br />
							 <input type="submit" name="save_bp_settings" id="save_bp_settings" value="<?php echo _('Save Changes'); ?>" />
						 </td>
					 </tr>
				  </tbody>
			   </table>
			   
			   </form>
			
			</div>
		 </div>
		 
	  </div>