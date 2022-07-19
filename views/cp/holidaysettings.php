<div style="padding: 0px; display:block;" class="inside">
        <div class="table">
          <form action="<?php echo base_url()?>cp/settings" enctype="multipart/form-data" method="post" id="frm_holiday_settings" name="frm_holiday_settings" onsubmit="return validateForm5()">
            <input type="hidden" value="settings" name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW">
            <table cellspacing="0" border="0">
              <tbody>
                <tr>
                 
                  <td><span style="padding-top:10px"><?php echo _('Webshop during holiday')?></span>&nbsp;&nbsp;&nbsp;<a title="<?php echo _('Example, if you closed your customers will choose not to pick up orders during holidays. When in the calendar (right) one day like Christmas marks then this setting.')?>" href="#" class="help"><img width="16" height="16" src="<?php echo base_url()?>assets/cp/images/help.png"></a></td>
                </tr>
                <?php if($order_settings){?>
                <?php foreach($order_settings as $holiday_setting){?>
                <tr>                  
                  <td><span style="padding-top:10px">
                    <input type="radio" value="close" name="holiday" id="holiday"<?php if($holiday_setting->holiday_timings=="close"):?> checked="checked"<?php endif;?>>
                    </span>&nbsp;&nbsp;<?php echo _('Closed (no orders are accepted for that day)');?> </td>
                </tr>
                <tr>                  
                  <td><table width="100%" cellspacing="0" cellpadding="0" border="0" class="override">
                      <tbody>
                        <tr>
                          <td width="12%" height="30">
                            <input type="radio" value="open" name="holiday" id="holiday" <?php if($holiday_setting->holiday_timings!="close"&&$holiday_setting->holiday_timings!=""):?> checked="checked"<?php endif;?>>
                            &nbsp;&nbsp;<?php echo _('OPEN');?> </td>
                            <td width="13%">
                              <?php $hr = 0; $min = 0;?>
                              <select onChange="show_hide('0','holiday',this.value);" style="margin-bottom:0px" class="select" type="select" id="h1" name="h1">
                                  <option value="0" >-- <?php echo _('Select ')?>--</option>
                                  <option value="NONE" <?php if($holiday_setting->holiday_timings!="close"&&$holiday_setting->holiday_timings!=""&&$holiday_timings[0]=="NONE"):?>selected="selected"<?php endif;?>><?php echo _('NONE');?></option>
                                  
                                  <?php while(!($hr == 23 && $min == 60)){?>
                                  <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
                                  <?php $selected = false;?>
                                  <?php
                                    $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);
                                    $min_str = ((strlen($min) == 1)?"0".$min:$min);
                                    if($holiday_setting->holiday_timings != "close" && $holiday_setting->holiday_timings != "" && $holiday_timings){
                                      if($min_str == 0 && ($holiday_timings[0] == $hr_str.":".$min_str || $holiday_timings[0] == $hr_str)){
                        $selected = true;
                      }elseif($holiday_timings[0] == $hr_str.":".$min_str){
                        $selected = true;
                      }
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
                                    <tr style="display:block" id="holiday_0">
                                        <td style="text-align:center"><?php echo _('To')?>&nbsp;&nbsp;&nbsp;</td>
                                          <td>
                                            <?php $hr = 0; $min = 0;?>
                                            <select style="margin-bottom:0px" class="select" type="select" id="h2" name="h2">
                                                <option value="0" selected=""><?php echo _('-- Select --')?></option>
                                                <option value="NONE" <?php if($holiday_setting->holiday_timings!="close"&&$holiday_setting->holiday_timings!=""&&$holiday_timings[1]=="NONE"):?>selected="selected"<?php endif;?>><?php echo _('NONE');?></option>
                                                <?php while(!($hr == 23 && $min == 60)){?>
                                            <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
                                            <?php $selected = false;?>
                                            <?php
                                              $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);
                                              $min_str = ((strlen($min) == 1)?"0".$min:$min);
                                              if($holiday_setting->holiday_timings != "close" && $holiday_setting->holiday_timings != "" && $holiday_timings){
                                                if($min_str == 0 && ($holiday_timings[1] == $hr_str.":".$min_str || $holiday_timings[1] == $hr_str)){
                                  $selected = true;
                                }elseif($holiday_timings[1] == $hr_str.":".$min_str){
                                  $selected = true;
                                }
                                              }
                                            ?>
                                            <option value="<?php echo $hr_str.':'.$min_str;?>" <?php if($selected){?>selected=""<?php }?>><?php echo $hr_str;?> : <?php echo $min_str;?></option>
                                            <?php $min = $min+30;?>
                                            <?php }?>
                                            </select>
                                          </td>
                                          <td>&nbsp;&nbsp;&nbsp;<?php echo _('and')?>&nbsp;&nbsp;&nbsp;</td>
                                          <td>
                                            <?php $hr = 0; $min = 0;?>
                                            <select style="margin-bottom:0px" class="select" type="select" id="h3" name="h3">
                                                <option value="0" selected="">-- <?php echo _('Select')?> --</option>
                                                <option value="NONE" <?php if($holiday_setting->holiday_timings!="close"&&$holiday_setting->holiday_timings!=""&&$holiday_timings[2]=="NONE"):?>selected="selected"<?php endif;?>><?php echo _('NONE');?></option>
                                                <?php while(!($hr == 23 && $min == 60)){?>
                                            <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
                                            <?php $selected = false;?>
                                            <?php
                                              $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);
                                              $min_str = ((strlen($min) == 1)?"0".$min:$min);
                                              if($holiday_setting->holiday_timings != "close" && $holiday_setting->holiday_timings != "" && $holiday_timings){
                                                if($min_str == 0 && ($holiday_timings[2] == $hr_str.":".$min_str || $holiday_timings[2] == $hr_str)){
                                  $selected = true;
                                }elseif($holiday_timings[2] == $hr_str.":".$min_str){
                                  $selected = true;
                                }
                                              }
                                            ?>
                                            <option value="<?php echo $hr_str.':'.$min_str;?>" <?php if($selected){?>selected=""<?php }?>><?php echo $hr_str;?> : <?php echo $min_str;?></option>
                                            <?php $min = $min+30;?>
                                            <?php }?>
                                            </select>
                                          </td>
                                          <td>&nbsp;&nbsp;&nbsp;<?php echo _('To')?>&nbsp;&nbsp;&nbsp;</td>
                                          <td>
                                            <?php $hr = 0; $min = 0;?>
                                            <select style="margin-bottom:0px" class="select" type="select" id="h4" name="h4">
                                                <option value="0" selected="">--<?php echo _(' Select ')?>--</option>
                                                <option value="NONE" <?php if($holiday_setting->holiday_timings!="close"&&$holiday_setting->holiday_timings!=""&&$holiday_timings[3]=="NONE"):?>selected="selected"<?php endif;?>><?php echo _('NONE');?></option>
                                                <?php while(!($hr == 23 && $min == 60)){?>
                                            <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
                                            <?php $selected = false;?>
                                            <?php
                                              $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);
                                              $min_str = ((strlen($min) == 1)?"0".$min:$min);
                                              if($holiday_setting->holiday_timings != "close" && $holiday_setting->holiday_timings != "" && $holiday_timings){
                                                if($min_str == 0 && ($holiday_timings[3] == $hr_str.":".$min_str || $holiday_timings[3] == $hr_str)){
                                  $selected = true;
                                }elseif($holiday_timings[3] == $hr_str.":".$min_str){
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
                        </tbody>
                    </table></td>
                </tr>
                <tr>
                  <td class="save_b" colspan="2"><input type="submit" onClick="setHiddenVar('holiday');" value="<?php echo _('UPDATE');?>" class="submit" id="btn_update" name="btn_update">
                    <input type="hidden" value="edit_holiday_settings" id="act" name="act"></td>
                </tr>
                <?php }?>
                <?php }?>
              </tbody>
            </table>
          </form>
          <script type="text/javascript">
 
          function validateForm5() {

                var x = document.forms["frm_holiday_settings"]["holiday"].value;
                
                      if (x == 0) {
                          alert("<?php echo _('Please choose')?>");
                          return false;
                      }

                
            }

           
    </script>

          <!--<script type="text/javascript" language="javascript">
      var frmValidator = new Validator("frm_holiday_settings");
      frmValidator.EnableMsgsTogether();
      frmValidator.setCallBack(validate_holiday);
      frmValidator.addValidation("holiday","selone_radio"," <?php //echo _('Please choose')?>");  
      </script>-->
        </div>
      </div>