<?php $front_msg_text_color = "#ffffff";?>
<style>
#other_tab > tbody > tr > .textlabel {
    width: 40%;
}

#discount_per_amount_div p input {
    margin: 0 5px 5px 0;
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
</style>
<!--<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.core.js"></script>
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.widget.js"></script>
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.sortable.js"></script>-->
<script src="<?php echo base_url()?>assets/cp/js/jquery.tooltip.js?version=<?php echo version;?>"></script>
<link rel="stylesheet" href="<?php echo base_url()?>assets/cp/css/qtip.css?version=<?php echo version;?>"/>
<script type="text/javascript" src="<?php echo base_url()?>assets/cp/new_js/colorpicker/jquery.miniColors.js?version=<?php echo version;?>"></script>
<link type="text/css" rel="stylesheet" href="<?php echo base_url()?>assets/cp/new_css/colorpicker/jquery.miniColors.css?version=<?php echo version;?>" />

<link rel="stylesheet" href="<?php echo base_url()?>assets/cp/css/qtip.css"/>
<script type="text/javascript" src="<?php echo base_url()?>assets/cp/new_js/colorpicker/jquery.miniColors.js?version=<?php echo version;?>"></script>
<link type="text/css" rel="stylesheet" href="<?php echo base_url()?>assets/cp/new_css/colorpicker/jquery.miniColors.css" />

<script type="text/javascript">
jQuery(document).ready(function($) {
  if($( "#main_drop option:selected" ).text() == "<?php echo _('Both');?>"){
    $("#default_drop").css("display", "");
  }
  $(".colors").minicolors();
});
function hide_option(){
  $("#default_drop").css("display", "none");
  if($( "#main_drop option:selected" ).text() == "<?php echo _('Both');?>")
  {
    $("#default_drop").css("display", "");
  }
}

var confirm_txt = "<?php echo _('Are you sure, you want to delete');?>";
var cropping = "<?php echo _('Cropping');?>";
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
</script>
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
      var attr_details=$(this).attr('attr_id');
      tb_show("<?php echo _("Upload Image");?>", "<?php echo base_url(); ?>cp/image_upload/ajax_img_upload/?height=400&width=600&attr_det="+attr_details, "true");
    }); 

    $(".thickboxed_lab_logo").click(function(){
		var num = $(this).attr('attr_id');
		tb_show("<?php echo _("Upload Image");?>", base_url+"cp/image_upload/ajax_img_upload/cp/"+num+"?height=400&width=600", "true");
	});
  });

    function updateCoords(c){
      $('#x').val(c.x);
      $('#y').val(c.y);
      $('#w').val(c.w);
      $('#h').val(c.h);
    }

    function checkCoords(){
      if (parseInt($('#w').val())) return true;
      alert("<?php echo _('Please select a crop region then press submit.');?>");
      return false;
    }

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
    }

	function gal_crop(obj){
		var cur_id = $(obj).parent().parent().attr('id');
		var ord = cur_id.replace('uploaded_image','');
		$("#"+cur_id).append('<img src="'+base_url+'assets/cp/images/loader.gif" alt="'+cropping+'"/>');
		$.ajax({
			url : base_url+'cp/image_upload/crop_image/'+ord,
			data : {'image_name': $("#image_name"+ord).val(), 'x': $("#x").val(), 'y': $("#y").val(), 'w': $("#w").val(), 'h': $("#h").val()},
			type: 'POST',
			success: function(response){
				$("#"+cur_id).html(response);
				$("#"+cur_id).focus();
			}
		});
	};
	
    function crop(i){
    if(i == 1){
      $("#uploaded_img").append('<img src="<?php echo base_url();?>assets/cp/images/loader.gif" alt="<?php echo _("Cropping");?>...."/>');
    }
    else{
      $("#uploaded_image").append('<img src="<?php echo base_url();?>assets/cp/images/loader.gif" alt="<?php echo _("Cropping");?>...."/>');
    }
    
    $.ajax({
      url : base_url+'cp/image_upload/crop_image',
      data : {'image_name': $("#image_name").val(), 'x': $("#x").val(), 'y': $("#y").val(), 'w': $("#w").val(), 'h': $("#h").val()},
      type: 'POST',
      success: function(response){
        //$("#uploaded_image").toggle("slow");
        if(i == 1){
          $("#uploaded_img").html(response);
          $("#uploaded_img").focus();
        }
        else{
          $("#uploaded_image").html(response);
          $("#uploaded_image").focus();
        }
        
        //$("#uploaded_image").toggle("slow");
      }
    });
  }
