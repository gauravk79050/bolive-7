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
<style>
#sidebar, #sidebarSet{
  display:none;
}
.save_b{
  padding: 20px 60px 20px 20px !important;
    text-align: right;
}
</style>
<script>
var upload="<?php echo _('Upload Image');?>";
var jcrop_api,boundx,boundy,xsize,ysize,$preview,$pcnt,$pimg;

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
        if(i == 1){
          $("#uploaded_img").html(response);
          $("#uploaded_img").focus();
        }
        else{
          $("#uploaded_image").html(response);
          $("#uploaded_image").focus();
        }
      }
    });
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
jQuery(document).ready(function(){
  tinyMCE.init({
    mode : "exact",
    
    elements: "message_front,obsdesk_footer_text,help_text",
    
    theme : "advanced",
    
    plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template", 
    
    // Theme options
    
    //theme_advanced_buttons1 : "bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull",
    
    theme_advanced_buttons2 : "",
    
    theme_advanced_buttons3 : "",
    
    theme_advanced_buttons4 : "",
    
    theme_advanced_toolbar_location : "top",
    
    theme_advanced_toolbar_align : "center",
    
    theme_advanced_statusbar_location : "bottom",
    
    template_external_list_url : "js/template_list.js",
    
    external_link_list_url : "js/link_list.js",
    
    external_image_list_url : "js/image_list.js",
    
    media_external_list_url : "js/media_list.js",
    
    template_replace_values : {
    
      username : "Some User",   
      staffid : "9912874"
    }
  });
  
  $(".thickboxed_label").click(function(){
		var num = $(this).attr('attr_id');
		tb_show(upload, base_url+"cp/image_upload/ajax_img_upload/cp/"+num+"?height=400&width=600", "true");
	});
  $(".thickboxed_label_kaart").click(function(){
		var num = $(this).attr('attr_id');
		tb_show(upload, base_url+"cp/image_upload/ajax_img_upload/cp/"+num+"?height=400&width=600", "true");
	});
});
</script>

<script type="text/javascript" src="<?php echo base_url()?>assets/cp/new_js/colorpicker/jquery.miniColors.js?version=<?php echo version;?>"></script>
<link type="text/css" rel="stylesheet" href="<?php echo base_url()?>assets/cp/new_css/colorpicker/jquery.miniColors.css?version=<?php echo version;?>" />

<link type="text/css" href="<?php echo base_url()?>assets/cp/css/ui-lightness/jquery-ui-1.8.16.custom.css?version=<?php echo version;?>" rel="stylesheet" />  
<script type="text/javascript" src="<?php echo base_url()?>assets/cp/js/jquery-ui-1.8.16.custom.min.js?version=<?php echo version;?>"></script>
    
<script type="text/javascript">
  //jQuery.noConflict();
  jQuery(document).ready(function($){
    $(".colors").minicolors();
  });
</script>

<style type="text/css">
  select{ 
    border: 1px solid #CCCCCC !important;
    width:150px !important
  }

  ul#theme-list{
    list-style:none;
  margin:0px;
  padding:0px;
  }
  
  ul#theme-list li{
    float: left;
    list-style: none outside none;
    margin-bottom: 10px;
    margin-left: 20px;
    width: 220px;
  }
  
  ul#theme-list li strong{
    font-weight: bold;
  font-size:14px;
  }
  
  ul#theme-list li img{
    margin:0 auto;
  height: 160px;
    width: 190px;
  }
</style>
<?php 
   $head_bg_color_1 = "313D4C";
   $head_text_color_1 = "000000";
   $head_bg_color_2 = "202730";
   $head_text_color_2 = "000000";
   $button_bg_color_1 = "2274AD";
   $button_text_color_1 = "000000";
   $button_bg_color_2 = "F77268";
   $button_text_color_2 = "000000";
   $availability_bg_color = "8ED12E";
   $availability_text_color = "000000";
