
<div style="padding: 0px; display: block;" class="inside">
    <div class="table">
        <form action="<?php echo base_url()?>cp/settings/labeler" enctype="multipart/form-data" method="post" id="frm_labeler" name="frm_labeler">
                <input type="hidden" value="settings" name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW">
                <br/>
                <table cellspacing="0" class="override">
                    <tbody>
                      
                        <tr>
                  <td class="textlabel" width="22%" valign="top"><?php echo _('Upload Labeler Logo')?></td>
                          <td style="padding-right:00px">
                            <div id="uploaded_image1"></div>
	                            <input type="hidden" id="x" name="x" />
			                    <input type="hidden" id="y" name="y" />
			                    <input type="hidden" id="w" name="w" />
			                    <input type="hidden" id="h" name="h" />
		                    <div>
                              <a href="javascript:;" class="thickboxed_lab_logo" attr_id="1" style="text-decoration: none;"><input type="button" name="upload_img" id="upload_img" value="<?php echo _("Upload Image Here");?>" /></a>
                            </div>
                          </td>
                      </tr>
                      
                      <?php if($general_settings && !empty($general_settings[0]->labeler_logo)){?>
                        <tr>
                          <td>&nbsp;</td>
                         <td>
                            <img alt="labeler logo" src="<?php echo base_url().'assets/cp/labeler_logo/'.$general_settings[0]->labeler_logo?>" style="height:150px">
                          </td>
                       </tr>
                      <?php } ?>
                    <tr>
                          <td colspan="2" class="save_b"><input type="submit" name="btn_update" id="btn_update" class="submit" value="<?php echo _('UPDATE');?>"></td>
                          <input type="hidden" name="act" id="act" value="edit_labeler_settings">
                      </tr>
                    </tbody>
                  </table>
              </form>
           </div>
      </div>