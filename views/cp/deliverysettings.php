<div style="padding:0px 0px 0px 0px; display:none" class="inside">
        <div class="inside">
          <div class="box">
            <div class="table">
              <form action="<?php echo base_url()?>cp/settings/deliverysettings" enctype="multipart/form-data" method="post" id="frm_delivery_settings" name="frm_delivery_settings" onsubmit="return validateForm4()">
                <input type="hidden" value="settings" name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW"/>
                <table cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <td colspan="4" style="font-size:16px; font-weight:bold"><?php echo _('Manage State Details')?></td>
                    </tr>
                    <!-- <tr>
                      <td colspan="4" style="text-align:right"><a href="<?php echo base_url()?>cp/settings/areadetails_addedit"><?php echo _('ADD')?> </a></td>
                    </tr> -->
                    <tr>
                      <td colspan="1" style="text-align:left" class="textlabel" ><?php echo _("Delivery");?></td>
                      <td colspan="1" style="text-align:left">
                        <input type="radio" name="delivery_status" id="" value="1" <?php if(!empty($delivery_area_settings) && $delivery_area_settings['0']['delivery_status']){?>checked="checked"<?php }?> /> <?php echo _("Yes");?> &nbsp;&nbsp;
                        <input type="radio" name="delivery_status" id="" value="0" <?php if(!empty($delivery_area_settings) && !$delivery_area_settings['0']['delivery_status']){?>checked="checked"<?php }?> /> <?php echo _("No");?>
                      </td>
                      <td colspan="2" style="text-align:right">&nbsp;</td>
                    </tr>
                    <tr>
                      <td colspan="1" style="text-align:left" class="textlabel" ><?php echo _("Type");?></td>
                      <td colspan="1" style="text-align:left">
                        <input type="radio" name="type" id="" value="national" onclick="show_hide_areas();" <?php if(!empty($delivery_area_settings) && $delivery_area_settings['0']['type'] == 'national' ){?>checked="checked"<?php }?> /> <?php echo _("National/Locale");?>
                      </td>
                      <td colspan="2" style="text-align:left; color: grey;">
                        <input type="radio" name="type" id="" value="international" onclick="show_hide_areas();"  <?php if(!empty($delivery_area_settings) && $delivery_area_settings['0']['type'] == 'international' ){?>checked="checked"<?php }?> /> <?php echo _("International");?>
                      </td>
                    </tr>

                    <!-- >>>>>>>>>>>>>>>>>>>>>>>>> START: NATIONAL DELIEVRY <<<<<<<<<<<<<<<<<<<<<<<<<<<<< -->
                    <tr class="national_row">
                      <td width="20%" align="right">&nbsp;</td>
                      <td width="20%" style="text-align: center;" class="textlabel" ><?php echo _("Country");?></td>
                      <td width="20%" style="text-align: center;" class="textlabel" ><?php echo _("State");?></td>
                      <td width="40%" style="text-align: center;" class="textlabel" ><?php echo _("City");?> <img id="load_postcodes" src="<?php echo base_url();?>assets/cp/images/wait.gif" style="display: none;"/></td>
                    </tr>
                    <tr class="national_row">
                      <td width="20%" align="left">&nbsp;</td>
                      <td width="20%" style="text-align: center;">
                        <select id="country_names" name="country_names" onChange="change_state();">
                          <option value="21"><?php echo _("Belgium");?></option>
                          <option value="150"><?php echo _("Netherlands");?></option>
                        </select>
                      </td>
                      <td width="20%" style="text-align: center;">
                        <select id="state_belgium" name="state_belgium" class="c_states" onChange="get_postcode(this.value)">
                          <?php if(!empty($belgium_states)):?>
                          <option value="0"><?php echo _("--Select--");?></option>
                          <?php foreach($belgium_states as $state):?>
                          <option value="<?php echo $state['state_id'];?>"><?php echo $state['state_name'];?></option>
                          <?php endforeach;?>

                          <?php ;else:?>
                          <option value="0"><?php echo _("No State");?></option>
                          <?php endif;?>
                        </select>
                        <select id="state_netherlands" name="state_netherlands" class="c_states" onChange="get_postcode(this.value)" style="display: none;">
                          <?php if(!empty($netherlands_states)):?>
                          <option value="0"><?php echo _("--Select--");?></option>
                          <?php foreach($netherlands_states as $state):?>
                          <option value="<?php echo $state['state_id'];?>"><?php echo $state['state_name'];?></option>
                          <?php endforeach;?>

                          <?php ;else:?>
                          <option value="0"><?php echo _("No State");?></option>
                          <?php endif;?>
                        </select>
                      </td>
                      <td width="40%" style="text-align: center;">
                        <select id="postcodes" name="postcodes" multiple="multiple" style="height: 160px;">
                          <option value="0"><?php echo _("No City");?></option>
                        </select>

                        <input type="button" id="up_delivery_area" name="up_delivery_area" value="<?php echo _("Add above selected city(s)");?>" />
                      </td>
                    </tr>
                    <tr class="national_row">
                      <td>&nbsp;</td>
                      <td class="textlabel"><?php echo _("Delivery Charge");?>:</td>
                      <td><input type="radio" id="delivery_charge_km" name="current_delivery_charge" value="km" <?php if(!empty($delivery_area_settings) && $delivery_area_settings['0']['current_delivery_charge'] == 'km' ){?>checked="checked"<?php }?> /> <input type="text/javascript" class="text short" id="charge_km" name="charge_km" value="<?php if(!empty($delivery_area_settings) && $delivery_area_settings['0']['charge_km']){ echo $delivery_area_settings['0']['charge_km']; }?>" /> <?php echo _("per Km");?></td>
                      <td>&nbsp;</td>
                    </tr>
                     <tr class="national_row">
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td><input type="radio" id="delivery_charge_fixed" name="current_delivery_charge" value="fixed" <?php if(!empty($delivery_area_settings) && $delivery_area_settings['0']['current_delivery_charge'] == 'fixed' ){?>checked="checked"<?php }?> /> <input type="text/javascript" class="text short" id="charge_fixed" name="charge_fixed" value="<?php if(!empty($delivery_area_settings) && $delivery_area_settings['0']['charge_fixed']){ echo $delivery_area_settings['0']['charge_fixed']; }?>" /> <?php echo _("Fixed fee");?></td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr class="national_row">
                      <td colspan="4">&nbsp;</td>
                    </tr>
                    <tr class="national_row">
                      <td>&nbsp;</td>
                        <td class="textlabel"><?php echo _('Minimum amount for delivery')?></td>
                        <td><input type="text" class="text short" id="min_amount_delivery" name="min_amount_delivery" value="<?php if(!empty($delivery_area_settings) && $delivery_area_settings['0']['min_amount_delivery']){ echo $delivery_area_settings['0']['min_amount_delivery']; }?>" /> &euro;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <!-- >>>>>>>>>>>>>>>>>>>>>>>>> END: NATIONAL DELIEVRY <<<<<<<<<<<<<<<<<<<<<<<<<<<<< -->

                    <!-- >>>>>>>>>>>>>>>>>>>>>> START: INTERNATIONAL DELIEVRY <<<<<<<<<<<<<<<<<<<<<<<<< -->
                    <tr class="international_row">
                      <td width="20%" align="right">&nbsp;</td>
                      <td width="20%" >&nbsp;</td>
                      <td width="20%" style="text-align: center;" class="textlabel" ><?php echo _("Country");?></td>
                      <td width="40%" >&nbsp;</td>
                    </tr>
                    <tr class="international_row">
                      <td width="20%" align="left">&nbsp;</td>
                      <td width="20%" style="text-align: center;">&nbsp;</td>
                      <td width="20%" style="text-align: center;">
                        <?php if(!empty($all_countries)){?>
                        <select id="inter_c" name="inter_c" multiple="multiple" style="height: 160px;">
                <?php foreach ($all_countries as $all_country){?>
                <option value="<?php echo $all_country->id;?>" <?php if(in_array($all_country->id,$country_ids)){?>selected="selected"<?php }?> ><?php echo $all_country->country_name; ?></option>
                <!-- <option value="<?php echo $all_country->id;?>" <?php if(in_array($all_country->id,$country_ids_int)){?>selected="selected"<?php }?> ><?php echo $all_country->country_name; ?></option> -->
                <?php }?>
                        </select>
                        <?php }?>
                      </td>
                      <td width="40%" style="text-align: center;">
                        <input type="button" id="up_comp_country" name="up_comp_country" value="<?php echo _("Add selected country(s)");?>" />
                        <img id="load_postcodes" src="<?php echo base_url();?>assets/cp/images/wait.gif" style="display: none;"/>
                      </td>
                    </tr>

                    <!-- Here selected countries will be displayed with their delivery cost -->
                    <tr class="international_row">
                      <td width="20%" align="left">&nbsp;</td>
                      <td width="20%" align="left">&nbsp;</td>
                      <!--<td width="20%" id="selected_countries">
                        <?php if(!empty($companies_countries)){?>
                        <p> <?php //echo _('Please insert delivery costs for each countries you have selected');?> </p>
                        <?php foreach ($companies_countries as $companies_country){?>
                        <p id="s_c_<?php echo $companies_country->country_id;?>">
                          <label>
                  <span style="width:215px; display:inline-block">
                    <?php echo $companies_country->country_name; ?>
                  </span>
                  <input type="text" class="short text" name="country_cost[]" value="<?php echo $companies_country->country_cost; ?>" /> &euro;
                  <input type="hidden" name="country_selected[]" class="country_selected" value="<?php echo $companies_country->country_id; ?>" />
                </label>
              </p>
                        <?php }?>
                        <?php }?>
                      </td>-->
                      <script type="text/javascript">
                        function int_del_rate(cou_id){
                          $('#country_row_'+cou_id).show();
                            $('#hide_country_'+cou_id).show();
                            $('#show_country_'+cou_id).hide();
                        }
                        function int_del_rate_hide(cou_id){
                          $('#country_row_'+cou_id).hide();
                            $('#hide_country_'+cou_id).hide();
                            $('#show_country_'+cou_id).show();
                        }
                        function int_del_add(cou_id){
                          $('#rate_country_id').val(cou_id);
                        }
                        function int_del_upd(cou_id, rate_name, criteria, lower_range, upper_range, rate_cost, rate_id){
                          tb_show('<?php echo _('Update Delivery Rate')?>','#TB_inline?height=155&width=300&inlineId=int_del_add_upd');
                          $('#TB_ajaxContent').find('#rate_name').val(rate_name);
                          $('#TB_ajaxContent').find('#rate_criteria').val(criteria);
                          $('#TB_ajaxContent').find('#lower_range').val(lower_range);
                          $('#TB_ajaxContent').find('#upper_range').val(upper_range);
                          $('#TB_ajaxContent').find('#rate_cost').val(rate_cost);
                          $('#TB_ajaxContent').find('#rate_id').val(rate_id);
                          $('#TB_ajaxContent').find('#rate_country_id').val(cou_id);
                        }
                        function int_remove_rate(elem, row_number){
                          $(elem).closest('tr').html('');
                        }
                      </script>
                      <td width="60%" id="selected_countries_int">
                        <?php if(!empty($companies_countries)){ $str = '';$str_arr= array();?>
                        <p> <?php echo _('Please insert delivery costs for each countries you have selected');?> </p>
                        <?php //foreach ($companies_countries_int as $companies_country){if(!in_array($companies_country->country_id, $str_arr)){ $str .= $companies_country->country_id.' '; $str_arr = explode(' ', $str);
                        foreach ($companies_countries as $companies_country){if(!in_array($companies_country->country_id, $str_arr)){ $str .= $companies_country->country_id.' '; $str_arr = explode(' ', $str);?>
                        <p id="s_c_<?php echo $companies_country->country_id;?>">
                          <label>
                  <span style="width:215px; display:inline-block">
                    <?php echo $companies_country->country_name; ?>
                  </span>
                </label>
                <input type="hidden" name="country_selected[]" class="country_selected" value="<?php echo $companies_country->country_id; ?>" />
                <a id="show_country_<?php echo $companies_country->country_id;?>" href="javascript:void(0)" onclick="int_del_rate(<?php echo $companies_country->country_id;?>)"><img src="<?php echo base_url();?>assets/images/plus_btn.png" style="width: 22px; display: inline-block; padding-top: 1px;" class="close_img"/><?php echo _('See Selected Delivery Rates');?></a>
                <a id="hide_country_<?php echo $companies_country->country_id;?>" style="display:none" href="javascript:void(0)" onclick="int_del_rate_hide(<?php echo $companies_country->country_id;?>)">
                <img src="<?php echo base_url();?>assets/images/minus_btn.png" style="width: 22px; display: inline-block; padding-top: 1px;" class="open_img"/></a>
                <div id="country_row_<?php echo $companies_country->country_id;?>" style="display:none">
                  <a class="thickbox" title="Add New Rate" href="#TB_inline?height=155&width=300&inlineId=int_del_add_upd" onclick="int_del_add(<?php echo $companies_country->country_id;?>)"><?php echo _('Add New Delivery Rate');?></a>
                  <table>
                    <thead>
                    <tr>
                      <th width= "20%"><?php echo _('Delivery Rate Name');?></th>
                      <th width= "20%"><?php echo _('Based On');?></th>
                      <th width= "20%"><?php echo _('Range');?></th>
                      <th width= "10%"><?php echo _('Cost');?></th>
                      <th width= "30%"><?php echo _('Action');?></th>
                    </tr>
                    </thead>
                    <tbody id="tab_<?php echo $companies_country->country_id?>">
                    <?php if(!empty($companies_countries_int)){$i=1;?>
                    <?php foreach ($companies_countries_int as $companies_country_int){
                        if($companies_country->country_id == $companies_country_int->country_id){ ?>
                      <tr id="<?php echo $companies_country->country_id.'_'.$i;?>" class="rows">
                        <td width= "20%"><?php echo $companies_country_int->rate_name; ?></td>
                        <td width= "20%"><?php echo $companies_country_int->criteria;?><?php if($companies_country_int->criteria == 'weight_wise')echo _('in KG')?></td>
                        <td width= "20%"><?php echo $companies_country_int->lower_range.'-'.$companies_country_int->upper_range; ?></td>
                        <td width= "10%"><?php echo $companies_country_int->rate_cost; ?>&euro;</td>
                        <td width= "30%"><a title="Update Delivery Rate" href="javascript:void(0);" onclick="int_del_upd('<?php echo $companies_country->country_id;?>','<?php echo $companies_country_int->rate_name; ?>', '<?php echo $companies_country_int->criteria; ?>', '<?php echo $companies_country_int->lower_range?>','<?php echo $companies_country_int->upper_range; ?>', '<?php echo $companies_country_int->rate_cost; ?>','<?php echo $i;?>')"><?php echo _('UPDATE');?></a>
                        <a href="javascript:void(0);" onclick="int_remove_rate(this,'<?php echo $companies_country->country_id.'_'.$companies_country_int->id?>')"><?php echo _('Delete');?></a></td>
                        <?php $companies_country_int->rate_id = '';
                        $companies_country_int->rate_country_id = $companies_country_int->country_id;?>
                        <input id="<?php echo $companies_country->country_id.'_'.$companies_country_int->id.'_hidden';?>" type="hidden" value="<?php echo urlencode(json_encode($companies_country_int));?>" name="rate_arr[]">
                      </tr>
                      <?php $i++;}}}?>
                    </tbody>
                  </table>
                </div>
              </p>
                        <?php }}?>
                        <?php }?>
                      </td>
                      <script type="text/javascript">
              jQuery(document).ready(function(){
                $('#TB_ajaxContent').find('#rate_id').val('');
              });
                        function int_del_add_upd_submit(){
                          insert = [];
                          int_rate_name = $('#TB_ajaxContent').find('#rate_name').val();
                          int_rate_criteria = $('#TB_ajaxContent').find('#rate_criteria').val();
                          int_lower_range = $('#TB_ajaxContent').find('#lower_range').val();
                          int_upper_range = $('#TB_ajaxContent').find('#upper_range').val();
                          int_rate_cost = $('#TB_ajaxContent').find('#rate_cost').val();
                          int_rate_id = $('#TB_ajaxContent').find('#rate_id').val();
                          int_country_id = $('#TB_ajaxContent').find('#rate_country_id').val();

                          insert= {
                              'rate_name': int_rate_name,
                              'criteria': int_rate_criteria,
                              'lower_range': int_lower_range,
                              'upper_range': int_upper_range,
                              'rate_cost': int_rate_cost,
                              'rate_id': int_rate_id,
                              'rate_country_id': int_country_id
                              };

                          if(int_rate_id != ''){
                            $('#'+int_country_id+'_'+int_rate_id+'_hidden').html("");
                            var update_html = '';
                            update_html +='<td>'+int_rate_name+'</td>';
                            update_html +='<td>'+int_rate_criteria+((int_rate_criteria == 'weight_wise')?'(<?php echo _('in KG');?>)':'')+'</td>';
                            update_html +='<td>'+int_lower_range+'-'+int_upper_range+'</td>';
                            update_html +='<td>'+int_rate_cost+'&euro;</td>';
                            update_html +='<td><a title="Update Delivery Rate" href="javascript:void(0);" onclick="int_del_upd(\''+int_country_id+'\',\''+int_rate_name+'\', \''+int_rate_criteria+'\', \''+int_lower_range+'\',\''+int_upper_range+'\', \''+int_rate_cost+'\',\''+int_rate_id+'\')"><?php echo _("UPDATE")?></a>';
                  update_html +='<a href="javascript:void(0);" onclick="int_remove_rate(this,\''+int_country_id+'_'+int_rate_id+'\')"><?php echo _('Delete');?></a></td>';
                  update_html +='<input id="'+int_country_id+'_'+int_rate_id+'_hidden" type="hidden" value="'+escape(JSON.stringify(insert))+'" name="rate_arr[]"> ';
                            $('#'+int_country_id+'_'+int_rate_id).html(update_html);
                          }
                          else{
                            var i = $('#tab_'+int_country_id+' tr').length + 1;
                            var update_html = '';
                            update_html +='<tr id="'+int_country_id+'_'+i+'" class="rows">';
                            update_html +='<td>'+int_rate_name+'</td>';
                            update_html +='<td>'+int_rate_criteria+((int_rate_criteria == 'weight_wise')?'(<?php echo _('in KG');?>)':'')+'</td>';
                            update_html +='<td>'+int_lower_range+'-'+int_upper_range+'</td>';
                            update_html +='<td>'+int_rate_cost+'&euro;</td>';
                            update_html +='<td><a title="Update Delivery Rate" href="javascript:void(0);" onclick="int_del_upd(\''+int_country_id+'\',\''+int_rate_name+'\', \''+int_rate_criteria+'\', \''+int_lower_range+'\',\''+int_upper_range+'\', \''+int_rate_cost+'\',\''+i+'\')"><?php echo _('UPDATE');?></a> ';
                  update_html +='<a href="javascript:void(0);" onclick="int_remove_rate(this,\''+int_country_id+'_'+int_rate_id+'\')"><?php echo _('Delete');?></a></td>';
                  update_html +='<input id="'+int_country_id+'_'+i+'_hidden" type="hidden" value="'+escape(JSON.stringify(insert))+'" name="rate_arr[]"> ';
                  update_html +='</tr>';

                            $('#tab_'+int_country_id).append(update_html);

                            if(!$('#tab_'+int_country_id).length){
                              var int_html = '';
                              int_html +='<a id="show_country_'+int_country_id+'" href="javascript:void(0)" style = "display:none" onclick="int_del_rate('+int_country_id+')"><img src="'+base_url+'assets/images/plus_btn.png" style="width: 22px; display: inline-block; padding-top: 1px;" class="close_img"/><?php echo _('See Selected Delivery Rates');?></a>';
                            int_html +='<a id="hide_country_'+int_country_id+'" href="javascript:void(0)" onclick="int_del_rate_hide('+int_country_id+')">';
                        int_html +='<img src="'+base_url+'assets/images/minus_btn.png" style="width: 22px; display: inline-block; padding-top: 1px;" class="open_img"/></a>';
                            int_html +='<div id="country_row_'+int_country_id+'">';
                            int_html +=' <a href="javascript:void(0)" onclick="int_del_add_new('+int_country_id+')"><?php echo _('Add New Delivery Rate');?></a>';
                            int_html +='  <table>';
                            int_html +='    <thead>';
                            int_html +='    <tr>';
                            int_html +='      <th><?php echo _('Delivery Rate Name');?></th>';
                            int_html +='      <th><?php echo _('Based On');?></th>';
                            int_html +='      <th><?php echo _('Range');?></th>';
                            int_html +='      <th><?php echo _('Cost');?></th>';
                            int_html +='      <th><?php echo _('Action');?></th>';
                            int_html +='    </tr>';
                            int_html +='    </thead>';
                            int_html +='    <tbody id="tab_'+int_country_id+'">';
                        int_html +='      <tr id="'+int_country_id+'_1" class="rows">';
                        int_html +='        <td>'+int_rate_name+'</td>';
                        int_html +='        <td>'+int_rate_criteria+((int_rate_criteria == 'weight_wise')?'(<?php echo _('in KG');?>)':'')+'</td>';
                        int_html +='        <td>'+int_lower_range+'-'+int_upper_range+'</td>';
                        int_html +='        <td>'+int_rate_cost+'&euro;</td>';
                        int_html +='        <td><a title="Update Delivery Rate" href="javascript:void(0)" onclick="int_del_upd(\''+int_country_id+'\',\''+int_rate_name+'\', \''+int_rate_criteria+'\',\''+int_lower_range+'\',\''+int_upper_range+'\',\''+int_rate_cost+'\',\'1\')"><?php echo _('UPDATE');?></a>';
                        int_html +='        <a href="javascript:void(0)" onclick="int_remove_rate(this,\''+int_country_id+'_1\')"><?php echo _('Delete');?></a></td>';
                        int_html +='        <input id="'+int_country_id+'_1_hidden" type="hidden" value="'+escape(JSON.stringify(insert))+'" name="rate_arr[]"> ';
                        int_html +='      </tr>';
                        int_html +='    </tbody>';
                        int_html +='  </table>';
                        int_html +='</div>';
                        $('#country_rows_'+int_country_id).html('');
                        $('#s_c_'+int_country_id).append(int_html);
                            }
                          }
                          self.parent.tb_remove();
                        }
                      </script>
                      <div id="int_del_add_upd" style="display:none">
                        <form method="post" action="" id="add_delivery_rate">
                          <label><?php echo _('Rate Name');?>:</label>
                            <select id="rate_name" style="display: inline; padding: 5px;">
                            <option value="Standard Delivery"><?php echo _('Standard Delivery');?></option>
                            <option value="Medium Goods Delivery"><?php echo _('Medium Goods Delivery');?></option>
                            <option value="Heavy Goods Delivery"><?php echo _('Heavy Goods Delivery');?></option>
                          </select><br/>
                          <label><?php echo _('Criteria')?>:</label>
                          <select id="rate_criteria" style="display: inline; padding: 5px;">
                            <option value="weight_wise"><?php echo _('weight wise');?>(<?php echo _('in KG');?>)</option>
                            <option value="per_unit"><?php echo _('per unit');?></option>
                            <option value="per_person"><?php echo _('per person');?></option>
                          </select><br/>
                  <label><?php echo _('Range');?>:</label>
                            <input id="lower_range" class="text range" type="number" max="99" min="0" value="" name="lower_range" placeholder="lower_range" style="width: 20%"/>
                            -
                          <input id="upper_range" class="text range" type="number" max="100" min="1" value="" name="upper_range" placeholder="upper_range" style="width: 20%"/><br/>
                        <label><?php echo _('Cost');?>:</label>
                            <input id="rate_cost" class="text" type="text" name="rate_cost" value="" style="width: 20%"/>&euro;<br/>
                          <input id="rate_id" type="hidden" name="rate_id" value="">
                          <input id="rate_country_id" type="hidden" name="rate_country_id" value="">
                <input id="int_del_add_upd_submit" onclick="int_del_add_upd_submit()" type="button" value="<?php echo _('Submit')?>">
              </form>
                      </div>
                      <td width="40%" >&nbsp;</td>
                    </tr>
                    <tr class="international_row">
                      <td colspan="4">&nbsp;</td>
                    </tr>
                    <tr class="international_row">
                      <td>&nbsp;</td>
                        <td class="textlabel"><?php echo _('Minimum amount for delivery')?> (<?php echo _('International');?>)</td>
                        <td><input type="text" class="text short" id="min_amount_delivery_int" name="min_amount_delivery_int" value="<?php if(!empty($delivery_area_settings) && $delivery_area_settings['0']['min_amount_delivery_int']){ echo $delivery_area_settings['0']['min_amount_delivery_int']; }?>" /> &euro;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <!-- >>>>>>>>>>>>>>>>>>>>>>> END: INTERNATIONAL DELIEVRY <<<<<<<<<<<<<<<<<<<<<<<<<< -->
                  </tbody>
                </table>
                <table cellspacing="0">
                  <tbody>
                    <tr>
                      <td class="textlabel" colspan="2"></td>
                    </tr>
                    <tr>
                      <td class="textlabel"><?php echo _('Order Settings');?> </td>
                      <td>
                        <table class="override" width="100%" cellspacing="0" cellpadding="0" border="0">
                          <tr>
                            <td width="3%">
                              <input type="checkbox" onClick="samedayDelivery();" value="1" class="checkbox" id="same_day_orders_delivery" name="same_day_orders_delivery" <?php if($order_settings&&$order_settings[0]->same_day_orders_delivery):?>checked="checked"<?php endif;?>>
                            </td>
                            <td width="30%">
                              <?php echo _('Allow orders for the day itself for ')?>
                            </td>
                            <td width="67%">
                              <?php $allowed_days_delivery = explode("," , $order_settings[0]->allowed_days_delivery); ?>
                              <select id="allowed_days_delivery" name="allowed_days_delivery[]" multiple>
                                <option value="1" <?php if(in_array("1",$allowed_days_delivery)){?>selected="selected"<?php }?>><?php echo _("Monday");?></option>
                                <option value="2" <?php if(in_array("2",$allowed_days_delivery)){?>selected="selected"<?php }?>><?php echo _("Tuesday");?></option>
                                <option value="3" <?php if(in_array("3",$allowed_days_delivery)){?>selected="selected"<?php }?>><?php echo _("Wednesday");?></option>
                                <option value="4" <?php if(in_array("4",$allowed_days_delivery)){?>selected="selected"<?php }?>><?php echo _("Thursday");?></option>
                                <option value="5" <?php if(in_array("5",$allowed_days_delivery)){?>selected="selected"<?php }?>><?php echo _("Friday");?></option>
                                <option value="6" <?php if(in_array("6",$allowed_days_delivery)){?>selected="selected"<?php }?>><?php echo _("Saturday");?></option>
                                <option value="7" <?php if(in_array("7",$allowed_days_delivery)){?>selected="selected"<?php }?>><?php echo _("Sunday");?></option>
                              </select>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                    <tr>
                      <td class="textlabel">&nbsp;</td>
                        <td style="padding-left:35px">
                          <table border="0" class="override" id="samedayDelivery" style="display: none;">
                            <tbody>
                                <tr>
                                    <td width="280" align="left"><?php echo _('Minimum time between ordering and delivery')?> &nbsp;</td>
                                    <td>
                                      <?php $hr = 0; $min = 0;?>
                                      <select style="margin-bottom:0px" class="select" type="select" id="time_diff_delivery" name="time_diff_delivery">
                                          <option value="0" selected="">-- <?php echo _('Select')?> --</option>
                                          <?php while(!($hr == 5 && $min == 30)){?>
                                          <?php $min = $min+30;?>
                                          <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
                                          <?php $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);?>
                                          <?php $min_str = ((strlen($min) == 1)?"0".$min:$min);?>
                                          <option value="<?php echo $hr_str.':'.$min_str;?>" <?php if($order_settings && $order_settings[0]->time_diff_delivery == $hr_str.":".$min_str):?>selected=""<?php endif;?>><?php echo $hr_str;?> : <?php echo $min_str;?></option>
                                          <?php }?>
                                      </select>
                                    </td>
                                </tr>
                              </tbody>
                          </table>
                        </td>
                    </tr>
                    <tr>
                      <td class="textlabel">&nbsp;</td>
                      <td style="padding-left:35px"><table border="0" class="override" id="samedayDeliveryTime" style="display:none">
                          <tbody>
                            <tr>
                                <td width="280" align="left"><?php echo _('Client can order same day till')?></td>
                                <td>
                                  <?php $hr = 0; $min = 0;?>
                                  <select style="margin-bottom:0px" class="select" type="select" id="same_day_time_delivery" name="same_day_time_delivery">
                                      <option value="0">-- <?php echo _('Select')?> --</option>
                                      <?php while(!($hr == 23 && $min == 60)){?>
                                      <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
                                      <?php $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);?>
                                      <?php $min_str = ((strlen($min) == 1)?"0".$min:$min);?>
                                      <option value="<?php echo $hr_str.':'.$min_str;?>" <?php if($order_settings && $order_settings[0]->same_day_time_delivery == $hr_str.":".$min_str):?>selected=""<?php endif;?>><?php echo $hr_str;?> : <?php echo $min_str;?></option>
                                      <?php $min = $min+30;?>
                                      <?php }?>
                                  </select>
                            </td>
                            </tr>
                          </tbody>
                        </table></td>
                    </tr>

                    <tr>
                      <td class="textlabel">&nbsp;</td>
                      <td><table width="100%" cellspacing="0" cellpadding="0" border="0" class="override">
                          <tbody>
                          <?php //$custom_delivery_array = explode("#",$order_settings[0]->customise_delivery); //print_r($custom_delivery_array);?>
                          <?php $custom_time_delivery_array = explode("#",$order_settings[0]->custom_time_delivery);?>
                          <?php $custom_days_delivery_array = explode("#",$order_settings[0]->custom_days_delivery);?>
                          <?php for($i = 0 ; $i < 7 ; $i++){?>
                            <tr style="height: 30px;">
                                <td width="2%"><?php /*?><input type="checkbox" value="<?php echo $i+1;?>" class="checkbox" id="customise_delivery" name="customise_delivery[]"<?php if($order_settings && isset($custom_delivery_array[$i]) && $custom_delivery_array[$i] == $i+1 ):?>checked="checked"<?php endif;?>><?php */?></td>
                                <td width="23%">&nbsp;&nbsp;<?php echo _('If the customer ordered for')?></td>
                                <td width="10%">&nbsp;&nbsp;
                                <?php
                                  if(isset($_COOKIE['locale']) && $_COOKIE['locale'] == 'nl_NL')
				                  {
				                     if( $i == 0 ){
				                        echo 'Maandag';
				                     }
				                     if( $i == 1 )
				                      echo 'Dinsdag';
				                     if( $i == 2 )
				                      echo 'Woensdag';
				                     if( $i == 3 )
				                       echo 'Donderdag';
				                     if( $i == 4 )
				                      echo 'Vrijdag';
				                     if( $i == 5 )
				                      echo 'Zaterdag';
				                     if( $i == 6 )
				                      echo 'Zondag';
				                  }else if(isset($_COOKIE['locale']) && $_COOKIE['locale'] == 'fr_FR')
				                  {
				                     if( $i == 0 ){
				                        echo 'Lundi';
				                     }
				                     if( $i == 1 )
				                      echo 'Mardi';
				                     if( $i == 2 )
				                      echo 'Mercredi';
				                     if( $i == 3 )
				                       echo 'Jeudi';
				                     if( $i == 4 )
				                      echo 'Vendredi';
				                     if( $i == 5 )
				                      echo 'Samedi';
				                     if( $i == 6 )
				                      echo 'dimanche';
				                  }else{
				                    if( $i == 0 )
				                      echo 'Monday';
				                    if( $i == 1 )
				                      echo 'Tuesday';
				                    if( $i == 2 )
				                      echo 'Wednesday';
				                    if( $i == 3 )
				                      echo 'Thursday';
				                    if( $i == 4 )
				                      echo 'friday';
				                    if( $i == 5 )
				                      echo 'Saturday';
				                    if( $i == 6 )
				                      echo 'Sunday';
				                  }
				                ?>
                                </td>
                                <td width="15%">
                                  <?php $hr = 0; $min = 0;?>
                                  <select style="margin-bottom:0px" class="select" type="select" id="custom_time_delivery[]" name="custom_time_delivery[]">
                                      <option value="0" selected="">-- <?php echo _('Select')?>--</option>
                                      <?php while(!($hr == 23 && $min == 60)){?>
                                      <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
                                      <?php $selected = false;?>
                                      <?php
                                        $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);
                                        $min_str = ((strlen($min) == 1)?"0".$min:$min);
                                        if($order_settings && isset($custom_time_delivery_array[$i])){
                        if($min_str == 0 && ($custom_time_delivery_array[$i] == $hr_str.":".$min_str || $custom_time_delivery_array[$i] == $hr_str)){
                          $selected = true;
                        }elseif($custom_time_delivery_array[$i] == $hr_str.":".$min_str){
                          $selected = true;
                        }
                      }
                                      ?>
                                      <option value="<?php echo $hr_str.':'.$min_str;?>" <?php if($selected){?>selected=""<?php }?>><?php echo $hr_str;?> : <?php echo $min_str;?></option>
                                      <?php $min = $min+30;?>
                                      <?php }?>
                                  </select>
                                </td>
                                <td width="40%"><?php echo _('hours: the products can be supplied from')?></td>
                                <td width="10%">
                                  <?php $day = 1;?>
                                  <select style="margin-bottom:0px" class="select" type="select" id="custom_days_delivery[]" name="custom_days_delivery[]">
                                      <option value="0" selected="">-- <?php echo _('Select')?> --</option>
                                      <option value="<?php echo $day;?>" <?php if($order_settings && isset($custom_days_delivery_array[$i]) && $custom_days_delivery_array[$i] == $day):?>selected=""<?php endif;?>><?php echo _('Next day')?></option>
                                      <?php $day++;?>
                                      <?php while($day < 14){?>
                                      <option value="<?php echo $day;?>" <?php if($order_settings && isset($custom_days_delivery_array[$i]) && $custom_days_delivery_array[$i] == $day):?>selected=""<?php endif;?>><?php echo $day.' '._('Days')?></option>
                                      <?php $day++?>
                                      <?php }?>
                                  </select>
                                </td>
                            </tr>
                            <?php }?>
                            <tr><td colspan=6>&nbsp;</td></tr>
                            <tr>
                              <td ><input type="checkbox" value="1" class="checkbox" id="customise_delivery_holiday" name="customise_delivery_holiday"<?php if($order_settings && $order_settings[0]->custom_delivery_holiday ):?>checked="checked"<?php endif;?> /></td>
                              <td colspan=3><?php echo _("If holiday or closing day overule above settings with");?></td>
                              <td colspan=2>
                                <?php $day = 1;?>
                                <select style="margin-bottom:0px" class="select" type="select" id="custom_holidays_delivery" name="custom_holidays_delivery">
                                      <option value="0">-- <?php echo _('Select ')?>--</option>
                                      <option value="<?php echo $day;?>" <?php if($order_settings && $order_settings[0]->custom_holidays_delivery == $day):?>selected=""<?php endif;?>><?php echo _('1')?></option>
                                      <?php $day++;?>
                                      <?php while($day < 14){?>
                                      <option value="<?php echo $day;?>" <?php if($order_settings && $order_settings[0]->custom_holidays_delivery == $day):?>selected=""<?php endif;?>><?php echo $day.' '._('Days')?></option>
                                      <?php $day++?>
                                      <?php }?>
                                  </select>
                                </td>
                            </tr>
                          </tbody>
                        </table></td>
                    </tr>
                    <tr>
                        <td class="textlabel">&nbsp;</td>
                        <td>
                          <table border="0" class="override">
                              <tbody>
                                <tr>
                                    <td width="250px" align="left"><strong><?php echo _('Opening Hours:')?></strong>&nbsp;<?php echo _('ALL DAY means FROM')?>&nbsp;</td>
                                    <td width="150">
                                      <?php $hr = 0; $min = 0;?>
                                      <select style="margin-bottom:0px" class="select" type="select" id="all_day_starttime_d" name="all_day_starttime_d">
                                          <option value="0">-- <?php echo _('Select')?> --</option>
                                          <?php while(!($hr == 23 && $min == 60)){?>
                                        <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
                                        <?php $selected = false;?>
                                        <?php
                                          $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);
                                          $min_str = ((strlen($min) == 1)?"0".$min:$min);
                                          if($order_settings){
                            if($min_str == 0 && ($order_settings[0]->all_day_starttime_d == $hr_str.":".$min_str || $order_settings[0]->all_day_starttime_d == $hr_str)){
                              $selected = true;
                            }elseif($order_settings[0]->all_day_starttime_d == $hr_str.":".$min_str){
                              $selected = true;
                            }
                          }
                                      ?>
                                        <option value="<?php echo $hr_str.':'.$min_str;?>" <?php if($selected){?>selected=""<?php }?>><?php echo $hr_str;?> : <?php echo $min_str;?></option>
                                        <?php $min = $min+30;?>
                                        <?php }?>
                                      </select>
                                    </td>
                                    <td width="30" align="center">&nbsp;&nbsp;<?php echo _('TO')?> &nbsp;</td>
                                    <td>
                                      <?php $hr = 0; $min = 0;?>
                                      <select style="margin-bottom:0px" class="select" type="select" id="all_day_endtime_d" name="all_day_endtime_d">
                                          <option value="0">-- <?php echo _('Select');?> --</option>
                                          <?php while(!($hr == 23 && $min == 60)){?>
                                        <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
                                        <?php $selected = false;?>
                                        <?php
                                          $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);
                                          $min_str = ((strlen($min) == 1)?"0".$min:$min);
                                          if($order_settings){
                            if($min_str == 0 && ($order_settings[0]->all_day_endtime_d == $hr_str.":".$min_str || $order_settings[0]->all_day_endtime_d == $hr_str)){
                              $selected = true;
                            }elseif($order_settings[0]->all_day_endtime_d == $hr_str.":".$min_str){
                              $selected = true;
                            }
                          }
                                        ?>
                                        <option value="<?php echo $hr_str.':'.$min_str;?>" <?php if($selected){?>selected=""<?php }?>><?php echo $hr_str;?> : <?php echo $min_str;?></option>
                                        <?php $min = $min+30;?>
                                        <?php }?>
                                      </select>
                                    </td>
                                </tr>
                              </tbody>
                          </table>
                        </td>
                    </tr>
                    <tr>
                      <td width="20%" class="textlabel"><?php echo _('Order restriction time frame (in minutes)')?></td>
                        <td>
                          <?php $day = 5;?>
                          <select style="margin-bottom:0px;" class="select" type="select" id="time_restriction_d" name="time_restriction_d" >
                          <option value="0">-- <?php echo _('Select')?> --</option>
                          <?php while($day < 55){?>
                          <option value="<?php echo $day;?>" <?php if($order_settings && $order_settings[0]->time_restriction_d == $day):?>selected="selected"<?php endif;?>><?php echo $day;?></option>
                          <?php $day = $day + 5;?>
                          <?php }?>
                          </select>
                        </td>
                    </tr>
                    <tr>
                      <td width="20%" class="textlabel">&nbsp;</td>
                      <td><strong> <?php echo _('Delivery Hours')?> </strong></td>
                    </tr>
                    <tr>
                      <td class="textlabel">&nbsp;</td>
                      <td><table width="100%" cellspacing="0" cellpadding="0" border="0" class="override">
                          <tbody>
                            <?php if($pickup_settings):?>
                            <?php foreach($pickup_settings as $pickup_setting):?>

                            <?php if($pickup_setting->delivery1!="NONE"&&$pickup_setting->delivery1!="ALL DAY"&&$pickup_setting->delivery1!="CLOSED"):?>
                            <script type="text/javascript">
                jQuery('document').ready(function(){

                  show_hide('<?php echo $pickup_setting->day_id?>','delivery',this.value,'d1');
                });

              </script>
                            <?php endif;?>

                            <?php

              $day_name = $pickup_setting->name;

              if(isset($_COOKIE['locale']) && $_COOKIE['locale'] == 'nl_NL')
              {
                 if( $day_name == 'Monday' )
                 $day_name = 'Maandag';
                 if( $day_name == 'Tuesday' )
                 $day_name = 'Dinsdag';
                 if( $day_name == 'Wednesday' )
                 $day_name = 'Woensdag';
                 if( $day_name == 'Thursday' )
                 $day_name = 'Donderdag';
                 if( $day_name == 'Friday' )
                 $day_name = 'Vrijdag';
                 if( $day_name == 'Saturday' )
                 $day_name = 'Zaterdag';
                 if( $day_name == 'Sunday' )
                 $day_name = 'Zondag';
              }elseif(isset($_COOKIE['locale']) && $_COOKIE['locale'] == 'fr_FR')
			  {
					if( $day_name == 'Monday' )
						$day_name = 'Lundi';
					if( $day_name == 'Tuesday' )
						$day_name = 'Mardi';
					if( $day_name == 'Wednesday' )
						$day_name = 'Mercredi';
					if( $day_name == 'Thursday' )
						$day_name = 'Jeudi';
					if( $day_name == 'Friday' )
						$day_name = 'Vendredi';
					if( $day_name == 'Saturday' )
						$day_name = 'Samedi';
					if( $day_name == 'Sunday' )
						$day_name = 'dimanche';
			  } ?>

                            <tr>
                              <td width="100" height="30" style="text-align:right;padding-right:20px"><strong><?php echo $day_name; ?></strong></td>
                                <td>
                                  <?php $hr = 0; $min = 0;?>
                                  <select onChange="show_hide('<?php echo $pickup_setting->day_id?>','delivery',this.value,'d1');" style="margin-bottom:0px" class="select" type="select" id="d1[<?php echo $pickup_setting->day_id?>]" name="d1[<?php echo $pickup_setting->day_id?>]">
                                  <option value="0" <?php if($pickup_setting->delivery1=="0"):?>selected=""<?php endif;?>>--<?php echo _(' Select')?>--</option>
                                      <option value="ALL DAY" <?php if($pickup_setting->delivery1=="ALL DAY"):?>selected=""<?php endif;?>><?php echo _('ALL DAY')?></option>
                                      <option value="CLOSED" <?php if($pickup_setting->delivery1=="CLOSED"):?>selected=""<?php endif;?>><?php echo _('CLOSED')?></option>
                                      <option value="NONE" <?php if($pickup_setting->delivery1=="NONE"):?>selected=""<?php endif;?>><?php echo _('NONE')?></option>
                                      <?php while(!($hr == 23 && $min == 60)){?>
                                    <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
                                    <?php $selected = false;?>
                                    <?php
                                      $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);
                                      $min_str = ((strlen($min) == 1)?"0".$min:$min);
                      if($min_str == 0 && ($pickup_setting->delivery1 == $hr_str.":".$min_str || $pickup_setting->delivery1 == $hr_str)){
                        $selected = true;
                      }elseif($pickup_setting->delivery1 == $hr_str.":".$min_str){
                        $selected = true;
                      }
                                    ?>
                                    <option value="<?php echo $hr_str.':'.$min_str;?>" <?php if($selected){?>selected=""<?php }?>><?php echo $hr_str;?> : <?php echo $min_str;?></option>
                                    <?php $min = $min+30;?>
                                    <?php }?>
                                  </select>
                                </td>
                                <td>
                                  <table border="0" class="override">
                                      <tbody>
                                        <tr style="display: none;" id="delivery_<?php echo $pickup_setting->day_id?>">
                                            <td style="text-align:center"><strong><?php echo _('To')?>&nbsp;&nbsp;&nbsp;</strong></td>
                                            <td>
                                              <?php $hr = 0; $min = 0;?>
                                              <select style="margin-bottom:0px" class="select" type="select" id="d2[<?php echo $pickup_setting->day_id?>]" name="d2[<?php echo $pickup_setting->day_id?>]">
                                                <option value="0"  <?php if($pickup_setting->delivery2=="0"):?>selected=""<?php endif;?>>-- <?php echo _('Select')?> --</option>
                                                  <option value="NONE"  <?php if($pickup_setting->delivery2=="NONE"):?>selected=""<?php endif;?>><?php echo _('NONE')?></option>
                                                  <?php while(!($hr == 23 && $min == 60)){?>
                                          <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
                                          <?php $selected = false;?>
                                          <?php
                                            $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);
                                            $min_str = ((strlen($min) == 1)?"0".$min:$min);
                              if($min_str == 0 && ($pickup_setting->delivery2 == $hr_str.":".$min_str || $pickup_setting->delivery2 == $hr_str)){
                                $selected = true;
                              }elseif($pickup_setting->delivery2 == $hr_str.":".$min_str){
                                $selected = true;
                              }
                                          ?>
                                          <option value="<?php echo $hr_str.':'.$min_str;?>" <?php if($selected){?>selected=""<?php }?>><?php echo $hr_str;?> : <?php echo $min_str;?></option>
                                          <?php $min = $min+30;?>
                                          <?php }?>
                                              </select>
                                            </td>
                                            <td><strong>&nbsp;&nbsp;&nbsp;<?php echo ('and')?>&nbsp;&nbsp;&nbsp;</strong></td>
                                            <td>
                                              <?php $hr = 0; $min = 0;?>
                                              <select style="margin-bottom:0px" class="select" type="select" id="d3[<?php echo $pickup_setting->day_id?>]" name="d3[<?php echo $pickup_setting->day_id?>]">
                                                  <option value="0"  <?php if($pickup_setting->delivery3=="0"):?>selected=""<?php endif;?>>-- <?php echo _('Select')?> --</option>
                                                  <option value="NONE"  <?php if($pickup_setting->delivery3=="NONE"):?>selected=""<?php endif;?>><?php echo _('NONE')?></option>
                                                  <?php while(!($hr == 23 && $min == 60)){?>
                                          <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
                                          <?php $selected = false;?>
                                          <?php
                                            $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);
                                            $min_str = ((strlen($min) == 1)?"0".$min:$min);
                              if($min_str == 0 && ($pickup_setting->delivery3 == $hr_str.":".$min_str || $pickup_setting->delivery3 == $hr_str)){
                                $selected = true;
                              }elseif($pickup_setting->delivery3 == $hr_str.":".$min_str){
                                $selected = true;
                              }
                                          ?>
                                          <option value="<?php echo $hr_str.':'.$min_str;?>" <?php if($selected){?>selected=""<?php }?>><?php echo $hr_str;?> : <?php echo $min_str;?></option>
                                          <?php $min = $min+30;?>
                                          <?php }?>
                                              </select>
                                            </td>
                                            <td><strong>&nbsp;&nbsp;&nbsp;<?php echo _('To')?>&nbsp;&nbsp;&nbsp;</strong></td>
                                            <td>
                                              <?php $hr = 0; $min = 0;?>
                                              <select style="margin-bottom:0px" class="select" type="select" id="d4[<?php echo $pickup_setting->day_id?>]" name="d4[<?php echo $pickup_setting->day_id?>]">
                                                  <option value="0"  <?php if($pickup_setting->delivery4=="0"):?>selected=""<?php endif;?>>-- <?php echo _('Select')?> --</option>
                                                  <option value="NONE" <?php if($pickup_setting->delivery4=="NONE"):?>selected=""<?php endif;?>><?php echo _('NONE')?></option>
                                                  <?php while(!($hr == 23 && $min == 60)){?>
                                          <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
                                          <?php $selected = false;?>
                                          <?php
                                            $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);
                                            $min_str = ((strlen($min) == 1)?"0".$min:$min);
                              if($min_str == 0 && ($pickup_setting->delivery4 == $hr_str.":".$min_str || $pickup_setting->delivery4 == $hr_str)){
                                $selected = true;
                              }elseif($pickup_setting->delivery4 == $hr_str.":".$min_str){
                                $selected = true;
                              }
                                          ?>
                                          <option value="<?php echo $hr_str.':'.$min_str;?>" <?php if($selected){?>selected=""<?php }?>><?php echo $hr_str;?> : <?php echo $min_str;?></option>
                                          <?php $min = $min+30;?>
                                          <?php }?>
                                              </select>
                                            </td>
                                        </tr>
                                      </tbody>
                                  </table>
                                </td>
                            </tr>
                            <?php endforeach;?>
                            <?php endif;?>
                          </tbody>
                        </table></td>
                    </tr>
                    <tr>
                      <td width="20%" class="textlabel">&nbsp;</td>
                      <td>
                        <input type="checkbox" value="1" class="checkbox" id="hide_hrs_min_delivery" name="hide_hrs_min_delivery" <?php if($order_settings && $order_settings[0]->hide_hrs_min_delivery):?>checked="checked"<?php endif;?>>
                        &nbsp;&nbsp;&nbsp;&nbsp;<?php echo _('Hide the hours and minutes (clients cant choose the time)')?>
                      </td>
                    </tr>
                    <tr>
                      <td class="save_b" colspan="2"><input type="submit"  value="<?php echo _('UPDATE');?>" class="submit" id="btn_update" name="btn_update" onClick="setHiddenVar('delivery');">
                      <!-- onClick="setHiddenVar('delivery');" -->
                      </td>
                      <input type="hidden" value="edit_delivery_settings" id="act" name="act">
                    </tr>
                  </tbody>
                </table>
              </form>
<script type="text/javascript">

          function validateForm4() {

                var x = document.forms["frm_delivery_settings"]["all_day_starttime_d"].value;
                var y=document.forms["frm_delivery_settings"]["all_day_endtime_d"].value;

                      if(y== 0){
                       alert("<?php echo _("Select All DAY End Time")?>");
                          return false;
                      }

                      if (x == 0) {
                          alert("<?php echo _('Select ALL DAY Start Time')?>");
                          return false;
                      }

            }


    </script>

              <!--<script type="text/javascript" language="javascript">
          var frmValidator = new Validator("frm_delivery_settings");
          frmValidator.EnableMsgsTogether();
          frmValidator.setCallBack(validate_mess);
          frmValidator.addValidation("all_day_starttime_d","dontselect=0","<?php //echo _('Select ALL DAY Start Time')?>");
          frmValidator.addValidation("all_day_endtime_d","dontselect=0","<?php //echo _('Select All DAY End Time')?>");
        </script>-->
            </div>
          </div>
        </div>
      </div>