</script>
<script type="text/javascript">
  function samedayDelivery(){
      var sameDayDeliveryOrder = document.getElementById("same_day_orders_delivery");
  
    if(sameDayDeliveryOrder){
      if(sameDayDeliveryOrder.checked == true){
        document.getElementById("samedayDelivery").style.display = "block";
        document.getElementById("samedayDeliveryTime").style.display = "block";
      }else if(sameDayDeliveryOrder.checked == false){
        document.getElementById("samedayDelivery").style.display = "none";
        document.getElementById("samedayDeliveryTime").style.display = "none";
      }
    }
  }
  
  function samedayPickup(){
      var sameDayPickupOrder = document.getElementById("same_day_orders_pickup");
  
    if(sameDayPickupOrder){
        if(sameDayPickupOrder.checked == true){
        document.getElementById("samedayPickup").style.display = "block";
        document.getElementById("samedayPickupTime").style.display = "block";
        }else if(sameDayPickupOrder.checked == false){
        document.getElementById("samedayPickup").style.display = "none";
        document.getElementById("samedayPickupTime").style.display = "none";
        }
    }
    } 

  //============== START : VALIDATING THE DELIVERY AND PICK UP SETTINGS TABLES ===================//

    function validate_mess(result){

    if(result == true){
  
      var key = document.getElementById('part').value;
  
      if(key == 'p'){
  
        var res = validateDeliveryPickupDiff('pickup');
  
        if(res == false) return res;
  
      }else{
  
        var res = validateDeliveryPickupDiff('delivery');
  
        if(res == false) return res;
  
      }
  
      var all_day_start = document.getElementById('all_day_starttime_'+key).value;
  
      var all_day_end = document.getElementById('all_day_endtime_'+key).value;
  
      if((parseInt(all_day_end) <= parseInt(all_day_start))){
  
        alert("<?php echo _('ERROR - Please hours just to check - these are not correctly entered.'); ?>");
  
        return false;
      }
  
      for(var k=1;k<=7;k++){
  
        var field1 = document.getElementById(key+'1['+k+']').value;
  
        var Day = getDayName(k);    
  
        if(field1 == "0"){
  
          alert("<?php echo _('Set a time for'); ?> "+Day);
  
          return false;
        }   
        
        if(field1 != "ALL DAY" && field1 != "CLOSED"){        
  
          var field2 = document.getElementById(key+'2['+k+']').value;
  
          var field3 = document.getElementById(key+'3['+k+']').value;
  
          var field4 = document.getElementById(key+'4['+k+']').value;
  
          if(field2 == "0"){
  
            alert("<?php echo _('Set a time for'); ?> "+Day+" <?php echo _('tomorrow'); ?>");
  
            return false;
  
          }else if(field3 == "0"){
  
            alert("<?php echo _('Set a time for'); ?> "+Day+" <?php echo _('evening'); ?>");
  
            return false;
  
          }else if(field4 == "0"){
  
            alert("<?php echo _('Set a time for'); ?> "+Day+" <?php echo _('evening'); ?>");
  
            return false;
  
          }else{    
  
            if((field1 == "NONE" && field2 != "NONE") || (field1 != "NONE" && field2 == "NONE")){
  
              alert("<?php echo _('ERROR - Replace Select NONE or a time'); ?> : "+Day+" <?php echo _('tomorrow'); ?>");
  
              return false;
  
            }else if((field3 == "NONE" && field4 != "NONE") || (field3 != "NONE" && field4 == "NONE")){
  
              alert("<?php echo _('ERROR - Replace Select NONE or a time'); ?> : "+Day+" <?php echo _('evening'); ?>");
  
              return false;
  
            }else if((parseInt(field2) <= parseInt(field1)) && !isNaN(field1) && !isNaN(field2)){
  
              alert("<?php echo _('Please hours just to check - these are not correctly entered'); ?> : "+Day);
  
              return false;
  
            }else if((parseInt(field3) <= parseInt(field2)) && !isNaN(field2) && !isNaN(field3)){
  
              alert("<?php echo _('Please hours just to check - these are not correctly entered'); ?> : "+Day);
  
              return false;
  
            }else if((parseInt(field4) <= parseInt(field3)) && !isNaN(field3) && !isNaN(field4)){
  
              alert("<?php echo _('Please hours just to check - these are not correctly entered'); ?> : "+Day); 
  
              return false;
            }
          }
        }
      }
      return true;
    }
    return false;
    }

    //============== START : VALIDATING THE HOLIDAY SETTINGS TABLES ===================//

  function validate_holiday(result){

    if(result == true){
      if(document.frm_holiday_settings.holiday[1].checked){

        var key = document.getElementById('part').value;

        var field1 = document.getElementById(key+'1').value;

        if(field1 == "0"){

          alert("<?php echo _('Determine the opening time'); ?>");

          return false;
        }

        if(field1 != "ALL DAY"){

          var field2 = document.getElementById(key+'2').value;

          var field3 = document.getElementById(key+'3').value;

          var field4 = document.getElementById(key+'4').value;  

          if(field2 == "0"){

            alert("<?php echo _('Determine the closing in the morning'); ?>");

            return false;

          }else if(field3 == "0"){

            alert("<?php echo _('Determine the opening time in the afternoon'); ?>");

            return false;

          }else if(field4 == "0"){

            alert("<?php echo _('Determine the closing in the afternoon'); ?>");

            return false;

          }else{    

            if((field1 == "NONE" && field2 != "NONE") || (field1 != "NONE" && field2 == "NONE")){

              alert("<?php echo _('ERROR - Setting is not correct'); ?>");

              return false;

            }else if((field3 == "NONE" && field4 != "NONE") || (field3 != "NONE" && field4 == "NONE")){

              alert("<?php echo _('ERROR - Setting is not correct'); ?>");

              return false;

            }else if((parseInt(field2) <= parseInt(field1)) && !isNaN(field1) && !isNaN(field2)){

              alert("<?php echo _('Setting is not correct'); ?>");

              return false;

            }else if((parseInt(field3) <= parseInt(field2)) && !isNaN(field2) && !isNaN(field3)){

              alert("<?php echo _('Setting is not correct'); ?>");

              return false;

            }else if((parseInt(field4) <= parseInt(field3)) && !isNaN(field3) && !isNaN(field4)){

              alert("<?php echo _('Setting is not correct'); ?>");  

              return false;
            }
          }
        }
      }
      return true;
    }
    return false;
  }

    jQuery(document).ready(function($) {
    $('.help').tipsy({gravity: 'w'});
    
    // Updating delivery areas
    $("#up_delivery_area").click(function(){
      var country_id = $("#country_names").val();
      
      if(country_id == 21)
        var state_id = $("#state_belgium").val();
      else
        var state_id = $("#state_netherlands").val();
      
      var postcode_ids = $("#postcodes").val();
      if(postcode_ids != null){
        
      }else{
        postcode_ids = '';
      }
      
      $.ajax({
        url: base_url+'cp/orders/set_delivery_areas',
        type: 'POST',
        data: { countryId:country_id, stateId:state_id, postcodeIds:postcode_ids},
        success: function(response){
          alert(response);
        }
      });
    });
    
    var fixHelper = function(e, ui) {
      ui.children().each(function() {
        $(this).width($(this).width());
      });
      return ui;
    };
    
    $(".grp_sort").sortable({
      helper: fixHelper,
      cursor: "move"
    });
    
    show_hide_areas();
    
//    $('#up_comp_country').click(function(){
//      var new_html = '';
//      var selected = false;
//      var to_append = false;
//      var msg = '';
//      msg += '<p>';
//      msg += '<?php //echo _('Please insert delivery costs for each countries you have selected');?>';
//      msg += '</p>';

//      var already_selected_ids = new Array();
//      $(".country_selected").each(function () {
//        already_selected_ids.push($(this).val());
//      });
//      var c_to_add = new Array();
//      $("#inter_c option:selected").each(function () {
//        c_to_add.push($(this).val());
//        selected = true;
//        if(already_selected_ids.length == 0 || already_selected_ids.indexOf($(this).val()) == -1){
//          //delete already_selected_ids[already_selected_ids.indexOf($(this).val())];
//          new_html += '<p id=\"s_c_'+$(this).val()+'\">';
//          new_html += ' <label>';
//          new_html += '   <span style=\"width:215px; display:inline-block\">';
//          new_html +=       $(this).text();
//          new_html += '   </span>';
//          new_html += '   <input type=\"text\" class=\"short text\" name=\"country_cost[]\" value=\"\" /> &euro;';
//          new_html += '   <input type=\"hidden\" name=\"country_selected[]\" class=\"country_selected\" value=\"'+$(this).val()+'\" />';
//          new_html += ' </label>';
//          new_html += '</p>';
//        }else{
//          to_append = true;
//        }
//      });

//      // Deleting countries
//      if(already_selected_ids.length){
//        // Now it can be used reliably with $.map()
//        $.map( already_selected_ids, function( val, i ) {
//          if(c_to_add.indexOf(val) == -1)
//            $('#s_c_'+val).remove();
//        });
//        //$('#s_c_'+$(this).val()).remove();
//      }

//      if(!selected){
//        new_html = '<p>';
//        new_html += '&nbsp;';
//        new_html += '</p>';
//        $('#selected_countries').html(new_html);
//      }else if(to_append){
//        $('#selected_countries').append(new_html);
//      }else{
//        $('#selected_countries').html(msg + new_html);
//      }
      
//    });
    $('#up_comp_country').click(function(){
      var new_html = '';
      var selected = false;
      var to_append = false;
      var msg = '';
      msg += '<p>';
      msg += "<?php echo _('Please insert delivery costs for each countries you have selected');?>";
      msg += '</p>';

      var already_selected_ids = new Array();
      $(".country_selected").each(function () {
        already_selected_ids.push($(this).val());
      });
      var c_to_add = new Array();
      $("#inter_c option:selected").each(function () {
        c_to_add.push($(this).val());
        selected = true;
        if(already_selected_ids.length == 0 || already_selected_ids.indexOf($(this).val()) == -1){
          //delete already_selected_ids[already_selected_ids.indexOf($(this).val())];
          new_html += '<p id=\"s_c_'+$(this).val()+'\">';
          new_html += ' <label>';
          new_html += '   <span style=\"width:215px; display:inline-block\">';
          new_html +=       $(this).text();
          new_html += '   </span>';         
          new_html += '   <a id=\"country_rows_'+$(this).val()+'\" href=\"javascript:void(0);\" onclick=\"int_del_add_new('+$(this).val()+')\"><?php echo _('Add New Delivery Rate');?></a>';
          new_html += ' </label>';
          new_html += '   <input type=\"hidden\" name=\"country_selected[]\" class=\"country_selected\" value=\"'+$(this).val()+'\" />';
          new_html += '</p>';
        }else{
          to_append = true;
        }
      });
      
      // Deleting countries
      if(already_selected_ids.length){
        // Now it can be used reliably with $.map()
        $.map( already_selected_ids, function( val, i ) {
          if(c_to_add.indexOf(val) == -1)
            $('#s_c_'+val).remove();
        });
        //$('#s_c_'+$(this).val()).remove();
      }

      if(!selected){
        new_html = '<p>';
        new_html += '&nbsp;';
        new_html += '</p>';
        $('#selected_countries_int').html(new_html);
      }else if(to_append){
        $('#selected_countries_int').append(new_html);
      }else{
        $('#selected_countries_int').html(msg + new_html);
      }
    });   
    });
    
    function int_del_add_new(cou_id){
    tb_show('<?php echo _('Add Delivery Rate')?>','#TB_inline?height=155&width=300&inlineId=int_del_add_upd');
    $('#TB_ajaxContent').find('#rate_country_id').val(cou_id);
  }
  
  /*=========this function will hide intro if check box is clicked=========*/
  function intro_show_hide(checkobj){
    if(checkobj.checked){
        $(".intro").css({'display':'none'});
      }else{
        $(".intro").css({'display':'block'});
      }
    }
  /*======================================================================*/
  
  function change_state(){
  
    $('.c_states').toggle();
    $('#postcodes').html('<option value=\"0\"><?php echo _("No City");?></option>');
  }
  
  function get_postcode(state_id){
    if(state_id != 0){
      $("#load_postcodes").toggle();
      $.post(
          base_url+"cp/orders/getPostcodes",
          {'stateId':state_id, 'countryId':$("#country_names").val()},
          function(response){
            /*var new_html = '';
            if(response.length > 0){
              for(var i = 0 ; i < response.length ; i++ ){
                new_html += '<option value='+response[i].id+'>'+response[i].post_code+'&nbsp;&nbsp;&nbsp;&nbsp;'+response[i].area_name.replace("\\","")+'</option>';
              }
            }
            $("#postcodes").html(new_html);*/
            $("#postcodes").html(response);
            $("#load_postcodes").toggle();
          }
        );
    }else{
      alert("<?php echo _("please select any state");?>");
    }
  }
  
  // Function to show fide National and International sections
  function show_hide_areas(){
    if($("input[name='type']:checked").val() == 'national'){
      $('.national_row').show();
      $('.international_row').hide();
    }else{
      $('.national_row').hide();
      $('.international_row').show();
    }
  }
