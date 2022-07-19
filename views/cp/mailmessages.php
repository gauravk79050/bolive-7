<div style="padding: 0px; display: block;" class="inside">
        <div class="table">
          <form action="<?php echo base_url()?>cp/settings/mailmessages" enctype="multipart/form-data" method="post" id="frm_mailmessages" name="frm_mailmessages" onsubmit="return validateForm3()">
            <input type="hidden" value="settings" name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW">
            <br>
            <table cellspacing="0" class="override">
              <tbody>
                <tr>
                  <td style="padding-left:20px" class="textlabel"><?php echo _('Email Subject')?>&nbsp;&nbsp;&nbsp;<a title="<?php echo _('topic');?>" href="#" class="help"><img width="16" height="16" src="<?php echo base_url()?>assets/cp/images/help.png"></a></td>
                  <td><input type="text" value="<?php if($general_settings):echo $general_settings[0]->subject_emails;endif; ?>" class="text medium" id="subject_emails" name="subject_emails"></td>
                </tr>
                
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td style="padding-left:20px" class="textlabel"><?php echo _('Order success MAIL')?>&nbsp;&nbsp;&nbsp;<a title="<?php echo _('This is the first mail that the customer gets an order after he has done.')?>" href="#" class="help"><img width="16" height="16" src="<?php echo base_url()?>assets/cp/images/help.png"></a></td>
                  <td><textarea style="width: 550px; height: 200px" type="textarea" id="orderreceived_msg" name="orderreceived_msg"><?php  if($general_settings): echo $general_settings[0]->orderreceived_msg;endif;?>
</textarea></td>
                </tr>
                
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td width="210" style="padding-left:20px" class="textlabel"><?php echo _('OK MAIL')?>&nbsp;&nbsp;&nbsp;<a title="<?php echo _('If OK is clicked, the customer MAIL this mail. This tells the customer that you have read and approved the order');?>" href="#" class="help"><img width="16" height="16" src="<?php echo base_url()?>assets/cp/images/help.png"></a></td>
                  <td><textarea style="width: 550px; height: 200px" type="textarea" id="ok_msg" name="ok_msg"><?php  if($general_settings): echo $general_settings[0]->ok_msg;endif;?>
</textarea></td>
                </tr>
                
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td style="padding-left:20px" class="textlabel"><?php echo _('HOLD MAIL')?>&nbsp;&nbsp;&nbsp;<a title="<?php echo _('If you doubt the authenticity of the order or if anything is unclear please contact the customer through the order page on the name. If you click MAIL HOLD the customer receives this mail.')?>" href="#" class="help"><img width="16" height="16" src="<?php echo base_url()?>assets/cp/images/help.png"></a></td>
                  <td><textarea style="width: 550px; height: 200px" type="textarea" id="hold_msg" name="hold_msg"><?php  if($general_settings): echo $general_settings[0]->hold_msg; endif;?>
</textarea></td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td style="padding-left:20px" class="textlabel"><?php echo _('PICK Complete MAIL')?>&nbsp;&nbsp;&nbsp;<a title="<?php echo _('This mail is sent to the customer when you have clicked on MAIL COMPLETE (after you are sure the order is ready or will be at the appointed time).')?>" href="#" class="help"><img width="16" height="16" src="<?php echo base_url()?>assets/cp/images/help.png"></a></td>
                  <td><textarea style="width: 550px; height: 200px" type="textarea" id="completedpickup_msg" name="completedpickup_msg"><?php  if($general_settings): echo $general_settings[0]->completedpickup_msg;endif;?>
</textarea>
                  </td>
                </tr>
                
                <tr>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td style="padding-left:20px" class="textlabel"><?php echo _('Complete MAIL DELIVERY')?>&nbsp;&nbsp;&nbsp;<a title="<?php echo _('This mail is sent to the customer when you have clicked on MAIL COMPLETE (after you are sure the order at the appointed time will be delivered).')?>" href="#" class="help"><img width="16" height="16" src="<?php echo base_url()?>assets/cp/images/help.png"></a></td>
                  <td><textarea style="width: 550px; height: 200px" type="textarea" id="completeddelivery_msg" name="completeddelivery_msg"><?php  if($general_settings): echo $general_settings[0]->completeddelivery_msg;endif;?>
</textarea>
                  </td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td style="padding-left:20px" class="textlabel"><?php echo _('Payment cancelled MAIL')?>&nbsp;&nbsp;&nbsp;<a title="<?php echo _('Enter what you want to send in Payment cancelled mail')?>" href="#" class="help"><img width="16" height="16" src="<?php echo base_url()?>assets/cp/images/help.png"></a></td>
                  <td><textarea style="width: 550px; height: 200px" type="textarea" id="pay_can" name="pay_can"><?php  if($general_settings): echo $general_settings[0]->payment_cancel_msg;endif;?>
					</textarea>
                  </td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td style="padding-left:20px" class="textlabel"><?php echo _('Order cancelled MAIL')?>&nbsp;&nbsp;&nbsp;<a title="<?php echo _('Enter what you want to send in Order cancelled mail')?>" href="#" class="help"><img width="16" height="16" src="<?php echo base_url()?>assets/cp/images/help.png"></a></td>
                  <td><textarea style="width: 550px; height: 200px" type="textarea" id="ord_can" name="ord_can"><?php  if($general_settings): echo $general_settings[0]->order_cancel_msg;endif;?>
</textarea>
                  </td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td class="save_b" colspan="2"><input type="submit" value="<?php echo _('UPDATE');?>" class="submit" id="btn_update" name="btn_update">
                  </td>
                  <input type="hidden" value="edit_mail_messages" id="act" name="act">
                </tr>
              </tbody>
            </table>
          </form>
          <script type="text/javascript">
          function validateForm3(){
                var x = document.forms["frm_mailmessages"]["subject_emails"].value;
                	if (x == null || x=="" ){
                          alert("<?php echo _('Please provide a topic please')?>");
                          return false;
                    }
          }
    </script>
          <!--<script type="text/javascript" language="javascript">
      var frmValidator = new Validator("frm_mailmessages");
      frmValidator.EnableMsgsTogether();
      frmValidator.setCallBack(validate_mess);
      frmValidator.addValidation("subject_emails","req","<?php //echo _('Please provide a topic please')?>"); 
      function validate_mess(result){
        if(result == true){
          var ok_msg = tinyMCE.get('ok_msg').getContent();
          if(ok_msg == ""){
            alert("<?php// echo _('please Enter OK Mail message')?>");
            return false;
          }
          var hold_msg = tinyMCE.get('hold_msg').getContent();
          if(hold_msg == ""){
            alert("<?php// echo _('please enter HOLD Mail message')?>");
            return false;
          }
          var completeddelivery_msg = tinyMCE.get('completeddelivery_msg').getContent();
          if(completeddelivery_msg == ""){
            alert("<?php //echo _('please enter COMPLETE Mail message')?>");
            return false;
          }
          var completedpickup_msg = tinyMCE.get('completedpickup_msg').getContent();
          if(completedpickup_msg == ""){
            alert("<?php //echo _('please enter PICK Complete MAIL message')?>");
            return false;
          }
          var orderreceived_msg = tinyMCE.get('orderreceived_msg').getContent();
          if(orderreceived_msg == ""){
            alert("<?php //echo _('please enter Order Received  MAIL message')?>");
            return false;
          }
          return true;
        }else{
          return false;
        }         
      }
      $('.fadenext').click(function(){
        $(this).next('.fader').slideToggle("slow");
        $(this).next('.fader').css({'opacity':'1','display': 'block'});
        return false;
      }); 
    </script>-->
        </div>
      </div>