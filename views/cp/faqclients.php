 <div style="padding: 0px; display: block;" class="inside">
        <div class="table">
          <form action="<?php echo base_url()?>cp/settings/faqclients" enctype="multipart/form-data" method="post" id="frm_faq_clients" name="frm_faq_clients" onsubmit="return validate_mess_faq()">
            <input type="hidden" value="settings" name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW">
            <br>
            <table cellspacing="0" class="override">
              <tbody>
               <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
               </tr> 
               <tr>
                  <td width="20%" class="textlabel">&nbsp;</td>
                  <td style="padding-top:10px; vertical-align:middle">
                     <input type="checkbox" value="1" class="checkbox" id="faq_showhide" name="faq_showhide" <?php if($general_settings && $general_settings[0]->faq_showhide):?>checked="checked"<?php endif;?>>
                     &nbsp;&nbsp;<?php echo _("Show the questions in frontend");?>                    
                  </td>
                </tr>               
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td style="padding-left:20px" class="textlabel">&nbsp;</td>
                  <td><textarea style="width: 550px; height: 800px" type="textarea" id="faq_txt" name="faq_txt"><?php  if($general_settings): echo $general_settings[0]->faq_txt;endif;?></textarea></td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>                
                <tr>
                  <td class="save_b" colspan="2"><input type="submit" value="<?php echo _('UPDATE');?>" class="submit" id="btn_update" name="btn_update">
                  </td>
                  <input type="hidden" value="edit_faq_settings" id="act" name="act">
                </tr>
              </tbody>              
            </table>
          </form>
          <script type="text/javascript" language="javascript">
      
      function validate_mess_faq(){

          if(document.getElementById('faq_showhide').checked){
          var ok_msg = tinyMCE.get('faq_txt').getContent();
          if(ok_msg == ""){
            alert("<?php echo _('please Enter FAQ message')?>");
            return false;
          }
        }
          return true;
        }    

      
    </script>
        </div>
      </div>