<div style="padding: 0px; display: block;" class="inside">
        <div class="table">
          <form action="<?php echo base_url()?>cp/settings/termsandConditions" enctype="multipart/form-data" method="post" id="frm_tnc" name="frm_tnc" onsubmit="return validate_mess_tc()">
            <input type="hidden" value="settings" name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW">
            <br>
            <table cellspacing="0" class="override">
              <tbody>
               <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
               </tr> 
                <tr>
                  <td style="padding-left:20px" width="30%" class="textlabel">&nbsp;</td>
                  <td><textarea style="width: 550px; height: 500px" type="textarea" id="tnc_txt" name="tnc_txt"><?php  if($general_settings): echo $general_settings[0]->tnc_txt;endif;?></textarea></td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>                
                <tr>
                  <td class="save_b" colspan="2"><input type="submit" value="<?php echo _('UPDATE');?>" class="submit" id="btn_update" name="btn_update">
                  </td>
                  <input type="hidden" value="edit_tnc_settings" id="act" name="act">
                </tr>
              </tbody>              
            </table>
          </form>
          <script type="text/javascript" language="javascript">
      function validate_mess_tc(){
           
          var ok_msg = tinyMCE.get('tnc_txt').getContent();
          if(ok_msg == ""){
            alert("<?php echo _('please Enter Terms and Conditions')?>");
            return false;
          }
        
          return true;
        }    
   
    </script>
        </div>
      </div>