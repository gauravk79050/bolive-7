
<div style="padding: 0px; display: block;" class="inside">
 <div class="table">
<form action="<?php echo base_url()?>cp/settings/othersettings" enctype="multipart/form-data" method="post" id="frm_othersettings" name="frm_othersettings">

            <input type="hidden" value="settings" name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW">
            <br>
            <table id="other_tab" cellspacing="0" class="override">
              <tbody>
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <?php if( $this->company_role == 'master' || $this->company_role == 'super' ) { ?>
                <tr>
                  <td width="22%"  valign="top" class="textlabel"><?php echo _('Add extra field in popup')?><br />(<?php echo _('above remark');?>)</td>
                  <td style="vertical-align:top">
          <div class="other_setting_div">
            <p>
              <span class="left">
                <input type="checkbox" value="1" class="checkbox" id="extra_field_popup" name="extra_field_popup" <?php if($general_settings && $general_settings[0]->extra_field_popup):?>checked="checked"<?php endif;?>>
              </span>
                        <span class="left">
                          <?php echo _("Name of the field")?>
                          <input type="text" value="<?php if($general_settings && isset($general_settings[0]->extra_field_popup_name)): echo $general_settings[0]->extra_field_popup_name; endif;?>" id="extra_field_popup_name" name="extra_field_popup_name" class="text medium">
                        </span>
              <div class="clear"></div>
            </p>
          </div>
                  </td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <?php }?>
                <?php if( $this->company_role == 'master' || $this->company_role == 'sub' ) { ?>
                <tr >
                  <td width="22%"  valign="top" class="textlabel"><?php echo _('Discount Per Amount')?></td>
                  <td style="vertical-align:middle">
          <div id="discount_per_amount_div">
            <p>
              <span class="left">
                <input type="checkbox" value="1" class="checkbox" id="disc_per_amount" name="disc_per_amount" <?php if($general_settings && $general_settings[0]->disc_per_amount):?>checked="checked"<?php endif;?>>
              </span>
                        <span class="right">
                          <?php echo _("If total amount is more than")?>
                          <input type="text" value="<?php if($general_settings && isset($general_settings[0]->disc_after_amount)): echo $general_settings[0]->disc_after_amount; endif;?>" id="disc_after_amount" name="disc_after_amount" class="text veryshort"> &euro; <?php echo _("add a dicount off")?>
                          <input type="text" value="<?php if($general_settings && isset($general_settings[0]->disc_percent)): echo $general_settings[0]->disc_percent; endif;?>" id="disc_percent" name="disc_percent" class="text veryshort" onKeyup="check_disc('percent',this.value);"> %
                        </span>
              <div class="clear"></div>
            </p>
            <p>
              <?php echo _("or");?> <input type="text" value="<?php if($general_settings && isset($general_settings[0]->disc_price)): echo $general_settings[0]->disc_price; endif;?>" id="disc_price" name="disc_price" class="text veryshort" onKeyup="check_disc('price',this.value);"> &euro;
            </p>
          </div>
                  </td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <?php if($activated_addons_status=='1'){?>
                <tr >
                  <td width="22%" class="textlabel"><?php echo _('Activate discount cards for clients')?></td>
                  <td style="padding-top:10px; vertical-align:middle">
                     <input type="checkbox" value="1" class="checkbox" id="activate_dicount" name="activate_dicount" <?php if($general_settings && $general_settings[0]->activate_discount_card):?>checked="checked"<?php endif;?>>                    
                  </td>
                </tr>               
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td style="padding-left:20px" class="textlabel"><?php echo _('Text that has to be appear in registration form')?></td>
                  <td><textarea style="width: 550px; height: 200px" type="textarea" id="discountcard_text" name="discountcard_text"><?php  if($general_settings): echo $general_settings[0]->discount_card_message;endif;?></textarea></td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <?php }?>
                
                <?php if($activated_addons_status=='0'){?>
                <tr >
                  <td width="22%" class="textlabel" style="color:#CCC;"><?php echo _('Activate discount cards for clients')?>&nbsp;<a href="<?php echo base_url()?>cp/cdashboard/addons"><?php echo _('(addon not activated)'); ?></a></td>
                  <td style="padding-top:10px; vertical-align:middle">
                     <input type="checkbox" value="1" class="checkbox" id="activate_discount" name="activate_discount" <?php if($general_settings && $general_settings[0]->activate_discount_card):?>checked="checked"<?php endif;?> disabled />                    
                  </td>
                </tr>               
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <?php }?>
                <?php }?>
                <?php if( $this->company_role == 'master' || $this->company_role == 'super' ) { ?>
                <tr>
                  <td width="20%" class="textlabel"><?php echo _('Hide availability feature in cp/popup')?></td>
                  <td style="padding-top:10px; vertical-align:middle">
                     <input type="checkbox" value="1" class="checkbox" id="hide_availability" name="hide_availability" <?php if($general_settings && $general_settings[0]->hide_availability):?>checked="checked"<?php endif;?>>
                    &nbsp;&nbsp;&nbsp;
                    <a title="<?php echo _('Check, if you want to hide the product availability feature, in CP as well as on OBS-Shop.')?>" href="#" class="help"><img width="16" height="16" src="<?php echo base_url()?>assets/cp/images/help.png"></a>
                  </td>
                </tr>
        <?php } ?>
        <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <?php if( $this->company_role == 'master' || $this->company_role == 'sub' ) { ?>
                <tr>
                  <td style="padding-left:20px" class="textlabel"><?php echo _('Calendar you want to connected with')?></td>
                  <td>
                    <select id="calendar_country" name="calendar_country" style="margin-left: 0px;">
                      <option value=""><?php echo _('--Select--');?></option>
                      <option value="calendar_belgium" <?php if($general_settings && $general_settings[0]->calendar_country == 'calendar_belgium'):?>selected="selected"<?php endif;?> ><?php echo _('Belgium');?></option>
                      <option value="calendar_netherland" <?php if($general_settings && $general_settings[0]->calendar_country == 'calendar_netherland'):?>selected="selected"<?php endif;?> ><?php echo _('Netherlands');?></option>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td class="textlabel"><?php echo _('Order timing Information')?></td>
                  <td><textarea style="width: 70%; height: 200px" type="textarea" id="order_timing_info" name="order_timing_info"><?php if($general_settings): echo $general_settings[0]->order_timing_info;endif;?></textarea>
                  </td>
                </tr>
                
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                
                <?php if($general_settings && $general_settings[0]->activate_labeler){ ?>
                <tr>
                  <td style="padding-left:20px" class="textlabel"><?php echo _('Labeler')?></td>
                  <td>
                    <div style="float: left;">
                      <select id="labeler_print_type" name="labeler_print_type" style="margin-left: 0px;" onchange="$('#labeler_type').toggle();">
                        <option value="manual" <?php if($general_settings && $general_settings[0]->labeler_print_type == 'manual'):?>selected="selected"<?php endif;?> ><?php echo _('Print manually');?></option>
                        <option value="automatic" <?php if($general_settings && $general_settings[0]->labeler_print_type == 'automatic'):?>selected="selected"<?php endif;?> ><?php echo _('Print automatically');?></option>
                      </select>
                    </div>
                    <div style="float: left;">
                      <select id="labeler_type" name="labeler_type" <?php if($general_settings[0]->labeler_print_type == 'manual'){?>style="display:none;"<?php }?>>
                        <option value="all" <?php if($general_settings && $general_settings[0]->labeler_type == 'all'):?>selected="selected"<?php endif;?> ><?php echo _('Print all');?></option>
                        <option value="per_order" <?php if($general_settings && $general_settings[0]->labeler_type == 'per_order'):?>selected="selected"<?php endif;?> ><?php echo _('Print order in general');?></option>
                        <option value="per_ordered_product" <?php if($general_settings && $general_settings[0]->labeler_type == 'per_ordered_product'):?>selected="selected"<?php endif;?> ><?php echo _('Print every ordered product seperately');?></option>
                      </select>
                    </div>
                    <div style="clear: both;"></div>
                    <div style="float: left;margin: 15px 0 0 0;">
                      <label>Print Bestellingen</label>
                    </div>
                    <div style="float: left;margin: 15px 0 0 15px;">
                      <select id="printer_orders" name="printer_orders">
                        <option value="0" <?php if($general_settings && $general_settings[0]->printer_orders == 0):?>selected="selected"<?php endif;?> >Alle bestellingen</option>
                        <option value="1" <?php if($general_settings && $general_settings[0]->printer_orders == 1):?>selected="selected"<?php endif;?>>Enkel de bestellingen van die dag</option>
                      </select>
                    </div>
                    <div style="clear: both;"></div>
                  </td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <?php }?>
                <?php }?>
                 <?php if( $this->company_role == 'master' || $this->company_role == 'super' ) { ?>
                <tr>
                  <td style="padding-left:20px" class="textlabel"><?php echo _('Show searchbox')?></td>
                  <td>
                    <input type="checkbox" value="1" class="checkbox" id="show_searchbox" name="show_searchbox" <?php if($general_settings && $general_settings[0]->show_searchbox):?>checked="checked"<?php endif;?>>
                    &nbsp;&nbsp;&nbsp;
                    <a title="<?php echo _('Check, if you want to search box on frontend')?>" href="#" class="help"><img width="16" height="16" src="<?php echo base_url()?>assets/cp/images/help.png"></a>
                  </td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                
                <tr>
                  <td style="padding-left:20px" class="textlabel"><?php echo _('Activate Suggetions at checkout')?></td>
                  <td>
                    <input type="checkbox" value="1" class="checkbox" id="activate_suggetions" name="activate_suggetions" <?php if($general_settings && $general_settings[0]->activate_suggetions):?>checked="checked"<?php endif;?>>
                    &nbsp;&nbsp;&nbsp;
                    <a title="<?php echo _('Check, if you want to show suggetions at checkout')?>" href="#" class="help"><img width="16" height="16" src="<?php echo base_url()?>assets/cp/images/help.png"></a>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <b><?php echo _("and show me");?></b>
                    <select id="num_of_suggetions" name="num_of_suggetions" style="display: inline;">
                      <option value="4" <?php if($general_settings && $general_settings[0]->num_of_suggetions == 4){?>selected="selected"<?php }?>>4</option>
                      <option value="5" <?php if($general_settings && $general_settings[0]->num_of_suggetions == 5){?>selected="selected"<?php }?>>5</option>
                      <option value="6" <?php if($general_settings && $general_settings[0]->num_of_suggetions == 6){?>selected="selected"<?php }?>>6</option>
                      <option value="7" <?php if($general_settings && $general_settings[0]->num_of_suggetions == 7){?>selected="selected"<?php }?>>7</option>
                      <option value="8" <?php if($general_settings && $general_settings[0]->num_of_suggetions == 8){?>selected="selected"<?php }?>>8</option>
                      <option value="9" <?php if($general_settings && $general_settings[0]->num_of_suggetions == 9){?>selected="selected"<?php }?>>9</option>
                      <option value="10" <?php if($general_settings && $general_settings[0]->num_of_suggetions == 10){?>selected="selected"<?php }?>>10</option>
                      <option value="15" <?php if($general_settings && $general_settings[0]->num_of_suggetions == 15){?>selected="selected"<?php }?>>15</option>
                      <option value="20" <?php if($general_settings && $general_settings[0]->num_of_suggetions == 20){?>selected="selected"<?php }?>>20</option>
                      <option value="25" <?php if($general_settings && $general_settings[0]->num_of_suggetions == 25){?>selected="selected"<?php }?>>25</option>
                      <option value="30" <?php if($general_settings && $general_settings[0]->num_of_suggetions == 30){?>selected="selected"<?php }?>>30</option>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                
                <tr>
                  <td style="padding-left:20px" class="textlabel"><?php echo _('Hide REGISTER link from Shop')?></td>
                  <td>
                    <input type="checkbox" value="1" class="checkbox" id="hide_register" name="hide_register" <?php if($general_settings && $general_settings[0]->hide_register):?>checked="checked"<?php endif;?>>
                    &nbsp;&nbsp;&nbsp;
                    <a title="<?php echo _('Check, if you want to hide register link from frontend')?>" href="#" class="help"><img width="16" height="16" src="<?php echo base_url()?>assets/cp/images/help.png"></a>
                  </td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td style="padding-left:20px" class="textlabel"><?php echo _('Receive notification mail when product has been requested to producer');?></td>
                  <td>
                    <p style="display:inline;float:left;"><input type="radio" name="notify_producer" value="1" <?php if($general_settings && $general_settings[0]->notify_req_producer == '1'):?>checked="checked"<?php endif;?>></p><b style="display:inline;float:left;margin-left;margin-left:2px;"><?php echo _('yes');?></b>
            <p style="display:inline;float:left;margin-left:10px"><input type="radio" name="notify_producer" value="0" <?php if($general_settings && $general_settings[0]->notify_req_producer == '0'):?>checked="checked"<?php endif;?>></p><b style="display:inline;float:left;margin-left:2px;"><?php echo _('no');?></b>
                  </td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td style="padding-left:20px" class="textlabel"><?php echo _('Receive notification mail when product has assigned');?></td>
                  <td>
                    <p style="display:inline;float:left;"><input type="radio" name="notify_pro_assign" value="1" <?php if($general_settings && $general_settings[0]->notify_prod_assign == '1'):?>checked="checked"<?php endif;?>></p><b style="display:inline;float:left;margin-left;margin-left:2px;"><?php echo _('yes');?></b>
            <p style="display:inline;float:left;margin-left:10px"><input type="radio" name="notify_pro_assign" value="0" <?php if($general_settings && $general_settings[0]->notify_prod_assign == '0'):?>checked="checked"<?php endif;?>></p><b style="display:inline;float:left;margin-left:2px;"><?php echo _('no');?></b>
                  </td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td style="padding-left:20px" class="textlabel"><?php echo _('Preference for E-nbr');?></td>
                  <td>
                    <p style="display:inline;float:left;"><input type="radio" name="enbr_status" value="1" <?php if($general_settings && $general_settings[0]->enbr_status == '1'):?>checked="checked"<?php endif;?>></p><b style="display:inline;float:left;margin-left;margin-left:2px;"><?php echo _('E-nbr values');?></b>
            <p style="display:inline;float:left;margin-left:10px"><input type="radio" name="enbr_status" value="2" <?php if($general_settings && $general_settings[0]->enbr_status == '2'):?>checked="checked"<?php endif;?>></p><b style="display:inline;float:left;margin-left:2px;"><?php echo _('E-nbr names');?></b>
            <input type="hidden" name="prev_enbr_status" value="<?php echo $general_settings[0]->enbr_status;?>">
                  </td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                 <tr>
                  <td style="padding-left:20px" class="textlabel"><?php echo _('Hide products in webshop when not fully fixed');?></td>
                  <td>
                    <p style="display:inline;float:left;"><input type="radio" name="display_fixed" value="1" <?php if($general_settings && $general_settings[0]->display_fixed == '1'):?>checked="checked"<?php endif;?>></p><b style="display:inline;float:left;margin-left;margin-left:2px;"><?php echo _('yes');?></b>
            <p style="display:inline;float:left;margin-left:10px"><input type="radio" name="display_fixed" value="0" <?php if($general_settings && $general_settings[0]->display_fixed == '0'):?>checked="checked"<?php endif;?>></p><b style="display:inline;float:left;margin-left:2px;"><?php echo _('no');?></b>
                  </td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                   <td class="textlabel"><?php echo _('List/thumbview')?></td>
                   <td>
                    <div style="float: left;">
                        <select id="main_drop" name="list_drop" onchange="hide_option()" style="margin-left: 0px;">
                  <option <?php if($general_settings && $general_settings[0]->shop_view == '0'):?>selected="selected"<?php endif;?> value="0"><?php echo _("Both");?></option>
                    <option <?php if($general_settings && $general_settings[0]->shop_view == '1'):?>selected="selected"<?php endif;?> value="1"><?php echo _("Listview only");?></option>
                    <option <?php if($general_settings && $general_settings[0]->shop_view == '2'):?>selected="selected"<?php endif;?> value="2"><?php echo _("Thumbview only");?></option>
              </select>
            </div>
            <div style="float: left;">
                        <select id="default_drop"  name="default_list_drop" style="display: none;style="margin-left: 0px;"">
                  <option <?php if($general_settings && $general_settings[0]->shop_view_default == '1'):?>selected="selected"<?php endif;?>value="1"><?php echo _("Listview");?></option>
                  <option <?php if($general_settings && $general_settings[0]->shop_view_default == '2'):?>selected="selected"<?php endif;?>value="2"><?php echo _("Thumbview");?></option>
              </select>
            </div>
            <div style="clear: both;"></div>
          </td>
        </tr>
        <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
        <tr>
                   <td class="textlabel"><?php echo _('Amount of rows(Pagination)')?></td>
                   <td>
                    <div style="float: left;">
                        <select  name="list_pages_drop" onchange="list_pages()" style="margin-left: 0px;">
                  <option <?php if($general_settings && $general_settings[0]->amt_row_page == '1'):?>selected="selected"<?php endif;?>  value="1"><?php echo _("1");?></option>
                    <option <?php if($general_settings && $general_settings[0]->amt_row_page == '2'):?>selected="selected"<?php endif;?> value="2"><?php echo _("2");?></option>
                    <option <?php if($general_settings && $general_settings[0]->amt_row_page == '3'):?>selected="selected"<?php endif;?> value="3"><?php echo _("3");?></option>
                    <option <?php if($general_settings && $general_settings[0]->amt_row_page == '4'):?>selected="selected"<?php endif;?> value="4"><?php echo _("4");?></option>
                    <option <?php if($general_settings && $general_settings[0]->amt_row_page == '5'):?>selected="selected"<?php endif;?> value="5"><?php echo _("5");?></option>
                    <option <?php if($general_settings && $general_settings[0]->amt_row_page == '6'):?>selected="selected"<?php endif;?> value="6"><?php echo _("6");?></option>
                    <option <?php if($general_settings && $general_settings[0]->amt_row_page == '7'):?>selected="selected"<?php endif;?> value="7"><?php echo _("7");?></option>
                    <option <?php if($general_settings && $general_settings[0]->amt_row_page == '8'):?>selected="selected"<?php endif;?> value="8"><?php echo _("8");?></option>
                    <option <?php if($general_settings && $general_settings[0]->amt_row_page == '9'):?>selected="selected"<?php endif;?> value="9"><?php echo _("9");?></option>
                    <option <?php if($general_settings && $general_settings[0]->amt_row_page == '10'):?>selected="selected"<?php endif;?> value="10"><?php echo _("10");?></option>
              </select>
            </div>
            <div style="clear: both;"></div>
          </td>
        </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                 <tr>
                    <td class="textlabel"><?php echo _('Allow upload big images')?></td>
                  <td>
                    <p style="display:inline;float:left;"><input type="radio" name="biggest_image" value="1" <?php if($general_settings && $general_settings[0]->biggest_image == 1):?>checked="checked"<?php endif;?>></p><b style="display:inline;float:left;margin-left;margin-left:2px;"><?php echo _('yes');?></b>
            <p style="display:inline;float:left;margin-left:10px"><input type="radio" name="biggest_image" value="0" <?php if($general_settings && $general_settings[0]->biggest_image == 0):?>checked="checked"<?php endif;?>></p><b style="display:inline;float:left;margin-left:2px;"><?php echo _('no');?></b>
                  </td>
                 </tr>
                 <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <?php if($this->company->ac_type_id == 4 || $this->company->ac_type_id == 5 || $this->company->ac_type_id == 6){?>
                <!-- <tr>
                    <td class="textlabel"><?php echo _('Show sheet in infodesk')?></td>
                  <td>
                    <p style="display:inline;float:left;"><input type="radio" name="sheet_in_desk" value="1" <?php if($sheet_in_desk && $sheet_in_desk[0]->show_sheet == 1):?>checked="checked"<?php endif;?>></p><b style="display:inline;float:left;margin-left:2px;"><?php echo _('yes');?></b>
            <p style="display:inline;float:left;margin-left:10px"><input type="radio" name="sheet_in_desk" value="0" <?php if($sheet_in_desk && $sheet_in_desk[0]->show_sheet == 0):?>checked="checked"<?php endif;?>></p><b style="display:inline;float:left;margin-left:2px;"><?php echo _('no');?></b>
                  </td>
                 </tr>
                 <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr> -->
                <?php }?>
                 <tr>
                    <td class="textlabel"><?php echo _('PROMOCODE')?> &nbsp;&nbsp;&nbsp;<a title="<?php echo _('it represents promocode which valid for given time period.')?>" href="#" class="help"><img width="16" height="16" src="<?php echo base_url();?>assets/cp/images/help.png"></a></td>
                    <td>
                      <input type="checkbox" value="1" class="checkbox" id="promocode_check" name="promocode_check"<?php if($promocode_settings && isset($promocode_settings[0]->promocode1)){if($promocode_settings[0]->promocode1 == 1){?>checked="checked"<?php }}?>>
                      &nbsp;&nbsp;&nbsp;<input type="text" value="<?php if($promocode_settings && isset($promocode_settings[0]->promocode1_text)): echo $promocode_settings[0]->promocode1_text; endif;?>" id="promocode_text" name="promocode_text" class="text short">
                  </td>
                </tr>
                <?php $promocode_val_array1 = explode("#",$promocode_settings[0]->promocode1_start);?>
			    <?php $promocode_end_val_array1 = explode("#",$promocode_settings[0]->promocode1_end);?>
                <tr>
                  <td class="textlabel">
                  </td>
                  <td>
                    <input type="text" value="<?php if($promocode_settings && isset($promocode_settings[0]->promocode1_percent)): echo $promocode_settings[0]->promocode1_percent; endif;?>" id="promocode_percent" name="promocode_percent" class="text veryshort" onkeyup="check_promo('percent',this.value);">
                    &nbsp;%&nbsp;
                    <?php echo _("or");?>
                    &nbsp;&nbsp;
                    <input type="text" value="<?php if($promocode_settings && isset($promocode_settings[0]->promocode1_price)): echo $promocode_settings[0]->promocode1_price; endif;?>" id="procode_price" name="promocode_price" class="text veryshort" onkeyup="check_promo('price',this.value);">
                    &nbsp;€&nbsp;&nbsp;
                    <?php echo _("From");?>&nbsp;&nbsp;
                    <input type="text" readonly="readonly" id="start_date" name="start_date" class="text short" value="<?php if($general_settings && ($general_settings[0]->promocode_start != '0000-00-00')): echo date('d/m/Y',strtotime($general_settings[0]->promocode_start)); endif;?>">&nbsp;&nbsp;
                    <input type="button" id="button1" name="button1" onclick="displayCalendar(document.frm_othersettings.start_date,'dd/mm/yyyy',this)" value="...">
                    &nbsp;&nbsp;
                    <?php echo _("To");?>&nbsp;&nbsp;
                    <input type="text" readonly="readonly" id="end_date" name="end_date" class="text short" value="<?php if($general_settings && ($general_settings[0]->promocode_end != '0000-00-00')): echo date('d/m/Y',strtotime($general_settings[0]->promocode_end)); endif;?>">
                    <input type="button" id="button2" name="button2" onclick="displayCalendar(document.frm_othersettings.end_date,'dd/mm/yyyy',this)" value="...">
                  </td>
                </tr>
                
                
                <tr>
                    <td class="textlabel"><?php echo _('PROMOCODE 2')?> &nbsp;&nbsp;&nbsp;<a title="<?php echo _('it represents promocode which valid for given time period.')?>" href="#" class="help"><img width="16" height="16" src="<?php echo base_url();?>assets/cp/images/help.png"></a></td>
                    <td>
                      <input type="checkbox" value="1" class="checkbox" id="promocode2_check" name="promocode2_check"<?php if(isset($promocode_settings[0]->promocode2)){if($promocode_settings[0]->promocode2 == 1){?>checked="checked"<?php }}?>>
                      &nbsp;&nbsp;&nbsp;<input type="text" value="<?php if(isset($promocode_settings[0]->promocode2_text)): echo $promocode_settings[0]->promocode2_text; endif;?>" id="promocode2_text" name="promocode2_text" class="text short">
                  </td>
                </tr>
                
                <tr>
                  <td class="textlabel">
                  </td>
                  <td>
                  <table class="promocode_set_up">
	                  <tbody>
	                   <?php $promocode_val_array = explode("#",$promocode_settings[0]->promocode2_start);?>
			                     <?php $promocode_end_val_array = explode("#",$promocode_settings[0]->promocode2_end);?>
			                     <?php $total_promocode_val_array =count($promocode_val_array);?>
			                     <?php for ($i=0;$i<$total_promocode_val_array;$i++){?>
				                 	 <tr>
					                  <?php if ($i == 0){?>
					                    <td style="width: 184px;">
						                    <input type="text" value="<?php if(isset($promocode_settings[0]->promocode2_percent)): echo $promocode_settings[0]->promocode2_percent; endif;?>" id="promocode2_percent" name="promocode2_percent" class="text veryshort" onkeyup="check_promo2('percent',this.value);" style="width: 24%;">
						                    &nbsp;%&nbsp;
						                    <?php echo _("or");?>
						                    &nbsp;&nbsp;
						                    <input type="text" value="<?php if(isset($promocode_settings[0]->promocode2_price)): echo $promocode_settings[0]->promocode2_price; endif;?>" id="promocode2_price" name="promocode2_price" class="text veryshort" onkeyup="check_promo2('price',this.value);" style="width: 24%;">
						                    &nbsp;€&nbsp;&nbsp;
						                      </td>
					                    <?php }else{?>
					                    <td></td>
					                    <?php }?>
					                
					                  	<td>   
						                    <?php echo _("From");?>&nbsp;&nbsp;
						                    <input type="text" style="width: 23%!important;" readonly="readonly" id="start_date_<?php echo $i;?>" name="promocode2_start_date[]" class="text short" value="<?php if( isset($promocode_settings) && $promocode_val_array[$i] != '0000-00-00' ): echo $promocode_val_array[$i]; endif;?>">&nbsp;&nbsp;
						                    <input type="button" name="button1" onclick="displayCalendar(document.frm_othersettings.start_date_<?php echo $i;?>,'dd/mm/yyyy',this)" value="...">
						                    &nbsp;&nbsp;
						                    <?php echo _("To");?>&nbsp;&nbsp;
						                    <input type="text" style="width: 23%!important;" readonly="readonly" id="end_date_<?php echo $i;?>" name="promocode2_end_date[]" class="text short" value="<?php if( isset($promocode_settings) && $promocode_end_val_array[$i] != '0000-00-00' ): echo $promocode_end_val_array[$i]; endif;?>">
						                    <input type="button" name="button2" onclick="displayCalendar(document.frm_othersettings.end_date_<?php echo $i;?>,'dd/mm/yyyy',this)" value="...">
						                  
						                 	<img width="18" border="0" style="vertical-align: middle;padding-left: 6px;" onClick="javascript:addNewpromorow(this)" src="<?php echo base_url(); ?>assets/cp/images/add.gif">
						                 	<img width="18" border="0" style="vertical-align: middle;padding-left: 6px;"  onClick="javascript:deletepromorow(this)" src="<?php echo base_url(); ?>assets/cp/images/delete.gif">
			                 	<?php }?>
			                 </td>
		                  </tr>
	                  </tbody>
	                  <input type="hidden" value="<?php if (isset($total_promocode_val_array)){echo $total_promocode_val_array; }else{?>0<?php }?>" name="total_promocode_val" id="total_promocode_val">
                  </table>
                  </td>
                </tr>
                <tr>
                  <td class="textlabel"><?php echo _('INTROCODE')?> &nbsp;&nbsp;&nbsp;<a title="<?php echo _('It represents coupon code when first time order is placed.')?>" href="#" class="help"><img width="16" height="16" src="<?php echo base_url();?>assets/cp/images/help.png"></a></td>
          		<td>
                      <input type="checkbox" value="1" class="checkbox" id="introcode_check" name="introcode_check"<?php if($general_settings && isset($general_settings[0]->introcode)){if($general_settings[0]->introcode == 1){?>checked="checked"<?php }}?>>
                      &nbsp;&nbsp;&nbsp;<input type="text" value="<?php if($general_settings && ($general_settings[0]->introcode_text)): echo $general_settings[0]->introcode_text; endif;?>" id="introcode_text" name="introcode_text" class="text short">
                  </td>
                </tr>
                
                <tr>
                  <td class="textlabel">
                  </td>
                  <td>
                    <input type="text" value="<?php if($general_settings && isset($general_settings[0]->introcode_percent)): echo $general_settings[0]->introcode_percent; endif;?>" id="introcode_percent" name="introcode_percent" class="text veryshort" onkeyup="check_intro('percent',this.value);">&nbsp;&nbsp;&nbsp;%&nbsp;&nbsp;&nbsp;<?php echo _("or");?>
                    &nbsp;&nbsp;&nbsp;
                    <input type="text" value="<?php if($general_settings && isset($general_settings[0]->introcode_price)): echo $general_settings[0]->introcode_price; endif;?>" id="introcode_price" name="introcode_price" class="text veryshort" onkeyup="check_intro('price',this.value);">&nbsp;&nbsp;&nbsp;€
                  </td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                 <tr>
                    <td class="textlabel"><?php echo _('Make Mobile No. Required for user')?></td>
                  <td>
                    <p style="display:inline;float:left;"><input type="radio" name="mobile_req" value="1" <?php if($general_settings && $general_settings[0]->mobile_req == 1):?>checked="checked"<?php endif;?>></p><b style="display:inline;float:left;margin-left;margin-left:2px;"><?php echo _('yes');?></b>
            <p style="display:inline;float:left;margin-left:10px"><input type="radio" name="mobile_req" value="0" <?php if($general_settings && $general_settings[0]->mobile_req == 0):?>checked="checked"<?php endif;?>></p><b style="display:inline;float:left;margin-left:2px;"><?php echo _('no');?></b>
                  </td>
                 </tr>
                 <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <!-- Added DEfault Image -->
                 <tr>
                 <td></td>
                  <td>
                   <?php
                    if($general_settings[0]->comp_default_image){?>
                     <img alt="infodesk default image" src="<?php echo base_url().'assets/cp/images/infodesk_default_image/'. $general_settings[0]->comp_default_image;?>" style="height:200px">
                   <?php }?>
                 </td>
                </tr>

                <tr>
                   <td class="textlabel"><?php echo _('Company default image')?>&nbsp;<a title="<?php echo _('Please upload a default infodesk image in jpg/gif/png format')?>" href="#" id="help-prod0"><img width="16" height="16" src="<?php echo base_url(); ?>assets/cp/images/help.png"></a>
                  
                   </td>
                   
                   <td style="padding-right:00px">
                   <div id="uploaded_image"></div>
                       <input type="hidden" id="x" name="x" />
                       <input type="hidden" id="y" name="y" />
                       <input type="hidden" id="w" name="w" />
                       <input type="hidden" id="h" name="h" />
                   <div>
                     <a href="javascript:;" class="thickboxed" attr_id="2" style="text-decoration: none;"><input type="button" name="upload_image" id="upload_image" value="<?php echo _("Upload Image Here");?>" /></a>
                     <?php 
                     if($general_settings[0]->comp_default_image){?>
                    <a id="delete_default_image" href="javascript:;"><?php  echo _('Remove'); ?></a>
                   <?php } ?>
                   </div>
                  </td>
                </tr>
                <!-- End Default Image -->
                
              <!--   <tr>
                    <td class="textlabel"><?php //echo _('Order ID default prefix')?> &nbsp;&nbsp;&nbsp;<a title="<?php //echo _('This string is added as prefix with Order ID.')?>" href="#" class="help"><img width="16" height="16" src="<?php echo base_url();?>assets/cp/images/help.png"></a></td>
                    <td>
                      <input type="text" value="<?php //if($general_settings && isset($general_settings[0]->order_id_prefix)): echo $general_settings[0]->order_id_prefix; endif;?>" id="order_id_prefix" name="order_id_prefix" class="text short">
                  </td>
                </tr> -->
            <!--     <tr>
                    <td class="textlabel"><?php //echo _('Show Stock')?></td>
                      <td>
                        <p style="display:inline;float:left;"><input type="radio" name="obs_stock_show" value="1" <?php //if($general_settings && $general_settings[0]->stock_show == 1):?>checked="checked"<?php //endif;?>></p><b style="display:inline;float:left;margin-left;margin-left:2px;"><?php //echo _('yes');?></b>
                        <p style="display:inline;float:left;margin-left:10px"><input type="radio" name="obs_stock_show" value="0" <?php //if($general_settings && $general_settings[0]->stock_show == 0):?>checked="checked"<?php //endif;?>></p><b style="display:inline;float:left;margin-left:2px;"><?php //echo _('no');?></b>
                      </td>
                </tr>
                <?php }?>
                <tr> -->
                  <td class="save_b" colspan="2"><input type="submit" value="<?php echo _('UPDATE');?>" class="submit" id="btn_update" name="btn_update">
                  </td>
                  <input type="hidden" value="edit_other_settings" id="act" name="act">
                </tr>
              </tbody>              
            </table>
          </form>
         <!--  <script language="JavaScript" type="text/javascript">
      var frmvalidator = new Validator("frm_othersettings");
      frmvalidator.EnableMsgsTogether();
      frmvalidator.addValidation("start_date","req","<?php echo _('Please enter start date')?>");
      frmvalidator.addValidation("end_date","req","<?php echo _('Please enter end date')?>");
     </script> -->
          <script type="text/javascript" language="javascript">
      function check_disc(type,value){
        if(type == "percent"){
          if(value != 0){
            jQuery("#disc_price").val('0');
          }
        }else if(type == "price"){
          if(value != 0){
            jQuery("#disc_percent").val('0');
          }
        }
      }

      function check_promo(type,value){
        if(type == "percent"){
          if(value != 0){
            jQuery("#procode_price").val('0');
          }
        }else if(type == "price"){
          if(value != 0){
            jQuery("#promocode_percent").val('0');
          }
        }
      }

      function check_promo2(type,value){
          if(type == "percent"){
            if(value != 0){
              jQuery("#promocode2_price").val('0');
            }
          }else if(type == "price"){
            if(value != 0){
              jQuery("#promocode2_percent").val('0');
            }
          }
        }

      function check_intro(type,value){
        if(type == "percent"){
          if(value != 0){
            jQuery("#introcode_price").val('0');
          }
        }else if(type == "price"){
          if(value != 0){
            jQuery("#introcode_percent").val('0');
          }
        }
      }
          
      var frmValidator = new Validator("frm_othersettings");
      frmValidator.EnableMsgsTogether();
      
      function validate_mess(result){
        if(result == true){
          var ok_msg = tinyMCE.get('discountcard_text').getContent();
          if(ok_msg == ""){
            alert("<?php echo _('please Enter Discount message')?>");
            return false;
          }         
          return true;
        }else{
          return false;
        }
      }
      
      tinyMCE.init({
          theme : "advanced",
          mode: "exact",
          elements : "discountcard_text_",
          readonly:1,
       //   theme_advanced_toolbar_location : "top",
          theme_advanced_toolbar_location : "top",
          theme_advanced_buttons1 : "bold,italic,underline,separator,"
          + "justifyleft,justifycenter,justifyright,justifyfull,"
          + "",
          theme_advanced_buttons2 : ""
          +"",
          theme_advanced_buttons3 : ""
      });
    
      $('.fadenext').click(function(){
        $(this).next('.fader').slideToggle("slow");
        $(this).next('.fader').css({'opacity':'1','display': 'block'});
        return false;
      }); 

		function addNewpromorow(obj){
			var length = $('#total_promocode_val').val();
			length = parseInt(length)+1;
			var html = '';
			html+='<tr>';
			html+='<td>';
			html+='</td>';
			html+='<td>';
			html+="<?php echo _("From");?>&nbsp;&nbsp;&nbsp;";
			html+='<input type="text" style="width: 23%!important;" readonly="readonly" id="start_date_'+length+'" name="promocode2_start_date[]" class="text short">&nbsp;&nbsp;';
			html+='<input type="button" name="button1" onclick="displayCalendar(document.frm_othersettings.start_date_'+length+',\'dd/mm/yyyy\',this)" value="...">';
			html+='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			html+="<?php echo _("To");?>&nbsp;&nbsp;&nbsp;";
			html+='<input type="text" style="width: 23%!important;" readonly="readonly" id="end_date_'+length+'" name="promocode2_end_date[]" class="text short">&nbsp;&nbsp;';
			html+='<input type="button" name="button2" onclick="displayCalendar(document.frm_othersettings.end_date_'+length+',\'dd/mm/yyyy\',this)" value="...">';
			html+='<img width="18" border="0" style="vertical-align: middle;padding-left: 6px;" onClick="javascript:addNewpromorow(this)" src="'+base_url+'assets/cp/images/add.gif">';
			html+='<img width="18" border="0" style="vertical-align: middle;padding-left: 6px;"  onClick="javascript:deletepromorow(this)" src="'+base_url+'assets/cp/images/delete.gif">';
			html+='</td>';
			html+='</tr>';
			$(obj).closest('tbody').append(html);
	        $('#total_promocode_val').val(length);
		}      

		function deletepromorow(obj){
			var length = $('#total_promocode_val').val();
			
			if(parseInt(length) == '1' ){
				
			}
			else
			{
				$(obj).closest('td').remove();
				length = parseInt(length)-1;
				$('#total_promocode_val').val(length);
			}
		}
    </script>
      </div>
  </div>
<script type="text/javascript">
var cropping = "<?php echo _('Cropping');?>";
</script>
<script src="<?php echo base_url();?>assets/cp/new_js/jquery.Jcrop.js?version=<?php echo version;?>"></script>
<script type="text/javascript">

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
          onSelect: updateCoords,
          setSelect: [ $('#x').val(), $('#y').val(), $('#w').val(), $('#h').val() ],
         
          aspectRatio: 1
          });
      },
    });
}

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
