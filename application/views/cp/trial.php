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
        <h2><?php echo _('OBS Desk Settings'); ?></h2>
     </div>
     <!-- /MAIN HEADER -->
     <div class="header_link">
     <?php if($this->session->userdata('menu_type') == 'fooddesk_light'){?>
    	<?php echo _("Link to connect to your website");?>: <a href="<?php echo $this->config->item("allergen_kart_url").$this->company->company_slug;?>" target="_blank"><?php echo $this->config->item("allergen_kart_url").$this->company->company_slug;?></a>
    <?php }elseif(($this->session->userdata('menu_type') == 'fdd_light') || ($this->session->userdata('menu_type') == 'fdd_pro')){?>
    		<?php echo _("Link to OBSdesk");?>: <a href="<?php echo $this->config->item("desk_url").$this->company->company_slug;?>" target="_blank"><?php echo $this->config->item("desk_url").$this->company->company_slug;?></a><br>
    		<?php echo _("Link to connect to your website");?>: <a href="<?php echo $this->config->item("allergen_kart_url").$this->company->company_slug;?>" target="_blank"><?php echo $this->config->item("allergen_kart_url").$this->company->company_slug;?></a>
     <?php }else{?>
     	<?php echo _("Link to OBSdesk");?>: <a href="<?php echo $this->config->item("desk_url").$this->company->company_slug;?>" target="_blank"><?php echo $this->config->item("desk_url").$this->company->company_slug;?></a>
     	<?php }?>
     	</div>
     <!-- CONTENT -->
     <div id="content" style="width: 100%;">
         
         <div id="content-container">
         	<div class="box">
          		<h3 id="desk_genSettings1"><?php echo _('General Settings'); ?></h3>
				<div class="table">
                        <table cellspacing="0">
                          <tbody>
                            
                            <tr>
                              <td class="textlabel"><?php echo _('Download the app'); ?></td>
                              <td>
                              <?php if($this->session->userdata('menu_type') == 'fooddesk_light'){?>
                              <a href="https://itunes.apple.com" target="_blank"><img alt="app-store" src="<?php echo base_url()?>assets/cp/images/App-Store.png"></a>&nbsp;&nbsp;&nbsp;
                              <?php }else{?>
                              <a href="https://itunes.apple.com/dk/app/fooddesk/id1041991909?mt=8" target="_blank"><img alt="app-store" src="<?php echo base_url()?>assets/cp/images/App-Store.png"></a>&nbsp;&nbsp;&nbsp;
                              <?php }?>
                                <a href="https://play.google.com/store" target="_blank"><img alt="play-store" src="<?php echo base_url()?>assets/cp/images/android-app.png"></a>
                              </td>
                            </tr>
                            
                            <tr>
                              <td class="textlabel"><?php echo _('Uw code'); ?></td>
                              <td>
                                <strong><?php if(!empty($uw_code)){echo $uw_code[0]['api_secret'];}?></strong>
                              </td>
                            </tr>
                            <?php if($this->session->userdata('menu_type') == 'fooddesk_light'){?>
                            <tr>
                              <td class="textlabel"><?php echo _('QR code for the menu'); ?></td>
                              <td>
                                <img src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=<?php echo $this->config->item("allergen_kart_url").$this->company->company_slug;?>&choe=UTF-8" />
                              </td>
                            </tr>
                            <?php }else{?> 
                            <tr>
                              <td class="textlabel"><?php echo _('QR code for the menu'); ?></td>
                              <td>
                                <img src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=<?php echo $this->config->item("desk_url").$this->company->company_slug;?>&choe=UTF-8" />
                              </td>
                            </tr>
                            <?php }?>
                          </tbody>
                        </table>
          		</div>
        	</div>
         <?php if($this->session->userdata('menu_type') != 'fooddesk_light'){?>
            <div class="box">
          		<h3 id="desk_genSettings"><?php echo _('General Settings'); ?></h3>
				<div class="table">
                    <?php //print_r($desk_settings); ?>
            		<form name="frm_general_settings" id="frm_general_settings" method="post" enctype="multipart/form-data" action="">
                        <table cellspacing="0">
                          <tbody>
                            <tr <?php if($type_id == 14):?> style="display:none" <?php endif;?>>
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
                            
                            <tr <?php if($type_id == 14):?> style="display:none" <?php endif;?>>
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
                          
                            <tr <?php if($type_id == 14):?> style="display:none" <?php endif;?>>
                              <td width="20%" class="textlabel"><?php echo _('Act as Infocenter'); ?></td>
                              <td style="padding-top:10px; vertical-align:middle">
                                <input type="checkbox" name="act_as_infocenter" id="act_as_infocenter" class="checkbox" value="1" <?php if($desk_settings['act_as_infocenter'] == 1){ echo 'checked="checked"'; } ?>>
                                &nbsp;&nbsp;&nbsp;
                                <a id="help17" href="javascript:;" title="<?php echo _('If you check this option, OBSdesk will act as infocenter where user can view only information about products.'); ?>">
                                  <img width="16" height="16" src="<?php echo base_url(); ?>assets/cp/images/help.png">
                                </a>
                              </td>
                            </tr>
                            
                            <tr <?php if($type_id == 14):?> style="display:none" <?php endif;?>>
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
                            
                            <tr <?php if($type_id == 14):?> style="display:none" <?php endif;?>>
                              <td width="20%" class="textlabel"><?php echo _('Send Orders to my Email'); ?></td>
                              <td style="padding-top:10px; vertical-align:middle">
                                <input type="checkbox" name="send_orders_to_email" id="send_orders_to_email" class="checkbox" value="1" <?php if($desk_settings['send_orders_to_email'] == 1){ echo 'checked="checked"'; } ?>>
                              </td>
                            </tr>
                            
                            <tr <?php if($type_id == 14):?> style="display:none" <?php endif;?>>
                              <td width="20%" class="textlabel"><?php echo _('Show Print Button'); ?></td>
                              <td style="padding-top:10px; vertical-align:middle">
                                <input type="checkbox" name="print_button" id="print_button" class="checkbox" value="1" <?php if($desk_settings['print_button'] == 1){ echo 'checked="checked"'; } ?>>
                              </td>
                            </tr>
                            
                            <tr <?php if($type_id == 14):?> style="display:none" <?php endif;?>>
                              <td width="20%" class="textlabel"><?php echo _('Use pics as (sub)cats'); ?></td>
                              <td style="padding-top:10px; vertical-align:middle">
                                <input type="checkbox" name="use_pics" id="use_pics" class="checkbox" value="1" <?php if($desk_settings['use_pics'] == 1){ echo 'checked="checked"'; } ?>>
                              </td>
                            </tr>
                                  
                            <tr <?php if($type_id == 14):?> style="display:none" <?php endif;?>>
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
            		<form action="" enctype="multipart/form-data" method="post" id="frm_obsdesk_settings" name="frm_obsdesk_settings" onsubmit="return check_deskTitle()">
                       <table border="0">
                          <tbody>
                          <tr <?php if($type_id == 14):?> style="display:none" <?php endif;?>>
                            <td width="170" class="textlabel"><span style="padding-left:20px"><?php echo _('Desk Title'); ?></span></td>
                            <td>
                                <input type="text" value="<?php echo $this->company->obsdesk_company_name; ?>" class="text medium" size="30" id="obsdesk_company_name" name="obsdesk_company_name"><?php echo form_error('obsdesk_company_name'); ?>
                                <input type="hidden" value="<?php echo $this->company->company_name; ?>" id="company_name" name="company_name">
                            </td>
                          </tr>
                          <tr>
                            <td width="170" class="textlabel"><span style="padding-left:20px"><?php echo _('Desk Logo'); ?></span></td>
                            <td>
                                <?php if( isset($this->company->obsdesk_logo) && $this->company->obsdesk_logo != '' ) { ?> 
                                <img src="<?php echo base_url(); ?>assets/company-logos/<?php echo $this->company->obsdesk_logo; ?>" height="110" />
                                <input type="hidden" value="<?php echo $this->company->obsdesk_logo; ?>" class="text medium" size="30" id="old_obsdesk_logo" name="old_obsdesk_logo">
                                <?php } else { ?>
                                <strong><?php echo _('No Logo Set !'); ?></strong>
                                <?php } ?>
                                <br /><br />
                                <?php echo ('Upload to change'); ?>&nbsp;:&nbsp;
                                <input type="file" class="text medium" size="30" id="obsdesk_logo" name="obsdesk_logo">
							    <?php echo form_error('obsdesk_logo'); ?>
                            </td>
                          </tr>
                          <tr>
                            <td width="170" class="textlabel"><span style="padding-left:20px"><?php echo _('Desk Footer Text'); ?></span></td>
                            <td>
                                <textarea name="obsdesk_footer_text" id="obsdesk_footer_text" style="width:70%;height: 200px;"><?php if( isset($this->company->obsdesk_footer_text) && $this->company->obsdesk_footer_text != '' ) { echo $this->company->obsdesk_footer_text; } ?></textarea>
                            </td>
                          </tr>
                          
                          <tr>
                            <td width="170" class="textlabel"><span style="padding-left:20px"><?php echo _('Apply this style'); ?></span></td>
                            <td>
                                <input type="checkbox" name="apply_css" id="apply_css" class="checkbox" value="1" <?php if($desk_settings['apply_css'] == 1){ echo 'checked="checked"'; } ?>>
                                &nbsp;&nbsp;&nbsp;
                                <a id="help13" href="javascript:;" title="<?php echo _('If you check this option, then following style will apply on front-end.'); ?>">
                                  <img width="16" height="16" src="<?php echo base_url(); ?>assets/cp/images/help.png">
                                </a>
                            </td>
                          </tr>
                          
                          <tr>
							<td width="20%" class="textlabel"><?php if($type_id == 14):echo _('Color of bar');else:echo _('Header bg color');endif;?></td>
							<td style="padding-top:10px; vertical-align:bottom">
								<input type="text" class="text short colors" id="head_bg_color_1" name="head_bg_color_1" value="<?php if($desk_section_design && $desk_section_design['0']->head_bg_color_1){ echo $desk_section_design['0']->head_bg_color_1; }else{ echo '#'.$head_bg_color_1; } ?>" style="height: 30px;">
								<span style="margin: 0px 50px; font-weight: bold; <?php if($type_id == 14):?>display:none;<?php endif;?>"><?php echo _("Text Color");?></span>
								<input type="text" class="text short <?php if($type_id != 14){?>colors<?php }?>" id="head_text_color_1" name="head_text_color_1" <?php if($type_id == 14):?> style="display:none; "<?php endif;?> value="<?php if($desk_section_design && $desk_section_design['0']->head_text_color_1){ echo $desk_section_design['0']->head_text_color_1; }else{ echo '#'.$head_text_color_1; } ?>" style="height: 30px;">
							</td>
						  </tr>
						  <tr <?php if($type_id == 14):?> style="display:none" <?php endif;?>>
							<td width="20%" class="textlabel"><?php echo _('Header bg color 2')?></td>
							<td style="padding-top:10px; vertical-align:bottom">
								<input type="text" class="text short colors" id="head_bg_color_2" name="head_bg_color_2" value="<?php if($desk_section_design && $desk_section_design['0']->head_bg_color_2){ echo $desk_section_design['0']->head_bg_color_2; }else{ echo '#'.$head_bg_color_2; } ?>" style="height: 30px;">
								<span style="margin: 0px 50px; font-weight: bold;"><?php echo _("Text Color");?></span>
								<input type="text" class="text short colors" id="head_text_color_2" name="head_text_color_2" value="<?php if($desk_section_design && $desk_section_design['0']->head_text_color_2){ echo $desk_section_design['0']->head_text_color_2; }else{ echo '#'.$head_text_color_2; } ?>" style="height: 30px;">
							</td>
						  </tr>
						  <tr <?php if($type_id == 14):?> style="display:none" <?php endif;?>>
							<td width="20%" class="textlabel"><?php echo _('Bg colors of buttons')?><br>(<?php echo _("Help,Search,Bestellen");?>)</td>
							<td style="padding-top:10px; vertical-align:bottom">
								<input type="text" class="text short colors" id="button_bg_color_1" name="button_bg_color_1" value="<?php if($desk_section_design && $desk_section_design['0']->button_bg_color_1){ echo $desk_section_design['0']->button_bg_color_1; }else{ echo '#'.$button_bg_color_1; } ?>" style="height: 30px;">
								<span style="margin: 0px 50px; font-weight: bold;"><?php echo _("Text Color");?></span>
								<input type="text" class="text short colors" id="button_text_color_1" name="button_text_color_1" value="<?php if($desk_section_design && $desk_section_design['0']->button_text_color_1){ echo $desk_section_design['0']->button_text_color_1; }else{ echo '#'.$button_text_color_1; } ?>" style="height: 30px;">
							</td>
						  </tr>
						  <tr <?php if($type_id == 14):?> style="display:none" <?php endif;?>>
							<td width="20%" class="textlabel"><?php echo _('Bg colors of buttons')?><br>(<?php echo _("Checkout,Shopping-cart");?>)</td>
							<td style="padding-top:10px; vertical-align:bottom">
								<input type="text" class="text short colors" id="button_bg_color_2" name="button_bg_color_2" value="<?php if($desk_section_design && $desk_section_design['0']->button_bg_color_2){ echo $desk_section_design['0']->button_bg_color_2; }else{ echo '#'.$button_bg_color_2; } ?>" style="height: 30px;">
								<span style="margin: 0px 50px; font-weight: bold;"><?php echo _("Text Color");?></span>
								<input type="text" class="text short colors" id="button_text_color_2" name="button_text_color_2" value="<?php if($desk_section_design && $desk_section_design['0']->button_text_color_2){ echo $desk_section_design['0']->button_text_color_2; }else{ echo '#'.$button_text_color_2; } ?>" style="height: 30px;">
							</td>
						  </tr>
						  <tr <?php if($type_id == 14):?> style="display:none" <?php endif;?>>
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
                        function check_deskTitle(){
                               	var x = document.forms["frm_obsdesk_settings"]["obsdesk_company_name"].value;
                                                                       
                               	if (x == null || x == "") {
                               		alert("<?php echo _("Company name should not be empty")?>");
                                            return true;
                                }
                        }
                       </script>
                    </form>
                </div>
            </div>
            <?php }?>
            <?php if($this->session->userdata('menu_type') == 'fooddesk_light'){?>
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
                          <tr>
                            <td width="170" class="textlabel"><span style="padding-left:20px"><?php echo _('Allergenkaart Logo'); ?></span></td>
                            <td>
                                <?php if( isset($this->company->obsdesk_logo) && $this->company->obsdesk_logo != '' ) { ?> 
                                <img src="<?php echo base_url(); ?>assets/company-logos/<?php echo $this->company->obsdesk_logo; ?>" height="110" />
                                <input type="hidden" value="<?php echo $this->company->obsdesk_logo; ?>" class="text medium" size="30" id="old_obsdesk_logo" name="old_obsdesk_logo">
                                <?php } else { ?>
                                <strong><?php echo _('No Logo Set !'); ?></strong>
                                <?php } ?>
                                <br /><br />
                                <?php echo ('Upload to change'); ?>&nbsp;:&nbsp;
                                <input type="file" class="text medium" size="30" id="obsdesk_logo" name="obsdesk_logo">
							    <?php echo form_error('obsdesk_logo'); ?>
                            </td>
                          </tr>
                          <tr style="display:none">
                            <td width="170" class="textlabel"><span style="padding-left:20px"><?php echo _('Desk Footer Text'); ?></span></td>
                            <td>
                                <textarea name="obsdesk_footer_text" id="obsdesk_footer_text" style="width:70%;height: 200px;"><?php if( isset($this->company->obsdesk_footer_text) && $this->company->obsdesk_footer_text != '' ) { echo $this->company->obsdesk_footer_text; } ?></textarea>
                            </td>
                          </tr>
                          
                          <tr>
                            <td width="170" class="textlabel"><span style="padding-left:20px"><?php echo _('Apply this style'); ?></span></td>
                            <td>
                                <input type="checkbox" name="apply_css" id="apply_css" class="checkbox" value="1" <?php if($desk_settings['apply_css'] == 1){ echo 'checked="checked"'; } ?>>
                                &nbsp;&nbsp;&nbsp;
                                <a id="help13" href="javascript:;" title="<?php echo _('If you check this option, then following style will apply on front-end.'); ?>">
                                  <img width="16" height="16" src="<?php echo base_url(); ?>assets/cp/images/help.png">
                                </a>
                            </td>
                          </tr>
                          
                          <tr>
							<td width="170" class="textlabel"><span style="padding-left:20px"><?php echo _('Sidebar Color');?></span></td>
							<td style="padding-top:10px; vertical-align:bottom">
								<input type="text" class="text short colors" id="head_bg_color_1" name="head_bg_color_1" value="<?php if($desk_section_design && $desk_section_design['0']->head_bg_color_1){ echo $desk_section_design['0']->head_bg_color_1; }else{ echo '#252525'; } ?>" style="height: 30px;">
								<span style="margin: 0px 50px; font-weight: bold;"><?php echo _("Text Color");?></span>
								<input type="text" class="text short colors" id="head_text_color_1" name="head_text_color_1" <?php if($type_id == 14):?> style="display:none;"<?php endif;?> value="<?php if($desk_section_design && $desk_section_design['0']->head_text_color_1){ echo $desk_section_design['0']->head_text_color_1; }else{ echo '#878787'; } ?>" style="height: 30px;">
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
                               <input type="hidden" value="look_n_feel" id="action" name="action">
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
		//alert("sjdhkj");
    	 $(".mceEditor").each(function(){
    		 alert($(this).css('display'));
    		 if($(this).css('display') == 'none'){
        		 
    			 $(this).remove();
             }
         });
    	 
     });
     </script>
</div>