</script>
<style>
  .theme_img{
    height:250px;
    width:300px;
  }
  
  .grp_sort .ui-state-disabled{
    opacity: 1;
  }
  
  #frm_groups tr{
     width:auto !important;
  }
  .save_b{
    padding: 20px 60px 20px 20px !important;
      text-align: right;
  }
</style>
<!--this function is used to show the drop down inintially if same day order pickup is initially selected-->
<?php if($order_settings&&$order_settings[0]->same_day_orders_pickup):?>
<script type="text/javascript">
  jQuery('document').ready(function(){
    samedayPickup(); 
  });
</script>
<?php endif;?>
<!--this function is used to show the drop down inintially if same day order delivery is initially selected-->
<?php if($order_settings&&$order_settings[0]->same_day_orders_delivery):?>
<script type="text/javascript">
  jQuery('document').ready(function(){
    samedayDelivery();
  });
</script>
<?php endif;?>
<script type="text/javascript">
  function check_area_name(form){
    //alert(form.name+ '  '+'prerna');
    var val = form.area_name.value;
    //alert(val);
    //return false;
    if(val == null  || val == ''){
      alert("plese enter value");
      return false; 
    } 
    return true;
  }
</script>
<script type="text/javascript">
function validateDeliveryPickupDiff(id){
  if(id == 'delivery'){
        var sameDayDeliveryOrder = document.getElementById("same_day_orders_delivery");
      if(sameDayDeliveryOrder.checked == true){
        var diffDelivery = document.getElementById("time_diff_delivery");
        if(diffDelivery.value == 0){
        alert("<?php echo _('Please Select Minimum time between Order and Delivery');?>");
        jQuery("#time_diff_delivery").focus();
        return false;
        }

        if(jQuery("#allowed_days_delivery").val() == null){
        alert("<?php echo _(' Please select atleast one day');?>");
        jQuery("#allowed_days_delivery").focus();
        return false;
        }

        if(jQuery("#same_day_time_delivery").val() == 0){
          alert("<?php echo _(' Please select time till user can place order for the same day');?>");
          jQuery("#same_day_time_delivery").focus();
        return false;
      }
      }
    }else{
        var sameDayPickupOrder = document.getElementById("same_day_orders_pickup");
      if(sameDayPickupOrder.checked == true){
        var diffPickup = document.getElementById("time_diff_pickup");
        if(diffPickup.value == 0){
        alert("<?php echo _(' Please Select Minimum time between Order and Pickup');?>");
        jQuery("#time_diff_pickup").focus();
        return false;
        }

        if(jQuery("#allowed_days_pickup").val() == null){
        alert("<?php echo _(' Please select atleast one day');?>");
        jQuery("#allowed_days_pickup").focus();
        return false;
        }

        if(jQuery("#same_day_time_pickup").val() == 0){
          alert("<?php echo _(' Please select time till user can place order for the same day');?>");
          jQuery("#same_day_time_pickup").focus();
        return false;
      }
      }
    }
}
</script>
<!--------------------------------------------------------------------------------------------------------->
<!--this reagon is get dispalyed in a thick box when a add link is pressed(in delivery settings)-->
<div name="add_delivery_area" id="add_delivery_area" style="display:none">
  <form method="post" action="<?php echo base_url()?>cp/settings" name="frm_area_add" id="frm_area_add" onsubmit="return check_area_name(this)">
    <table border="0" cellpadding="10px">
        <tbody><tr>
          <td style="width:100px" class="textlabel"><?php echo _('State Name')?></td>
          <td> <input type="text" name="area_name" id="area_name" value=""/></td>
        </tr>
        <tr>
          <td style="padding-left:20px" colspan="2"><input type="submit" id="add_area" name="add_area" value="<?php echo _('ADD');?>"/></td>
        </tr></tbody>
    </table>
  </form>
    
