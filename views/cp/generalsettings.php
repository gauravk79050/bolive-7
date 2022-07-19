<div style="padding: 0px; display: block;" class="inside">
        <div class="table">
          <input type="hidden" id="part" name="part">
          <!--this stores the p(for pickup),d(for delvery) or h according to the sections-->
          <form action="<?php echo base_url()?>cp/settings/general_settings" enctype="multipart/form-data" method="post" id="frm_general_settings" name="frm_general_settings" onsubmit="return validateForm()" >
            <input type="hidden" value="settings" name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW">
            <table cellspacing="0">
              <tbody>
        <tr>
                  <td class="textlabel"><?php echo _('Email Address')?></td>
                  <td><input type="email" value="<?php if($general_settings): echo $general_settings[0]->emailid; endif;?>" class="text short" id="emailid" name="emailid">
                    &nbsp;&nbsp;&nbsp;<a title="<?php echo _('Enter your email address you want to use to communicate with your customers')?>" href="#" class="help"><img width="16" height="16" src="<?php echo base_url();?>assets/cp/images/help.png"></a></td>
                </tr>
        
        <?php if( $this->company_role == 'master' || $this->company_role == 'super' || $this->company_role == 'sub' ) { ?>
        
        <?php $language_id = $general_settings[0]->language_id; 
              
              if(!empty($languages)){
               ?>
             <tr>
                             <td class="textlabel"><?php echo _('Change Language')?></td>
               <td>
                    <select name="language_id" id="language_id" style="margin:0px;padding:2px;">
                  <?php  foreach($languages as $l) {  ?>
                  <option value="<?php echo $l->id; ?>" <?php if($l->id==$language_id) { echo 'selected="selected"'; } ?>>
                     <?php echo $l->lang_name; ?>
                  </option>
                  <?php  } ?>
                  </select>
               </td>
             </tr>
             <?php
            }
        ?>
        <?php } ?>
        
        <?php if( $this->company_role == 'master' || $this->company_role == 'sub' ) { ?>
        <?php
          $language_value = $general_settings[0]->frontend_languages;
          $language_id = array();
          if($language_value != ''){
            $language_id = explode('_',$language_value);
          }
        ?>
        <tr>
                  <td class="textlabel"><?php echo _('Language in frontend')?></td>
                  <td>
                    <input type="checkbox" value="1" class="checkbox" id="lang_1" name="lang_1" <?php if(in_array('1',$language_id)):?>checked="checked"<?php endif;?> />
                    <span><?php echo _("English");?></span>
                    <input type="checkbox" value="1" class="checkbox" id="lang_2" name="lang_2" <?php if(in_array('2',$language_id)):?>checked="checked"<?php endif;?> />
                    <span><?php echo _("Dutch");?></span>
                    <input type="checkbox" value="1" class="checkbox" id="lang_3" name="lang_3" <?php if(in_array('3',$language_id)):?>checked="checked"<?php endif;?> />
                    <span><?php echo _("French");?></span>
                   </td>
                </tr>
        <?php } ?>
        
        <?php if( $this->company_role == 'master' || $this->company_role == 'super' ) { ?>
        <tr>
                  <td class="textlabel"><?php echo _('Hide INTRO as first page?')?></td>
                  <td><input type="checkbox" value="1" class="checkbox" id="hide_intro" name="hide_intro" <?php if($general_settings && $general_settings[0]->hide_intro == '1'):?>checked="checked"<?php endif;?> onclick="intro_show_hide(this)"/>
                    &nbsp;&nbsp;&nbsp;<a title="<?php echo _('Check this checkbox if you just want to show orders as the first page not intro')?>" href="#" class="help"><img width="16" height="16" src="<?php echo base_url();?>assets/cp/images/help.png"></a></td>
                </tr>
        <?php } ?>
        
        <?php if( $this->company_role == 'master' || $this->company_role == 'super' ) { ?>
                <tr>
                  <td class="textlabel"><?php echo _('Shop Put OFFLINE?')?></td>
                  <td><input type="checkbox" value="1" class="checkbox" id="shop_offline" name="shop_offline"<?php if($general_settings&&$general_settings[0]->shop_offline=='1'):?>checked="checked"<?php endif;?>>
                    &nbsp;&nbsp;&nbsp;<a title="<?php echo _('Check this checkbox if you just want to close the shop. The following text is then displayed on the website and customers can not make orders (eg on leave)')?>" href="#" class="help"><img width="16" height="16" src="<?php echo base_url();?>assets/cp/images/help.png"></a></td>
                </tr>
          <tr>
                  <td class="textlabel"><?php echo _('Offline Message')?></td>
                  <td><textarea style="width: 70%; height: 200px" type="textarea" id="shop_offline_message" name="shop_offline_message"><?php if($general_settings): echo $general_settings[0]->shop_offline_message;endif;?></textarea>
                  </td>
                </tr>
        <?php }?>
        
        <?php if( $this->company_role == 'master' || $this->company_role == 'super' ) { ?>
        <tr>
                  <td valign="top" class="textlabel"><?php echo _('Webshop visible')?></td>
                  <td><table width="100%" cellspacing="0" cellpadding="0" border="0" class="override">
                      <tbody>
                        <tr>
                          <td width="30" height="30"><input type="radio" checked="checked" value="1" id="shop_visible" name="shop_visible" <?php if($general_settings&&$general_settings[0]->shop_visible=='1'):?>checked="checked"<?php endif;?>></td>
                          <td><?php echo _('for all')?>&nbsp;&nbsp;&nbsp;<a title="<?php echo ('If the online store for all to see may be select this option')?>" href="#" class="help"><img width="16" height="16" src="<?php echo base_url()?>assets/cp/images/help.png"></a></td>
                        </tr>
                        <tr>
                          <td width="30" height="30"><input type="radio" value="0" id="shop_visible0" name="shop_visible" <?php if($general_settings&&$general_settings[0]->shop_visible=='0'):?>checked="checked"<?php endif;?>></td>
                          <td><?php echo _('Only clients I work with')?>&nbsp;&nbsp;&nbsp;<a title="<?php echo _('If you have activated this option to your existing or new customers this password by email. Please find below a posting that one sees')?>" href="#" class="help"><img width="16" height="16" src="<?php echo base_url()?>assets/cp/images/help.png"></a></td>
                        </tr>
                        <tr>
                          <td height="50" style="padding-left:100px" colspan="2"><?php echo _('Password')?>&nbsp;&nbsp;
                            <input type="text" value="<?php if($general_settings): echo $general_settings[0]->shop_password;endif;?>" class="text short" id="shop_password" name="shop_password" ></td>
                        </tr>
                        <tr>
                          <td colspan="2">&nbsp;</td>
                        </tr>
                        <tr>
                          <td colspan="2"><textarea style="width: 70%; height: 200px;" type="textarea" id="shop_visible_message" name="shop_visible_message"><?php if($general_settings): echo $general_settings[0]->shop_visible_message;endif;?></textarea>
                          </td>
                        </tr>
                      </tbody>
                    </table></td>
                </tr>
        <?php } ?>
        
        <?php if( $this->company_role == 'master' || $this->company_role == 'super' ) { ?>
                <tr>
                  <td valign="top" style="padding-top:10px" class="textlabel"><?php echo _('Show message frontend')?></td>
                  <td><table width="100%" border="0" class="override">
                      <tbody>
                        <tr>
                          <td valign="middle" height="30"><input type="checkbox" value="1" class="checkbox" id="show_message_front" name="show_message_front"<?php if($general_settings&&$general_settings[0]->show_message_front):?>checked="checked"<?php endif;?>>
                            <br></td>
                        </tr>
                        <tr>
                          <td><textarea style="width: 70%; height: 200px;" type="textarea" id="message_front" name="message_front"><?php if($general_settings): echo $general_settings[0]->message_front;endif;?></textarea></td>
                        </tr>
                        <tr>
                        <td style="padding-top: 10px;">
                          <span style="margin: 0px 50px; font-weight: bold;"><?php echo _("Text Color");?></span>
              <input type="text" class="text short colors" name="msg_front_txt_color" value="<?php if($general_settings){echo $general_settings[0]->msg_front_txt_color;}else{echo $front_msg_text_color;}?>" style="width: 100px; height: 30px;">
                        </td>
                        </tr>
                      </tbody>
                    </table>
          </td>
                </tr>
        <?php } ?>
                
        <?php if( $this->company_role == 'master' || $this->company_role == 'sub' ) { ?>
                <tr>
                  <td class="textlabel"><?php echo _('Pickup services')?></td>
                  <td><table width="100%" cellspacing="0" cellpadding="0" border="0" class="override">
                      <tbody>
                        <tr>
                          <td width="150"><input type="checkbox" onClick="show_hide_pickup();" value="1" class="checkbox" id="pickup_service" name="pickup_service" <?php if($general_settings&&$general_settings[0]->pickup_service):?>checked="checked"<?php endif;?>></td>
                          <td width="130" class="textlabel"><?php echo _('Delivery services')?></td>
                          <td><input type="checkbox"  onClick="show_hide_delivery();" value="1" class="checkbox" id="delivery_service" name="delivery_service" <?php if($general_settings&&$general_settings[0]->delivery_service):?>checked="checked"<?php endif;?>></td>
                        </tr>
                      </tbody>
                    </table></td>
                </tr>
        <?php } ?>
        
        <?php if( $this->company_role == 'master' || $this->company_role == 'sub' ) { ?>
                <tr>
                  <td width="20%" class="textlabel"><?php echo _( 'Category-Wise Pick/Delivery')?></td>
                  <td style="padding-top:10px; vertical-align:middle"><input type="checkbox" value="1" class="checkbox" id="category_feature" name="category_feature" <?php if($general_settings&&$general_settings[0]->category_feature):?>checked="checked"<?php endif;?>>
                    &nbsp;&nbsp;&nbsp;<a title="<?php echo _('This function only if you check the customer an option for a particular product to pick up or deliver. This feature only when you turn in categories one or more categories of service both is selected (only works if you have checked COLLECTION AND DELIVERY (!).')?>" href="#" class="help"><img width="16" height="16" src="<?php echo base_url()?>assets/cp/images/help.png"></a> </td>
                </tr>
        <?php } ?>
        
        <?php if( $this->company_role == 'master' || $this->company_role == 'super' ) { ?>
                <tr>
                  <td width="20%" class="textlabel"><?php echo _('Disable Price')?></td>
                  <td style="padding-top:10px; vertical-align:middle"><input type="checkbox" value="1" class="checkbox" id="disable_price" name="disable_price" <?php if($general_settings&&$general_settings[0]->disable_price):?>checked="checked"<?php endif;?>>
                    &nbsp;&nbsp;&nbsp;<a title="<?php echo _('If you want to hide all the rates on the online store can set it here.')?>" href="#" class="help"><img width="16" height="16" src="<?php echo base_url()?>assets/cp/images/help.png"></a> </td>
                </tr>
        <?php } ?>
        
        <tr>
                  <td class="textlabel"><?php echo _('Hide price untill user login')?></td>
                  <td><input type="checkbox" value="1" class="checkbox" id="hide_price_login" name="hide_price_login"<?php if($general_settings&&$general_settings[0]->hide_price_login=='1'):?>checked="checked"<?php endif;?>>
                    &nbsp;&nbsp;&nbsp;<a title="<?php echo _('Check this checkbox if you just want to hide price untill user do not login.')?>" href="#" class="help"><img width="16" height="16" src="<?php echo base_url();?>assets/cp/images/help.png"></a></td>
                </tr>
        
                <tr>
                  <td class="save_b" colspan="2"><input type="submit" value="<?php echo _('UPDATE');?>" class="submit" id="btn_update" name="btn_update">
                    <input type="hidden" value="edit_general_setting" id="act" name="act"></td>
                </tr>
              </tbody>
            </table>
          </form>
          
          <script>

            function validateForm() {
                var y = document.forms["frm_general_settings"]["shop_password"].value;
                
                var shop_visible = document.frm_general_settings.shop_visible[1].checked;
                var x = document.forms["frm_general_settings"]["emailid"].value;
                var atpos = x.indexOf("@");
                var dotpos = x.lastIndexOf(".");
                if (atpos<1 || dotpos<atpos+2 || dotpos+2>=x.length) {
                    alert("<?php echo _("Not a valid e-mail address")?>");
                    return false;
                }
                if(shop_visible)
                {
                  if (y == null || y == "") 
                    {
                    alert("<?php echo _("Password cannot be empty")?>");
                    return false;
                    }
                }
              }
</script>


          <!-- <script type="text/javascript" language="javascript">
      var frmValidator = new Validator("frm_general_settings");
      frmValidator.EnableMsgsTogether();
      frmValidator.setCallBack(validate_mess1);
      frmValidator.addValidation("emailid","req","<?php //echo _('Please enter a valid e-mail address please give')?>");
      frmValidator.addValidation("emailid","email","<?php //echo _('Please enter a valid e-mail address please give')?>");        
      //frmValidator.addValidation("pay_option","selone_radio"," <?php //echo _('Please choose one payment option')?>");
      
      function validate_mess1(result){
        if(result == true){ 
          var shop_visible = document.frm_general_settings.shop_visible[1].checked;
          if(shop_visible){
            var password = document.getElementById('password');
            if(password.value == ""){
              alert("<?php //echo _('Please enter a password')?>");
              password.focus();
              return false;
            }
      
            var message_shop_visible = tinyMCE.get('shop_visible_message').getContent();
            if(message_shop_visible == ""){
              alert("<?php //echo _('Please enter a message to give to your customers')?>");
              return false;
            }
          }
          return true;
        }else{
          return true;
        }       
        return false;
      }
    </script>-->


        </div>
      </div>