?>
<div id="main">

    <!-- MAIN HEADER -->
    <div id="main-header">
        <h2><?php echo _('Overview'); ?></h2>
     </div>
     <!-- /MAIN HEADER -->
     <!-- 
     <div class="header_link">
     <?php if($this->session->userdata('menu_type') == 'fooddesk_light'){?>
      <?php echo _("Link to connect to your website");?>: <a href="<?php echo $this->config->item("allergen_kart_url").$this->company->company_slug;?>" target="_blank"><?php echo $this->config->item("allergen_kart_url").$this->company->company_slug;?></a>
    <?php }elseif(($this->session->userdata('menu_type') == 'fdd_light') || ($this->session->userdata('menu_type') == 'fdd_pro') || ($this->session->userdata('menu_type') == 'fdd_premium')){?>
        <?php echo _("Link to OBSdesk");?>: <a href="<?php echo $this->config->item("desk_url").$this->company->company_slug;?>" target="_blank"><?php echo $this->config->item("desk_url").$this->company->company_slug;?></a><br>
        <?php echo _("Link to connect to your website");?>: <a href="<?php echo $this->config->item("allergen_kart_url").$this->company->company_slug;?>" target="_blank"><?php echo $this->config->item("allergen_kart_url").$this->company->company_slug;?></a>
     <?php }else{?>
      <?php echo _("Link to OBSdesk");?>: <a href="<?php echo $this->config->item("desk_url").$this->company->company_slug;?>" target="_blank"><?php echo $this->config->item("desk_url").$this->company->company_slug;?></a>
      <?php }?>
      </div>
       -->
      
     <!-- CONTENT -->
     <div id="content" style="width: 100%;">
         
         <div id="content-container">
          <div class="box">
              <h3 id="desk_genSettings1"><?php echo _('Overview'); ?></h3>
        <div class="table">
                        <table cellspacing="0">
                          <tbody>
                            <!-- start -->
                          <?php if($this->session->userdata('menu_type') == 'fooddesk_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium' || $this->session->userdata('menu_type') == 'fdd_light'  ){ ?>
                           <tr>
                              <td class="textlabel"><img alt="app-store" src="<?php echo base_url()?>assets/cp/images/info1.jpg"></td>
                              <td>
                              <strong><?php echo _('Allergenkaart'); ?></strong><br>
                              <?php echo _('This meant for linking this to your sight so visitor can check all products(the one with status is "show") for allergens. They can use an advanced filter to filter out allergen-free-products.'); ?>
                                <br><br>
                               <?php echo _('Link'); ?>:<a href="<?php echo $this->config->item("allergen_kart_url").$this->company->company_slug;?>"  target="_blank"><?php echo $this->config->item("allergen_kart_url").$this->company->company_slug;?></a>
                              </td>
                            </tr>
                            <?php } ?>

                            <?php if($this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium' || $this->session->userdata('menu_type') == 'fdd_light'  ){ ?>
                            <tr>
                              <td class="textlabel"><img alt="app-store" src="<?php echo base_url()?>assets/cp/images/info2.jpg"></td>
                              <td>
                              <strong><?php echo _('infoDesk'); ?></strong><br>
                              <?php echo _('You can use this link to show your products on a touchscreen or ipad in your shop to inform your clients about allergancs.An advanced fallergen filter in included.'); ?>
                                <br><br>
                               <?php echo _('Link'); ?>:<a href="<?php echo $this->config->item("desk_url").$this->company->company_slug;?>"  target="_blank"><?php echo $this->config->item("desk_url").$this->company->company_slug;?></a> <?php echo _('or download the app'); ?> <a href="https://itunes.apple.com/dk/app/fooddesk/id1041991909?mt=8"><strong> <?php echo _(' from app store'); ?></strong></a><?php echo _(' and use the code '); ?> <strong><?php if(!empty($uw_code)){echo $uw_code[0]['api_secret'];}?></strong>
                              </td>
                            </tr>
                            <?php } ?>

                            <?php if($this->session->userdata('menu_type') == 'fooddesk_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium' || $this->session->userdata('menu_type') == 'fdd_light'  ){ ?>
                             <tr>
                              <td class="textlabel"><img alt="app-store" src="<?php echo base_url()?>assets/cp/images/info3.jpg"></td>
                              <td>
                              <strong><?php echo _('PDF-version'); ?></strong><br>
                              <?php echo _('This meant for linking this to your sight so visitor can check all products(the one with status is "show") for allergens. They can use an advanced filter to filter out allergen-free-products.'); ?>
                                <br><br>
                               <?php echo _('Link'); ?>:<a href="<?php echo $this->config->item("allergen_kart_pdf_version_url")?>allergenkaart_products_pdf/<?php echo $this->company_id;?>"target="_blank"><?php echo $this->config->item("allergen_kart_pdf_version_url").$this->company->company_slug.'.pdf'?></a>&nbsp;&nbsp;<?php echo _('or you can '); ?>&nbsp;&nbsp;<a href="<?php echo $this->config->item("allergen_kart_pdf_version_url")?>allergenkaart_products_pdf/<?php echo $this->company_id;?>"><?php echo _('download the pdf here'); ?></a>
                              </td>
                            </tr>
                            <?php } ?>
                             <!-- <tr>
                              <td class="textlabel"><?php echo _('Download the app'); ?></td>
                              <td>
                              <?php if($this->session->userdata('menu_type') == 'fooddesk_light'){?>
                              <a href="https://itunes.apple.com" target="_blank"><img alt="app-store" src="<?php echo base_url()?>assets/cp/images/App-Store.png"></a>&nbsp;&nbsp;&nbsp;
                              <?php }else{?>
                              <a href="https://itunes.apple.com/dk/app/fooddesk/id1041991909?mt=8" target="_blank"><img alt="app-store" src="<?php echo base_url(); ?>assets/cp/images/App-Store.png"></a>&nbsp;&nbsp;&nbsp;
                              <?php }?>
                                <a href="https://play.google.com/store" target="_blank"><img alt="play-store" src="<?php echo base_url()?>assets/cp/images/android-app.png"></a>
                              </td>
                            </tr> -->

                           <!--  end -->
                        
                            <?php if($this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium' || $this->session->userdata('menu_type') == 'fdd_light'  ){ ?>
                              <tr>
                              <td class="textlabel"><img src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=<?php echo $this->config->item("desk_url").$this->company->company_slug;?>&choe=UTF-8" /></td>
                              <td>
                                <strong><?php echo _('QR-code'); ?></strong><br>
                                <?php echo _('You can placethe QR-code somewhere on your menuecard or article list. In that way your customer can scan and see the infoDesk on there smartphone and filter allergens.'); ?>
                                  <br><br>
                                 <?php echo _('You can download it '); ?><a href="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=<?php echo $this->config->item("desk_url").$this->company->company_slug;?>&choe=UTF-8"  target="_blank"><strong><?php echo _('here '); ?></strong></a>
                              </td>
                            </tr>
                           
                            <?php }else{?> 
                            <!-- 
                             <tr>
                              <td class="textlabel"><?php echo _('QR code for the menu'); ?></td>
                              <td>
                                <img src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=<?php echo $this->config->item("allergen_kart_url").$this->company->company_slug;?>&choe=UTF-8" />
                              </td>
                            </tr> -->

                          
                            <?php }?>
                          </tbody>
                        </table>
              </div>
          </div>
           
         <?php if($this->session->userdata('menu_type') != 'fooddesk_light'){?>
         <div id="main-header">
             <h2><?php echo _('OBS Desk Settings'); ?></h2>
           </div>
            <div class="box">
              <h3 id="desk_genSettings"><?php echo _('General Settings'); ?></h3>
        <div class="table">
                    <?php //print_r($desk_settings); ?>
                <form name="frm_general_settings" id="frm_general_settings" method="post" enctype="multipart/form-data" action="">
                        <table cellspacing="0">
                          <tbody>
                            <tr <?php if($type_id == 14 || $type_id == 15 || $type_id == 22 ):?> style="display:none" <?php endif;?>>
                              <td class="textlabel"><?php echo _('Email Address'); ?></td>
                              <td>
                                <input type="email" name="email_id" id="email_id" class="text short" value="<?php echo $desk_settings['email_id']; ?>">
                                &nbsp;&nbsp;&nbsp;
                                <a class="help" href="javascript:;" title="<?php echo _('Enter your email address where you want to receive your orders !'); ?>">
                                <img width="16" height="16" src="<?php echo base_url(); ?>assets/cp/images/help.png"></a>
                                <?php echo form_error('email_id'); ?>
                              </td>
                            </tr>
                                            
                            <tr>
                                <td class="textlabel"><?php echo _('Change Language'); ?></td>
                                <td>
                                   <select style="margin:0px;padding:2px;" id="lang_id" name="lang_id">
                                      <?php if( !$desk_settings['lang_id'] ){
                                        $desk_settings['lang_id'] = 2;
                                      }
                                      if(!empty($languages)) { foreach($languages as $l) { ?>
                                      <option value="<?php echo $l->id; ?>" <?php if($desk_settings['lang_id'] == $l->id){ echo 'selected="selected"'; } ?>><?php echo $l->lang_name; ?></option>
                                      <?php } } else { ?>
                                      <option value="0"><?php echo _('No Lang Set'); ?></option>
                                      <?php } ?>
                                   </select>
                                   <?php echo form_error('lang_id'); ?>
                                </td>
                            </tr>
                            
                            <tr <?php if($type_id == 14 || $type_id == 15 || $type_id == 22):?> style="display:none" <?php endif;?>>
                              <td valign="top" class="textlabel" style="padding-top:10px"><?php echo _('Show message frontend'); ?></td>
                              <td><table width="100%" border="0" class="override">
                                  <tbody>
                                    <tr>
                                      <td valign="middle" height="30">
                                        <input type="checkbox" name="show_message_front" id="show_message_front" class="checkbox" value="1" <?php if($desk_settings['show_message_front'] == 1){ echo 'checked="checked"'; } ?> ><br />
                                      </td>
                                    </tr>
                                    <tr>
                                      <td>
                                         <textarea name="message_front" id="message_front" style="width:70%;height: 200px;"><?php echo $desk_settings['message_front']; ?></textarea>
                                      </td>
                                    </tr>
                                  </tbody>
                                </table></td>
                            </tr>
                          
                            <tr <?php if($type_id == 14 || $type_id == 15 || $type_id == 22):?> style="display:none" <?php endif;?>>
                              <td width="20%" class="textlabel"><?php echo _('Act as Infocenter'); ?></td>
                              <td style="padding-top:10px; vertical-align:middle">
                                <input type="checkbox" name="act_as_infocenter" id="act_as_infocenter" class="checkbox" value="1" <?php if($desk_settings['act_as_infocenter'] == 1){ echo 'checked="checked"'; } ?>>
                                &nbsp;&nbsp;&nbsp;
                                <a id="help17" href="javascript:;" title="<?php echo _('If you check this option, OBSdesk will act as infocenter where user can view only information about products.'); ?>">
                                  <img width="16" height="16" src="<?php echo base_url(); ?>assets/cp/images/help.png">
                                </a>
                              </td>
                            </tr>
                            
                            <tr <?php if($type_id == 14 || $type_id == 15 || $type_id == 22):?> style="display:none" <?php endif;?>>
                              <td width="20%" class="textlabel"><?php echo _('Activate Numbering'); ?></td>
                              <td style="padding-top:10px; vertical-align:middle">
                                <input type="checkbox" name="activate_numbering" id="activate_numbering" class="checkbox" value="1" <?php if($desk_settings['activate_numbering'] == 1){ echo 'checked="checked"'; } ?>>
                                &nbsp;&nbsp;&nbsp;
                                <a id="help13" href="javascript:;" title="<?php echo _('If you check this option, you need to use a separate digital counter, for managing the orders in a queue.'); ?>">
                                  <img width="16" height="16" src="<?php echo base_url(); ?>assets/cp/images/help.png">
                                </a>
                              </td>
                            </tr>
                                            
                            <tr>
                              <td width="20%" class="textlabel"><?php echo _('Disable Price'); ?></td>
                              <td style="padding-top:10px; vertical-align:middle">
                                <input type="checkbox" name="disable_price" id="disable_price" class="checkbox" value="1" <?php if($desk_settings['disable_price'] == 1){ echo 'checked="checked"'; } ?>>
                                &nbsp;&nbsp;&nbsp;
                                <a id="help13" href="javascript:;" title="<?php echo _('If you would check this option, no prices would be shown to your client on desk !'); ?>">
                                  <img width="16" height="16" src="<?php echo base_url(); ?>assets/cp/images/help.png">
                                </a>
                              </td>
                            </tr>
                            
                            <tr <?php if($type_id == 14 || $type_id == 15 || $type_id == 22):?> style="display:none" <?php endif;?>>
                              <td width="20%" class="textlabel"><?php echo _('Send Orders to my Email'); ?></td>
                              <td style="padding-top:10px; vertical-align:middle">
                                <input type="checkbox" name="send_orders_to_email" id="send_orders_to_email" class="checkbox" value="1" <?php if($desk_settings['send_orders_to_email'] == 1){ echo 'checked="checked"'; } ?>>
                              </td>
                            </tr>
                            
                            <tr <?php if($type_id == 14 || $type_id == 15 || $type_id == 22):?> style="display:none" <?php endif;?>>
                              <td width="20%" class="textlabel"><?php echo _('Show Print Button'); ?></td>
                              <td style="padding-top:10px; vertical-align:middle">
                                <input type="checkbox" name="print_button" id="print_button" class="checkbox" value="1" <?php if($desk_settings['print_button'] == 1){ echo 'checked="checked"'; } ?>>
                              </td>
                            </tr>
                            
                            <tr <?php if($type_id == 14 || $type_id == 15 || $type_id == 22):?> style="display:none" <?php endif;?>>
                              <td width="20%" class="textlabel"><?php echo _('Use pics as (sub)cats'); ?></td>
                              <td style="padding-top:10px; vertical-align:middle">
                                <input type="checkbox" name="use_pics" id="use_pics" class="checkbox" value="1" <?php if($desk_settings['use_pics'] == 1){ echo 'checked="checked"'; } ?>>
                              </td>
                            </tr>
                                  
                            <tr <?php if($type_id == 14 || $type_id == 15 || $type_id == 22):?> style="display:none" <?php endif;?>>
                              <td valign="top" class="textlabel" style="padding-top:10px"><?php echo _('Help Text Frontend'); ?></td>
                              <td><table width="100%" border="0" class="override">
                                  <tbody>
                                    <tr>
                                      <td>
                                         <textarea name="help_text" id="help_text" style="width:70%;height: 200px;"><?php echo $desk_settings['help_text']; ?></textarea>
                                      </td>
                                    </tr>

                                  </tbody>
                                </table></td>
                            </tr>


                            <!-- <tr>
                             <td></td>
                              <td>
                               <?php //if($desk_settings['comp_default_image']){?>
                                 <img alt="infodesk default image" src="<?php //echo base_url().'assets/cp/images/infodesk_default_image/'. $desk_settings['comp_default_image'];?>" style="height:100px">
                               <?php //}?>
                             </td>
                            </tr>

                            <tr>
                               <td class="textlabel"><?php //echo _('Infodesk default image')?>&nbsp;<a title="<?php //echo _('Please upload a default infodesk image in jpg/gif/png format')?>" href="#" id="help-prod0"><img width="16" height="16" src="<?php //echo base_url(); ?>assets/cp/images/help.png"></a></td>
                               <td style="padding-right:00px">
                               <div id="uploaded_image"></div>
                                   <input type="hidden" id="x" name="x" />
                                   <input type="hidden" id="y" name="y" />
                                   <input type="hidden" id="w" name="w" />
                                   <input type="hidden" id="h" name="h" />
                               <div>
                                 <a href="javascript:;" class="thickboxed" attr_id="2" style="text-decoration: none;"><input type="button" name="upload_image" id="upload_image" value="<?php //echo _("Upload Image Here");?>" /></a>
                               </div>
                              </td>
                            </tr> -->
                            
                            <tr>
                                <td class="textlabel"><?php echo _('Show sheet in infodesk')?></td>
                                <td>
                                    <p style="display:inline;float:left;"><input type="radio" name="sheet_in_desk" value="1" <?php if($desk_settings['show_sheet']==1):?>checked="checked"<?php endif;?>></p><b style="display:inline;float:left;margin-left:2px;"><?php echo _('yes');?></b>
                                    
                                     <p style="display:inline;float:left;margin-left:10px"><input type="radio" name="sheet_in_desk" value="0" <?php if($desk_settings['show_sheet']==0):?>checked="checked"<?php endif;?>></p><b style="display:inline;float:left;margin-left:2px;"><?php echo _('no');?></b>
                                </td>
                            </tr>


                            <tr>
                              <td colspan="2" class="save_b">
                                <input type="submit" name="btn_update" id="btn_update" class="submit" value="<?php echo _('UPDATE'); ?>">
                                <input type="hidden" name="action" id="action" value="desk_general_setting">
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </form>
              </div>
          </div>
    
     
      <div class="box">
              <h3 id="desk_genSettings"><?php echo _('Look and Feel'); ?></h3>
        <div class="table">
                <form action="" enctype="multipart/form-data" method="post" id="frm_obsdesk_settings" name="frm_obsdesk_settings">
                       <table border="0">
                          <tbody>
                          <tr <?php if($type_id == 14 || $type_id == 15 || $type_id == 22):?> style="display:none" <?php endif;?>>
                            <td width="170" class="textlabel"><span><?php echo _('Desk Title'); ?></span></td>
                            <td>
                                <input type="text" value="<?php echo $this->company->obsdesk_company_name; ?>" class="text medium" size="30" id="obsdesk_company_name" name="obsdesk_company_name"><?php echo form_error('obsdesk_company_name'); ?>
                                <input type="hidden" value="<?php echo $this->company->company_name; ?>" id="company_name" name="company_name">
                            </td>
                          </tr>
                     <!-- <tr>
                            <td width="170" class="textlabel"><span><?php //echo _('Desk Logo'); ?></span></td>
                            <td>
                                <?php //if( isset($this->company->obsdesk_logo) && $this->company->obsdesk_logo != '' ) { ?> 
                                <img src="<?php //echo base_url(); ?>assets/company-logos/<?php //echo $this->company->obsdesk_logo; ?>" height="110" />
                                <input type="hidden" value="<?php //echo //$this->company->obsdesk_logo; ?>" class="text medium" size="30" id="old_obsdesk_logo" name="old_obsdesk_logo">
                                <?php //} else { ?>
                                <strong><?php //echo _('No Logo Set !'); ?></strong>
                                <?php //} ?>
                                <br /><br />
                                <?php //echo ('Upload to change'); ?>&nbsp;:&nbsp;
                                <input type="file" class="text medium" size="30" id="obsdesk_logo" name="obsdesk_logo">
                                  <?php //echo form_error('obsdesk_logo'); ?>
                            </td>
                          </tr>  -->
                          <?php if( isset($this->company->obsdesk_logo) && $this->company->obsdesk_logo != '' ) { ?> 
                        	<tr>
	                         	 <td>&nbsp;</td>
	                         	 <td>
	                            	<img alt="desk logo" src="<?php echo base_url(); ?>assets/company-logos/<?php echo $this->company->obsdesk_logo; ?>" style="height:80px;width: 300px;">
	                          	</td>
                       		</tr>
                       		<?php }?>
                            <tr>
	                  		<td class="textlabel" width="170"><span><?php echo _('Desk Logo')?></span></td>
                  			<td style="padding-right:250px">
                  				<div id="desk_logo">
	                  			  	<div id="uploaded_image5"></div>
			                            <input type="hidden" id="x" name="x" />
					                    <input type="hidden" id="y" name="y" />
					                    <input type="hidden" id="w" name="w" />
					                    <input type="hidden" id="h" name="h" />
		                            <div>
		                            	<?php echo ('Upload to change'); ?>&nbsp;:&nbsp;
	                              		<a href="javascript:;" class=thickboxed_label attr_id="5" style="text-decoration: none;"><input type="button" name="upload_img" id="upload_img" value="<?php echo _("Image upload");?>" /></a>
	                            	</div>
	                            </div>
                  			</td>
                      	</tr>
                          <tr>
                            <td width="170" class="textlabel"><span><?php echo _('Desk Footer Text'); ?></span></td>
                            <td>
                                <textarea name="obsdesk_footer_text" id="obsdesk_footer_text" style="width:70%;height: 200px;"><?php if( isset($this->company->obsdesk_footer_text) && $this->company->obsdesk_footer_text != '' ) { echo $this->company->obsdesk_footer_text; } ?></textarea>
                            </td>
                          </tr>
                          
                          <tr>
                            <td width="170" class="textlabel"><span><?php echo _('Apply this style'); ?></span></td>
                            <td>
                                <input type="checkbox" name="apply_css" id="apply_css" class="checkbox" value="1" <?php if($desk_settings['apply_css'] == 1){ echo 'checked="checked"'; } ?>>
                                &nbsp;&nbsp;&nbsp;
                                <a id="help13" href="javascript:;" title="<?php echo _('If you check this option, then following style will apply on front-end.'); ?>">
                                  <img width="16" height="16" src="<?php echo base_url(); ?>assets/cp/images/help.png">
                                </a>
                            </td>
                          </tr>
                          
                          <tr>
              <td width="20%" class="textlabel"><?php if($type_id == 14 || $type_id == 15 || $type_id == 22):echo _('Color of bar');else:echo _('Header bg color');endif;?></td>
              <td style="padding-top:10px; vertical-align:bottom">
                <input type="text" class="text short colors" id="head_bg_color_1" name="head_bg_color_1" value="<?php if($desk_section_design && $desk_section_design['0']->head_bg_color_1){ echo $desk_section_design['0']->head_bg_color_1; }else{ echo '#'.$head_bg_color_1; } ?>" style="height: 30px;">
                <span style="margin: 0px 50px; font-weight: bold; <?php if($type_id == 14 || $type_id == 15 || $type_id == 22):?>display:none;<?php endif;?>"><?php echo _("Text Color");?></span>
                <input type="text" class="text short <?php if($type_id != 14 && $type_id != 15 && $type_id != 22 ){?>colors<?php }?>" id="head_text_color_1" name="head_text_color_1" <?php if($type_id == 14 || $type_id == 15 || $type_id == 22):?> style="display:none; "<?php endif;?> value="<?php if($desk_section_design && $desk_section_design['0']->head_text_color_1){ echo $desk_section_design['0']->head_text_color_1; }else{ echo '#'.$head_text_color_1; } ?>" style="height: 30px;">
              </td>
              </tr>
              <tr <?php if($type_id == 14 || $type_id == 15 || $type_id == 22):?> style="display:none" <?php endif;?>>
              <td width="20%" class="textlabel"><?php echo _('Header bg color 2')?></td>
              <td style="padding-top:10px; vertical-align:bottom">
                <input type="text" class="text short colors" id="head_bg_color_2" name="head_bg_color_2" value="<?php if($desk_section_design && $desk_section_design['0']->head_bg_color_2){ echo $desk_section_design['0']->head_bg_color_2; }else{ echo '#'.$head_bg_color_2; } ?>" style="height: 30px;">
                <span style="margin: 0px 50px; font-weight: bold;"><?php echo _("Text Color");?></span>
                <input type="text" class="text short colors" id="head_text_color_2" name="head_text_color_2" value="<?php if($desk_section_design && $desk_section_design['0']->head_text_color_2){ echo $desk_section_design['0']->head_text_color_2; }else{ echo '#'.$head_text_color_2; } ?>" style="height: 30px;">
              </td>
              </tr>
              <tr <?php if($type_id == 14 || $type_id == 15 || $type_id == 22):?> style="display:none" <?php endif;?>>
              <td width="20%" class="textlabel"><?php echo _('Bg colors of buttons')?><br>(<?php echo _("Help,Search,Bestellen");?>)</td>
              <td style="padding-top:10px; vertical-align:bottom">
                <input type="text" class="text short colors" id="button_bg_color_1" name="button_bg_color_1" value="<?php if($desk_section_design && $desk_section_design['0']->button_bg_color_1){ echo $desk_section_design['0']->button_bg_color_1; }else{ echo '#'.$button_bg_color_1; } ?>" style="height: 30px;">
                <span style="margin: 0px 50px; font-weight: bold;"><?php echo _("Text Color");?></span>
                <input type="text" class="text short colors" id="button_text_color_1" name="button_text_color_1" value="<?php if($desk_section_design && $desk_section_design['0']->button_text_color_1){ echo $desk_section_design['0']->button_text_color_1; }else{ echo '#'.$button_text_color_1; } ?>" style="height: 30px;">
              </td>
              </tr>
              <tr <?php if($type_id == 14 || $type_id == 15 || $type_id == 22):?> style="display:none" <?php endif;?>>
              <td width="20%" class="textlabel"><?php echo _('Bg colors of buttons')?><br>(<?php echo _("Checkout,Shopping-cart");?>)</td>
              <td style="padding-top:10px; vertical-align:bottom">
                <input type="text" class="text short colors" id="button_bg_color_2" name="button_bg_color_2" value="<?php if($desk_section_design && $desk_section_design['0']->button_bg_color_2){ echo $desk_section_design['0']->button_bg_color_2; }else{ echo '#'.$button_bg_color_2; } ?>" style="height: 30px;">
                <span style="margin: 0px 50px; font-weight: bold;"><?php echo _("Text Color");?></span>
                <input type="text" class="text short colors" id="button_text_color_2" name="button_text_color_2" value="<?php if($desk_section_design && $desk_section_design['0']->button_text_color_2){ echo $desk_section_design['0']->button_text_color_2; }else{ echo '#'.$button_text_color_2; } ?>" style="height: 30px;">
              </td>
              </tr>
              <tr <?php if($type_id == 14 || $type_id == 15 || $type_id == 22):?> style="display:none" <?php endif;?>>
              <td width="20%" class="textlabel"><?php echo _('Bg color of availability')?></td>
              <td style="padding-top:10px; vertical-align:bottom">
                <input type="text" class="text short colors" id="availability_bg_color" name="availability_bg_color" value="<?php if($desk_section_design && $desk_section_design['0']->availability_bg_color){ echo $desk_section_design['0']->availability_bg_color; }else{ echo '#'.$availability_bg_color; } ?>" style="height: 30px;">
                <span style="margin: 0px 50px; font-weight: bold;"><?php echo _("Text Color");?></span>
                <input type="text" class="text short colors" id="availability_text_color" name="availability_text_color" value="<?php if($desk_section_design && $desk_section_design['0']->availability_text_color){ echo $desk_section_design['0']->availability_text_color; }else{ echo '#'.$availability_text_color; } ?>" style="height: 30px;">
              </td>
              </tr>       
            
                          <tr>
                            <td class="save_b" colspan="2">
                               <input type="submit" value="<?php echo _('UPDATE'); ?>" class="submit" id="btn_update" name="btn_update">
                               <input type="hidden" value="look_n_feel" id="action" name="action">
                            </td>
                          </tr>
                          </tbody>
                       </table>
                       <script>
                        /*function check_deskTitle(){
                                var x = document.forms["frm_obsdesk_settings"]["obsdesk_company_name"].value;
                                                                       
                                if (x == null || x == "") {
                                  alert("<?php //echo _("Company name should not be empty")?>");
                        function check_deskTitle(){
                                var x = document.forms["frm_obsdesk_settings"]["obsdesk_company_name"].value;
                                                                       
                                if (x == null || x == "") {
                                  alert("<?php echo _("Company name should not be empty")?>");
                                            return true;
                                }
                        }*/
                       </script>
                    </form>
                </div>
            </div>
            <?php }?>
           <?php if($this->session->userdata('menu_type') == 'fooddesk_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium' || $this->session->userdata('menu_type') == 'fdd_light'  ){ ?>
              <div id="main-header">
                <h2><?php echo _('Allergenkaart Settings'); ?></h2>
              </div>
              <div class="box">
              <h3 id="desk_genSettings"><?php echo _('Look and Feel'); ?></h3>
              <div class="table">
                <form action="" enctype="multipart/form-data" method="post" id="frm_obsdesk_settings" name="frm_obsdesk_settings">
                       <table border="0">
                          <tbody>
                          <tr style="display:none">
                            <td width="170" class="textlabel"><span style="padding-left:20px"><?php echo _('Allergenkaart Title'); ?></span></td>
                            <td>
                                <input type="text" value="<?php echo $this->company->obsdesk_company_name; ?>" class="text medium" size="30" id="obsdesk_company_name" name="obsdesk_company_name"><?php echo form_error('obsdesk_company_name'); ?>
                                <input type="hidden" value="<?php echo $this->company->company_name; ?>" id="company_name" name="company_name">
                            </td>
                          </tr>
                         
                           <?php if($this->session->userdata('menu_type') == 'fooddesk_light'){?>

                         <!--  <tr>
                            <td width="170" class="textlabel"><span style="padding-left:20px"><?php echo _('Allergenkaart Logo'); ?></span></td>
                            <td>
                                <?php if($allkaart_settings && $allkaart_settings['0']->allergenkaart_logo != '' ) { ?> 
                                <img src="<?php echo base_url(); ?>assets/allergenkaart_logos/<?php echo $allkaart_settings['0']->allergenkaart_logo ?>" height="110" />
                                <input type="hidden" value="<?php echo $allkaart_settings['0']->allergenkaart_logo; ?>" class="text medium" size="30" id="old_allergenkaart_logo" name="old_allergenkaart_logo">
                                <?php } else { ?>
                                <strong><?php echo _('No Logo Set !'); ?></strong>
                                <?php } ?>
                                <br /><br />
                                <?php echo ('Upload to change'); ?>&nbsp;:&nbsp;
                                <input type="file" class="text medium" size="30" id="allergenkaart_logo" name="allergenkaart_logo">
                                <?php echo form_error('obsdesk_logo'); ?>
                            </td>
                          </tr> -->
                           <?php if($allkaart_settings && $allkaart_settings['0']->allergenkaart_logo != '') { ?> 
                        	<tr>
	                         	 <td>&nbsp;</td>
	                         	 <td>
	                            	<img alt="allergenenkaaart logo" src="<?php echo base_url(); ?>assets/allergenkaart_logos/<?php echo $allkaart_settings[0]->allergenkaart_logo ?>" style="height:80px;width: 300px;">
	                          	</td>
                       		</tr>
                       		<?php }?>
                            <tr>
	                  		<td class="textlabel" width="170"><span style="padding-left:20px"><?php echo _('Allergenkaart Logo')?></span></a></td>
                  			<td style="padding-right:250px">
                  				<div id="allergenenkaart_logo">
	                  			  	<div id="uploaded_image6"></div>
			                            <input type="hidden" id="x" name="x" />
					                    <input type="hidden" id="y" name="y" />
					                    <input type="hidden" id="w" name="w" />
					                    <input type="hidden" id="h" name="h" />
		                            <div>
		                            	<?php echo ('Upload to change'); ?>&nbsp;:&nbsp;
	                              		<a href="javascript:;" class=thickboxed_label_kaart attr_id="6" style="text-decoration: none;"><input type="button" name="upload_img_kaart" id="upload_img_kaart" value="<?php echo _("Image upload");?>" /></a>
	                            	</div>
	                            </div>
                  			</td>
                      	</tr>
                          <?php } ?>


                          <tr style="display:none">
                            <td width="170" class="textlabel"><span style="padding-left:20px"><?php echo _('Desk Footer Text'); ?></span></td>
                            <td>
                                <textarea name="obsdesk_footer_text" id="obsdesk_footer_text" style="width:70%;height: 200px;"><?php if( isset($this->company->obsdesk_footer_text) && $this->company->obsdesk_footer_text != '' ) { echo $this->company->obsdesk_footer_text; } ?></textarea>
                            </td>
                          </tr>
                           <tr>
                              <td width="170" class="textlabel"><span style="padding-left:20px"><?php echo _('Change Language'); ?></span></td>
                                <td>
                                   <select style="margin:0px;padding:2px;" name="lang_id">
                                      <?php
                                      if(!empty($languages)) { foreach($languages as $l) { ?>
                                      <option value="<?php echo $l->id; ?>" <?php if($allkaart_settings[0]->lang == $l->id){ echo 'selected="selected"'; } ?>><?php echo $l->lang_name; ?></option>
                                      <?php } } else { ?>
                                      <option value="0"><?php echo _('No Lang Set'); ?></option>
                                      <?php } ?>
                                   </select>
                                   <?php echo form_error('lang_id'); ?>
                                </td>
               				</tr>
                          <tr>
                  <td width="170" class="textlabel"><span style="padding-left:20px"><?php echo _('Show Product Image'); ?></span></td>
                  <td>
                      <input type="radio" name="apply_product_image" id="apply_product_image"  value="1" style="display:inline;float:left;" <?php if($allkaart_settings && $allkaart_settings['0']->apply_product_image == 1){ echo 'checked="checked"'; } ?>>&nbsp;<b style="display:inline;float:left;margin-left:7px;">yes</b>
                                &nbsp;&nbsp;&nbsp;
                      <input type="radio" name="apply_product_image" id="apply_product_image1" class="radio" value="0" style="display:inline;float:left;margin-left:5px;" <?php if($allkaart_settings && $allkaart_settings['0']->apply_product_image == 0){ echo 'checked="checked"'; } ?>>&nbsp;<b style="display:inline;float:left;margin-left:2px;">no</b>
                            
                      <a id="help13" href="javascript:;" title="<?php echo _('If you choose yes then product image will be shown in template.'); ?>">
                      <img width="16" height="16" src="<?php echo base_url(); ?>assets/cp/images/help.png">
                      </a>
                  </td>
              </tr>

                          
                         <tr>
                            <td width="170" class="textlabel"><span style="padding-left:20px"><?php echo _('Apply this style'); ?></span></td>
                            <td>
                                <input type="checkbox" name="apply_this_style" id="apply_this_style" class="checkbox" value="1" <?php if($allkaart_settings && $allkaart_settings['0']->apply_css == 1){ echo 'checked="checked"'; } ?>>
                                &nbsp;&nbsp;&nbsp;
                                <a id="help13" href="javascript:;" title="<?php echo _('If you check this option, then following style will apply on front-end.'); ?>">
                                  <img width="16" height="16" src="<?php echo base_url(); ?>assets/cp/images/help.png">
                                </a>
                            </td>
                          </tr>
                          
                          
                          
                          <tr>
				              <td width="170" class="textlabel"><span style="padding-left:20px"><?php echo _('Sidebar Color');?></span></td>
				              <td style="padding-top:10px; vertical-align:bottom">
				                <input type="text" class="text short colors" id="sidebar_bg_color_1" name="sidebar_bg_color_1" value="<?php if($allkaart_settings && $allkaart_settings['0']->sidebar_bg_color_1){ echo $allkaart_settings['0']->sidebar_bg_color_1; }else{ echo '#313d4c'; } ?>" style="height: 30px;">
				                <span style="margin: 0px 50px; font-weight: bold;"><?php echo _("Text Color");?></span>
				                <input type="text" class="text short colors" id="sidebar_text_color_1" name="sidebar_text_color_1" value="<?php if($allkaart_settings && $allkaart_settings['0']->sidebar_text_color_1){ echo $allkaart_settings['0']->sidebar_text_color_1; }else{ echo '#FFFFFF'; } ?>" style="height: 30px;">
				              </td>
			              </tr>
              
              
              				
              
              

              <tr>
              <td width="170" class="textlabel"><span style="padding-left:20px"><?php echo _('Filter Button Color');?></span></td>
              <td style="padding-top:10px; vertical-align:bottom">
                <input type="text" class="text short colors" id="filter_bg_color_1" name="filter_bg_color_1" value="<?php if($allkaart_settings && $allkaart_settings['0']->filter_bg_color_1){ echo $allkaart_settings['0']->filter_bg_color_1; }else{ echo '#228BE1'; } ?>" style="height: 30px;">
                <span style="margin: 0px 50px; font-weight: bold;"><?php echo _("Text Color");?></span>
                <input type="text" class="text short colors" id="filter_text_color_1" name="filter_text_color_1" value="<?php if($allkaart_settings && $allkaart_settings['0']->filter_text_color_1){ echo $allkaart_settings['0']->filter_text_color_1; }else{ echo '#FFF6FB'; } ?>" style="height: 30px;">
              </td>
              </tr>


              <!-- <tr>
                  <td width="170" class="textlabel"><span style="padding-left:20px"><?php echo _('Allergen Image'); ?></span></td>
                  <td>
                      <input type="checkbox" name="apply_product_image" id="apply_product_image" class="checkbox" value="1" <?php if($allkaart_settings && $allkaart_settings['0']->apply_product_image == 1){ echo 'checked="checked"'; } ?>>
                                &nbsp;&nbsp;&nbsp;
                      <a id="help13" href="javascript:;" title="<?php echo _('If you check this option, then following style will apply on front-end.'); ?>">
                      <img width="16" height="16" src="<?php echo base_url(); ?>assets/cp/images/help.png">
                      </a>
                  </td>
              </tr> -->

              <tr>
              <td width="190" class="textlabel"><span style="padding-left:20px">
              <?php echo _('Menu Item Active Color');?></span></td>
              <td style="padding-top:10px; vertical-align:bottom">
                <input type="text" class="text short colors" id="active_bg_color_1" name="active_bg_color_1" value="<?php if($allkaart_settings && $allkaart_settings['0']->active_bg_color_1){ echo $allkaart_settings['0']->active_bg_color_1; }else{ echo '#FFFFFF'; } ?>" style="height: 30px;">
                <span style="margin: 0px 50px; font-weight: bold;"><?php echo _("Text Color");?></span>
                <input type="text" class="text short colors" id="active_text_color_1" name="active_text_color_1" value="<?php if($allkaart_settings && $allkaart_settings['0']->active_text_color_1){ echo $allkaart_settings['0']->active_text_color_1; }else{ echo '#FFFFFF'; } ?>" style="height: 30px;">
              </td>
              </tr>



              <tr style="display:none">
              <td width="20%" class="textlabel"><?php echo _('Header bg color 2')?></td>
              <td style="padding-top:10px; vertical-align:bottom">
                <input type="text" class="text short colors" id="head_bg_color_2" name="head_bg_color_2" value="<?php if($desk_section_design && $desk_section_design['0']->head_bg_color_2){ echo $desk_section_design['0']->head_bg_color_2; }else{ echo '#'.$head_bg_color_2; } ?>">
                <span style="margin: 0px 50px; font-weight: bold;"><?php echo _("Text Color");?></span>
                <input type="text" class="text short colors" id="head_text_color_2" name="head_text_color_2" value="<?php if($desk_section_design && $desk_section_design['0']->head_text_color_2){ echo $desk_section_design['0']->head_text_color_2; }else{ echo '#'.$head_text_color_2; } ?>">
              </td>
              </tr>
              <tr style="display:none">
              <td width="20%" class="textlabel"><?php echo _('Bg colors of buttons')?><br>(<?php echo _("Help,Search,Bestellen");?>)</td>
              <td style="padding-top:10px; vertical-align:bottom">
                <input type="text" class="text short colors" id="button_bg_color_1" name="button_bg_color_1" value="<?php if($desk_section_design && $desk_section_design['0']->button_bg_color_1){ echo $desk_section_design['0']->button_bg_color_1; }else{ echo '#'.$button_bg_color_1; } ?>">
                <span style="margin: 0px 50px; font-weight: bold;"><?php echo _("Text Color");?></span>
                <input type="text" class="text short colors" id="button_text_color_1" name="button_text_color_1" value="<?php if($desk_section_design && $desk_section_design['0']->button_text_color_1){ echo $desk_section_design['0']->button_text_color_1; }else{ echo '#'.$button_text_color_1; } ?>">
              </td>
              </tr>
              <tr style="display:none">
              <td width="20%" class="textlabel"><?php echo _('Bg colors of buttons')?><br>(<?php echo _("Checkout,Shopping-cart");?>)</td>
              <td style="padding-top:10px; vertical-align:bottom">
                <input type="text" class="text short colors" id="button_bg_color_2" name="button_bg_color_2" value="<?php if($desk_section_design && $desk_section_design['0']->button_bg_color_2){ echo $desk_section_design['0']->button_bg_color_2; }else{ echo '#'.$button_bg_color_2; } ?>">
                <span style="margin: 0px 50px; font-weight: bold;"><?php echo _("Text Color");?></span>
                <input type="text" class="text short colors" id="button_text_color_2" name="button_text_color_2" value="<?php if($desk_section_design && $desk_section_design['0']->button_text_color_2){ echo $desk_section_design['0']->button_text_color_2; }else{ echo '#'.$button_text_color_2; } ?>">
              </td>
              </tr>
              <tr style="display:none">
              <td width="20%" class="textlabel"><?php echo _('Bg color of availability')?></td>
              <td style="padding-top:10px; vertical-align:bottom">
                <input type="text" class="text short colors" id="availability_bg_color" name="availability_bg_color" value="<?php if($desk_section_design && $desk_section_design['0']->availability_bg_color){ echo $desk_section_design['0']->availability_bg_color; }else{ echo '#'.$availability_bg_color; } ?>">
                <span style="margin: 0px 50px; font-weight: bold;"><?php echo _("Text Color");?></span>
                <input type="text" class="text short colors" id="availability_text_color" name="availability_text_color" value="<?php if($desk_section_design && $desk_section_design['0']->availability_text_color){ echo $desk_section_design['0']->availability_text_color; }else{ echo '#'.$availability_text_color; } ?>">
              </td>
              </tr> 
                          <tr>
                            <td class="save_b" colspan="2">
                               <input type="submit" value="<?php echo _('UPDATE'); ?>" class="submit" id="btn_update" name="btn_update">
                               <input type="hidden" value="allergenkaart_look_n_feel" id="action" name="action">
                            </td>
                          </tr>
                          </tbody>
                       </table>
                    </form>
                </div>
            </div>
            <?php }?>
         </div>
     </div>
     <!-- /CONTENT -->
     
     <script>
     $(document).ready(function(){
       $(".mceEditor").each(function(){
         if($(this).css('display') == 'none'){
             
           $(this).remove();
             }
         });
       
     });
     </script>
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
  // $(document).ready(function(){
  //   //hide_repeated();
  //   $(".thickboxed").click(function(){
  //     var attr_details=$(this).attr('attr_id');
  //     tb_show("<?php echo _("Upload Image");?>", "<?php echo base_url(); ?>cp/image_upload/ajax_img_upload/?height=400&width=600&attr_det="+attr_details, "true");
  //   }); 
  // });

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
</div>