</div>
<!--------------------------------------------------------------------------->

<!--this reagon is get dispalyed in a thick box when a edit link is pressed-->
<?php if($delivery_areas):?>
<?php foreach($delivery_areas as $delivery_area):?>
<div name="update_delivery_area_<?php echo $delivery_area->id?>" id="update_delivery_area_<?php echo $delivery_area->id?>" style="display:none">
  <form method="post" action="<?php echo base_url()?>cp/settings" name="frm_area_edit" id="frm_area_edit" onsubmit="return check_area_name(this)">
      <table border="0">
        <tbody><tr>
          <td style="width:100px" class="textlabel"><?php echo _('State Name');?></td>
          <td><input tpe="text" name="area_name" id="area_name" value="<?php echo $delivery_area->area_name?>"/></td>
      </tr>
        <tr>
          <td class="save_b" colspan="2"><input type="submit" id="update_area" name="update_area" value="<?php echo _('UPDATE');?>"/><input type="hidden" value="add_edit" id="act" name="act"><input type="hidden" name="id" id="id" value="<?php echo $delivery_area->id?>"/>
        </td>
        </tr></tbody>
    </table>
  </form>
</div>
<?php endforeach;?>
<?php endif;?>
<!--------------------------------------------------------------------------->
<!-- MAIN -->
<div id="main">
<div id="main-header">
  <h2> <?php echo _('Settings')?></h2>
  <span class="breadcrumb"><a href="<?php echo base_url()?>cp"><?php echo _('Home')?> </a>&raquo;<?php echo _('Site Settings')?> </span> </div>
  <?php $messages = $this->messages->get();?>
  <?php if($messages != array()):?>
    <?php foreach($messages as $key => $val):?>
      <?php foreach($val as $v):?>
        <div class = "<?php echo $key;?>"><strong><?php if($key == 'success'){}elseif($key == 'error'){ echo _("error").' : '; };?></strong><?php echo $v;?></div>
      <?php endforeach;?> 
    <?php endforeach;?>
    <?php endif;?>
