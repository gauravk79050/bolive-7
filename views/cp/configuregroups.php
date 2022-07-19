<div style="padding: 0px; display: block;" class="inside">
        <div class="table">
    
      <div id="pre" style="width:13%;float:left;">
        <table cellspacing="0">
              <tbody>
        <tr>
                  <td class="textlabel"><?php echo _("Display Order");?></td>
        </tr>
        <?php for ($grp_counter = 1; $grp_counter <= 10; $grp_counter++ ){?>
          <tr>
                      <td align="center" class="textlabel" style="height: 44px;">
                        <?php echo $grp_counter;?>
                      </td>
                  </tr>
              <?php }?>
                <tr>
                  <td>&nbsp;</td>
                </tr>
              </tbody>
            </table>
      </div>
      
      <div id="first_half" style="width:29%;float:left;">
          <form action="<?php echo base_url()?>cp/settings/configuregroups" enctype="multipart/form-data" method="post" id="frm_groups" name="frm_groups">
            <input type="hidden" value="settings" name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW">
            <table cellspacing="0">
              <tbody class="grp_sort">
                <?php $count=count($company_groups);?>
        <tr class="ui-state-disabled">
                  <td class="textlabel"><?php echo _('Per Unit Groups'); ?></td>
        </tr>
        <?php for ($grp_counter = 0; $grp_counter < 10; $grp_counter++ ){?>
          <tr>
                      <td align="center" class="textlabel">
                        <input type="text" value="<?php if(($count >= ($grp_counter+1))): echo $company_groups[$grp_counter]->group_name;endif; ?>" class="text medium" id="group_name[]" name="group_name[<?php if(($count >= ($grp_counter+1))): echo $company_groups[$grp_counter]->id;endif;?>]">
                        &nbsp;&nbsp;&nbsp;<img src="<?php echo base_url();?>assets/cp/images/move.png" width="16" style="vertical-align: middle; cursor: pointer;"/>
                      </td>
                  </tr>
              <?php }?>
                <tr>
                  <td style="padding:10px 0px 10px 10px" colspan="2"><input type="submit" value="<?php echo _('UPDATE');?>" class="submit" id="btn_update" name="btn_update">
                    <input type="hidden" value="group_edit" id="act" name="act">
          <input type="hidden" value="0" id="type" name="type">
          </td>
                </tr>
              </tbody>
            </table>
          </form>
      </div>
      
      <div id="middle" style="width:29%;float:left;">
          <form action="<?php echo base_url()?>cp/settings/configuregroups" enctype="multipart/form-data" method="post" id="frm_groups" name="frm_groups">
            <input type="hidden" value="settings" name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW">
            <table cellspacing="0">
              <tbody class="grp_sort">
                <?php $count=count($company_person_groups);?>
        <tr class="ui-state-disabled">
                  <td class="textlabel"><?php echo _('Per Person Groups'); ?></td>
        </tr>
        <?php for ($grp_counter = 0; $grp_counter < 10; $grp_counter++ ){?>
          <tr>
                      <td align="center" class="textlabel">
                        <input type="text" value="<?php if(($count >= ($grp_counter+1))): echo $company_person_groups[$grp_counter]->group_name;endif; ?>" class="text medium" id="group_person_name[]" name="group_person_name[<?php if(($count >= ($grp_counter+1))): echo $company_person_groups[$grp_counter]->id;endif;?>]">
                        &nbsp;&nbsp;&nbsp;<img src="<?php echo base_url();?>assets/cp/images/move.png" width="16" style="vertical-align: middle; cursor: pointer;"/>
                      </td>
                  </tr>
              <?php }?>
                <tr>
                  <td style="padding:10px 0px 10px 10px" colspan="2"><input type="submit" value="<?php echo _('UPDATE');?>" class="submit" id="btn_update" name="btn_update">
                    <input type="hidden" value="group_person_edit" id="act" name="act">
          <input type="hidden" value="2" id="type" name="type">
          </td>
                </tr>
              </tbody>
            </table>
          </form>
      </div>
      
      <div id="second_half" style="width:29%;float:left;">
          <form action="<?php echo base_url()?>cp/settings/configuregroups" enctype="multipart/form-data" method="post" id="frm_groups" name="frm_groups">
            <input type="hidden" value="settings" name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW">
            <table cellspacing="0">
              <tbody class="grp_sort">
                <?php $count=count($company_wt_groups);?>
        <tr class="ui-state-disabled">
                  <td class="textlabel"><?php echo _('Weight Groups'); ?></td>
        </tr>
        <?php for ($grp_counter = 0; $grp_counter < 10; $grp_counter++ ){?>
          <tr>
                      <td align="center" class="textlabel">
                        <input type="text" value="<?php if(($count >= ($grp_counter+1))): echo $company_wt_groups[$grp_counter]->group_name;endif; ?>" class="text medium" id="group_wt_name[]" name="group_wt_name[<?php if(($count >= ($grp_counter+1))): echo $company_wt_groups[$grp_counter]->id;endif;?>]">
                        &nbsp;&nbsp;&nbsp;<img src="<?php echo base_url();?>assets/cp/images/move.png" width="16" style="vertical-align: middle; cursor: pointer;"/>
                      </td>
                  </tr>
              <?php }?>
                <tr>
                  <td style="padding:10px 0px 10px 10px" colspan="2"><input type="submit" value="<?php echo _('UPDATE');?>" class="submit" id="btn_update" name="btn_update">
                    <input type="hidden" value="group_wt_edit" id="act" name="act">
          <input type="hidden" value="1" id="type" name="type">
          </td>
                </tr>
              </tbody>
            </table>
          </form>
      </div>
      
      <div style="clear:both;"></div>     
      
        </div>
      </div>