<div id="content">
  <div id="content-container">
    <div <?php if($show==1){ echo 'class="box"'; } else { echo 'class="boxed"'; } ?>>
      <h3  id="r_genSettings"> <?php echo _('General Settings')?> </h3>
      
      <?php include 'generalsettings.php';?> 

    </div>
  
  <?php if( $this->company_role == 'master' || $this->company_role == 'super' ) { ?>
  
    <div <?php if($show==2){ echo 'class="box"'; } else { echo 'class="boxed"'; } ?>>
      <h3  id="r_groups"> <?php echo _('Configure groups')?></h3>
      
       <?php include 'configuregroups.php';?>

    </div>
  
  <?php } ?>
  
  <?php if( $this->company_role == 'master' || $this->company_role == 'sub' ) { ?>
  
    <!--this div will get dispalyed only if when the pickup services is on in general settings works on show_hide_pickup-->
    <div <?php if($show==3){ echo 'class="box"'; } else { echo 'class="boxed"'; } ?> <?php if($general_settings&&$general_settings[0]->pickup_service):?>style="display:block;"<?php else: ?>style="display:none;"<?php endif; ?> id="pickup">
      <h3 id="r_pickup"> <?php echo _('Pickup settings')?></h3>
      
      <?php include 'pickupsettings.php';?>


    </div>
  <?php } ?>
  
  <?php if( $this->company_role == 'master' || $this->company_role == 'sub' ) { ?>
    <!--this div will get dispalyed only if when the delivery serviced is on in general settings works on show_hide_delivery-->
    <div <?php if($show==4){ echo 'class="box"'; } else { echo 'class="boxed"'; } ?> <?php if($general_settings&&$general_settings[0]->delivery_service):?>style="display:block;"<?php else: ?>style="display:none;"<?php endif; ?> id="delivery">
      <h3  id="r_delivery"><?php echo _('Delivery Settings')?></h3>
      

      <?php include 'deliverysettings.php';?>



    </div>
  
  <?php } ?>
  
  <?php if( $this->company_role == 'master' || $this->company_role == 'sub' ) { ?>
  
    <div <?php if($show==5){ echo 'class="box"'; } else { echo 'class="boxed"'; } ?>>
      <h3  id="r_mail"> <?php echo _('Mail-Messages')?></h3>
      

      <?php include 'mailmessages.php';?>


    </div>
  
  <?php } ?>
  
  <?php if( $this->company_role == 'master' || $this->company_role == 'sub' ) { ?>
  
    <div <?php if($show==10){ echo 'class="box"'; } else { echo 'class="boxed"'; } ?>>
      <h3  id="r_faq"> <?php echo _('FAQ - clients')?></h3>
     
      <?php include 'faqclients.php';?>

    </div>
  
  <?php } ?>

  <!-- >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> START: TERMS and CONDITIONS <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< -->
  <?php if( $this->company_role == 'master' || $this->company_role == 'sub' ) { ?>
  
    <div <?php if($show==11){ echo 'class="box"'; } else { echo 'class="boxed"'; } ?>>
      <h3 id="t_n_c"> <?php echo _('Terms and Conditions')?></h3>
      
      <?php include 'termsandConditions.php';?>  


    </div>
  
  <?php } ?>
  <!-- >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> END: TERMS and CONDITIONS <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< -->
  
  <?php if( $this->company_role == 'master' || $this->company_role == 'sub' ) { ?>
  
    <div <?php if($show==6){ echo 'class="box"'; } else { echo 'class="boxed"'; } ?>>
      <h3  id="r_holiday"> <?php echo _('Holiday Settings')?> </h3>
      
    <?php include 'holidaysettings.php';?>  



    </div>
  
  
  
  <?php } ?>  
  
  <?php if( $this->company_role != 'sub' && ( $this->company_role == 'master' || $this->company_role == 'super' ) ) { ?>
  
  <div <?php if($show==8){ echo 'class="box"'; } else { echo 'class="boxed"'; } ?>>
      <h3  id="r_payment"> <?php echo _('Payment Settings')?> </h3>
      
              
    <?php include 'paymentSettings.php';?>  

           
    </div>
    
  <?php } ?>  
  
  <?php if( $this->company_role == 'master' || $this->company_role == 'super' ) { ?>  
  <div <?php if($show==12){ echo 'class="box"'; } else { echo 'class="boxed"'; } ?>>
      <h3 id="r_labeler"> <?php echo _('Labeler')?></h3>
        
              
        <?php include 'labelerSettings.php';?>     




  </div>
  <?php }?>
  
    <div <?php if($show==9){ echo 'class="box"'; } else { echo 'class="boxed"'; } ?>>
      <h3 id="r_other"> <?php echo _('Other Settings')?></h3>
      
       <?php include 'otherSettings.php';?>   
              
        
    </div>
  </div><!---/content-container--->
</div><!-- /content -->
