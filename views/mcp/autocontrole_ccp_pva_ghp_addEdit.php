<?php 
$task_name_var = '';
if( !empty( $allTask ) ){ 
      $task_name_var = json_encode( array_values( $allTask ) );
 } ?>
<script type="text/javascript">
  var  select_txt   = "<?php echo _('Select'); ?>";
  var  cant_delete  = "<?php echo _('Cant Delete this'); ?>";
</script>
 <div style="width:100%">
    <table width="100%" cellspacing="0" cellpadding="0" border="0">
      <tbody>
        <tr>
          <td valign="top" align="center">
          <table width="98%" cellspacing="0" cellpadding="0" border="0">
            <tbody>
              <tr>
                <td valign="middle" align="center" style="border:#8F8F8F 1px solid;">
				          <?php
                  if( strpos( current_url(), 'rp' ) > -1 ) {
                    $url = 'rp/autocontrole/ccp_pva_ghp_add_edit';
                  } else {
                    $url = 'mcp/autocontrole/ccp_pva_ghp_add_edit';
                  }
                  echo form_open_multipart( $url."",array('method'=>"post" ,'id'=>"frm_autocontrole_checklist_addedit", 'name'=>"frm_autocontrole_checklist_addedit"));
                  if( !empty( $ccp_pva_ghp_data ) ){ ?>
                    <input type="hidden" name="id" value="<?php echo $ccp_pva_ghp_data[ 'id' ] ?>" >
                  <?php } ?>
                  <input type="hidden" name="country_code" value="<?php echo $country_code ?>" >
                  <table width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                      <tr>
                        <td align="center" style="padding:15px 0px 10px 0px">
                          <table width="98%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/bg.jpg) left top repeat-x;" class="page_caption">
                            <tbody>
                              <tr>
                                  <td width="94%" align="left"><h3><?php echo _('CCP PVA GHP'); ?>&nbsp;</h3></td>
                                  <td width="3%" align="right"></td>
                                  <td width="3%" align="left">
                                    <div class="icon_button">
                                      <img width="16" height="16" border="0" style="cursor:pointer" onclick="javascript:history.back();" title="<?php echo _('Go Back'); ?>" alt="Go Back" src="<?php echo base_url();?>assets/mcp/images/undo.jpg">
                                    </div>
                                  </td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                      <tr>
                        <td align="center" style="padding-bottom:15px">
                          <table width="98%" cellspacing="0" cellpadding="5" border="0" align="center" style="border:1px solid #003366; text-align:left;">
                            <tbody>
                              <?php
                                $processtep           = '';
                                $processtep_dch       = '';
                                $processtep_fr        = '';
                                $danger               = '';
                                $danger_fr            = '';
                                $danger_dch           = '';
                                $action_dch           = '';
                                $action_fr            = '';
                                $action               = '';
                                $ccp_pva_ghp          = '';
                                $criteria_dch         = '';
                                $criteria_fr          = '';
                                $criteria             = '';
                                $ref                  = '';
                                $connected_tasks      = '';
                                $kind_of_danger       = array();
                                $company_type_id      = array();
                                if( !empty( $ccp_pva_ghp_data ) ){
                                  $processtep         = $ccp_pva_ghp_data[ 'processtep' ];
                                  $processtep_dch     = $ccp_pva_ghp_data[ 'processtep_dch' ];
                                  $processtep_fr      = $ccp_pva_ghp_data[ 'processtep_fr' ];
                                  $kind_of_danger     = ( $ccp_pva_ghp_data[ 'kind_of_danger' ] != '' ? json_decode( $ccp_pva_ghp_data[ 'kind_of_danger' ], true ) : array() ) ;
                                  $danger             = $ccp_pva_ghp_data[ 'danger' ];
                                  $danger_fr          = $ccp_pva_ghp_data[ 'danger_fr' ];
                                  $danger_dch         = $ccp_pva_ghp_data[ 'danger_dch' ];
                                  $action_dch         = $ccp_pva_ghp_data[ 'action_dch' ];
                                  $action_fr          = $ccp_pva_ghp_data[ 'action_fr' ];
                                  $action             = $ccp_pva_ghp_data[ 'action' ];
                                  $ccp_pva_ghp        = $ccp_pva_ghp_data[ 'ccp_pva_ghp' ];
                                  $criteria_dch       = $ccp_pva_ghp_data[ 'criteria_dch' ];
                                  $criteria_fr        = $ccp_pva_ghp_data[ 'criteria_fr' ];
                                  $criteria           = $ccp_pva_ghp_data[ 'criteria' ];
                                  $company_type_id    = $ccp_pva_ghp_data[ 'company_type' ];
                                  if( !empty( $company_type_id ) ) {
                                    $company_type_id = json_decode( $company_type_id );
                                  }
                                  $ref              = $ccp_pva_ghp_data[ 'ref' ];
                                  $connected_tasks  = $ccp_pva_ghp_data[ 'connected_tasks' ] != '' ? json_decode( $ccp_pva_ghp_data[ 'connected_tasks' ] ,true ) : array( );
                                }
                               ?>
                              <tr>
                                <td height="20" bgcolor="#003366" align="left" style="padding-left:10px;" class="whiteSmallBold" colspan="6"><?php echo _('CCP PVA GHP'); ?></td>
                              </tr>
                              <tr>
                                <td height="10" colspan="2"></td>
                              </tr>
                              <tr>
                                <td height="30">
                                  <input type="hidden" value='<?php echo $task_name_var; ?>' class="all_tasks_list">
                                  <?php echo _('Processtep'); ?>:
                                </td>
                                <td>
                                  <?php echo _( "Dutch" );?> :
                                  <input type="text" name="processtep_dch" class="textbox" value="<?php echo $processtep_dch; ?>">
                                </td>
                                <td>
                                  <?php echo _( "French" );?> :
                                  <input type="text" name="processtep_fr" class="textbox" value="<?php echo $processtep_fr; ?>">
                                </td>
                                <td>
                                  <?php echo _( "English" );?> :
                                  <input type="text" name="processtep" class="textbox" value="<?php echo $processtep; ?>">
                                </td>
                              </tr>
                              <tr>
                                <td height="30">
                                  <?php echo _('kind of danger'); ?>:
                                </td>
                                <td>
                                  <select  class="textbox" name="kind_of_danger[]" multiple="" >
                                    <option value=""><?php echo _( 'Select Domain' ); ?></option>
                                    <option <?php if( in_array( '1', $kind_of_danger ) ){ echo "selected='selected'"; } ?> value="1"><?php echo _( 'Fysisch' ); ?></option>
                                    <option <?php if( in_array( '2', $kind_of_danger ) ){ echo "selected='selected'"; } ?> value="2"><?php echo _( 'Chemisch' ); ?></option>
                                    <option <?php if( in_array( '3', $kind_of_danger ) ){ echo "selected='selected'"; } ?> value="3"><?php echo _( 'Microbiologisch' ); ?></option>
                                    <option <?php if( in_array( '4', $kind_of_danger ) ){ echo "selected='selected'"; } ?> value="4"><?php echo _( 'Allergenen' ); ?></option>
                                  </select>
                                </td>
                              </tr>
                              <tr>
                                <td height="30">
                                   <?php echo _( 'Danger' ); ?><span class="red_star">*</span> :
                                </td>
                                  <td height="30">
                                   <textarea name="danger_dch" class="textarea_auto" cols="34" rows="8" required="required" placeholder="Dutch"><?php echo $danger_dch; ?></textarea>
                                </td>
                                <td height="30">
                                   <textarea name="danger_fr"  class="textarea_auto" cols="34" rows="8" required="required" placeholder="French"><?php echo $danger_fr; ?></textarea>
                                </td>
                                 <td height="30">
                                   <textarea name="danger" class="textarea_auto" cols="34" rows="8" required="required" placeholder="English"><?php echo $danger; ?></textarea>
                                </td>
                              </tr>
                              <tr>
                                <td height="30">
                                   <?php echo _( 'Action' ); ?><span class="red_star">*</span> :
                                </td>
                                  <td height="30">
                                   <textarea name="action_dch" class="textarea_auto" cols="34" rows="8" required="required" placeholder="Dutch"><?php echo $action_dch; ?></textarea>
                                </td>
                                <td height="30">
                                   <textarea name="action_fr"  class="textarea_auto" cols="34" rows="8" required="required" placeholder="French"><?php echo $action_fr; ?></textarea>
                                </td>
                                 <td height="30">
                                   <textarea name="action" class="textarea_auto" cols="34" rows="8" required="required" placeholder="English"><?php echo $action; ?></textarea>
                                </td>
                              </tr>
                              <tr>
                                <td height="30">
                                  <?php echo _('CCP PVA GHP'); ?><span class="red_star">*</span> :
                                </td>
                                <td>
                                  <select  class="textbox" name="ccp_pva_ghp" required="required" >
                                    <option value="ccp"><?php echo _( 'CCP' ); ?></option>
                                    <option value="pva"><?php echo _( 'PVA' ); ?></option>
                                    <option value="ghp"><?php echo _( 'GHP' ); ?></option>
                                    <?php
                                    if( !empty( $domains ) ){ 
                                      foreach ( $domains as $key => $domain ) {
                                        if( $domain[ 'id' ] == $ccp_pva_ghp  ){ ?>
                                          <option value="<?php echo $domain[ 'id' ]; ?>" selected="selected" >
                                            <?php echo $domain[ 'domain_name' ]; ?>
                                          </option>
                                        <?php }else{ ?>
                                          <option value="<?php echo $domain[ 'id' ]; ?>" >
                                            <?php echo $domain[ 'domain_name' ]; ?>
                                          </option>
                                        <?php }
                                      }
                                    } ?>
                                  </select>
                                </td>
                              </tr>
                              <tr>
                                <td height="30">
                                   <?php echo _( 'Criteria' ); ?><span class="red_star">*</span> :
                                </td>
                                  <td height="30">
                                   <textarea name="criteria_dch" class="textarea_auto" cols="34" rows="8" required="required" placeholder="Dutch"><?php echo $criteria_dch; ?></textarea>
                                </td>
                                <td height="30">
                                   <textarea name="criteria_fr"  class="textarea_auto" cols="34" rows="8" required="required" placeholder="French"><?php echo $criteria_fr; ?></textarea>
                                </td>
                                 <td height="30">
                                   <textarea name="criteria" class="textarea_auto" cols="34" rows="8" required="required" placeholder="English"><?php echo $criteria; ?></textarea>
                                </td>
                              </tr>
                              <tr>
                                <td height="30">
                                  <?php echo _('Company Type'); ?><span class="red_star">*</span> :
                                </td>
                                <td>
                                  <select  class="select_subtype textbox" name="company_type[]" multiple="" required>
                                   <?php 
                                    if( !empty( $company_type ) ){
                                      foreach ( $company_type as $key => $value ) {
                                        if( in_array( $value[ 'id' ], $company_type_id ) ){ ?>
                                          <option value="<?php echo $value[ 'id' ]; ?>" selected="selected" >
                                            <?php echo $value[ 'company_type_name' ]; ?>
                                          </option>
                                        <?php }else{ ?>
                                          <option value="<?php echo $value[ 'id' ]; ?>" >
                                            <?php echo $value[ 'company_type_name' ]; ?>
                                          </option>
                                        <?php }
                                      }
                                    } ?>
                                  </select>
                                </td>
                              </tr>
                              <tr>
                                <td height="30">
                                  <?php echo _('Ref'); ?>:
                                </td>
                                <td>
                                  <input type="text" name="ref" class="textbox" value="<?php echo $ref; ?>">
                                </td>
                              </tr>
                              <?php 
                              if ( !empty( $connected_tasks ) ) {
                                foreach ( $connected_tasks as $key => $value) { ?>
                                     <tr id="connected_task_tr-<?php echo $key; ?>" class="connected_task_row">
                                      <td height="30">
                                        <?php if( $key == 0 ){ ?>
                                          <?php echo _('Connected TASKS'); ?>:
                                        <?php } ?>
                                      </td>
                                      <td>

                                        <select name="connected_tasks[]" class="textbox" style="width:100%">
                                          <option value=""> --<?php echo _('Select'); ?>--</option>
                                          <?php 
                                            if( !empty( $allTask ) ){
                                              foreach ($allTask as $key => $task ) { 
                                                if( $task[ 'id' ] ==  $value ){ ?>
                                                  <option value="<?php echo $task[ 'id' ]; ?>" selected="selected">
                                                    <?php echo $task[ 'predifined_name' ]; ?>
                                                  </option>
                                                <?php }else{ ?>
                                                     <option value="<?php echo $task[ 'id' ]; ?>">
                                                        <?php echo $task[ 'predifined_name' ]; ?>
                                                      </option>
                                                <?php } 
                                              }
                                            } ?>      
                                      </td>
                                      <td>
                                        <span><input type="button" class="add_connected_task" value="+" style="border-radius: 0;height: 21px;"></span>
                                        <span><input type="button" class="remove_connected_task" value="-" style="border-radius: 0;height: 21px;width: 21px;"></span>
                                      </td>
                                    </tr>
                                <?php }
                                
                              }else{ ?>
                                <tr id="connected_task_tr-0" class="connected_task_row">
                                  <td height="30">
                                    <?php echo _('Connected TASKS'); ?> :
                                  </td>
                                  <td>

                                    <select name="connected_tasks[]" class="textbox" style="width:100%">
                                      <option value=""> --<?php echo _('Select'); ?>--</option>
                                      <?php if( !empty( $allTask ) ){
                                        foreach ($allTask as $key => $task ) { ?>
                                          <option value="<?php echo $task[ 'id' ]; ?>"><?php echo $task[ 'predifined_name' ]; ?></option>
                                        <?php }
                                        } ?>      
                                  </td>
                                  <td>
                                    <span><input type="button" class="add_connected_task" value="+" style="border-radius: 0;height: 21px;"></span>
                                    <span><input type="button" class="remove_connected_task" value="-" style="border-radius: 0;height: 21px;width: 21px;"></span>
                                  </td>
                                </tr>
                              <?php } ?>
                               <tr>
                                <td height="30" colspan="2">
                                  <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                    <tbody>
                                      <tr>
                                        <td valign="middle" height="50">
                                          <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tbody>
                                              <tr>
                                                  <td align="right" style="padding-right:74px">&nbsp;</td>
                                                  <td colspan="2">
                                                    <?php
                                                      if( isset( $ccp_pva_ghp_data ) ){
                                                        echo form_submit( array( 'id'=>'edit','name'=>'action_btn','value'=>'EDIT CCP PVA GHP','class'=>'btnWhiteBack add_pre_defined','onClick'=>"return Checkempty()") );
                                                      }else{
                                                        echo form_submit(array('id'=>'add','name'=>'action_btn','value'=>'ADD CCP PVA GHP','class'=>'btnWhiteBack add_pre_defined','onClick'=>"return Checkempty()") );
                                                      }
                                                    ?>
                                                  </td>
                                                </tr>
                                              </tbody>
                                          </table>
                                        </td>
                                      </tr>
                                    </tbody>
                                  </table>
                                </td>
                              </tr>
                              <tr>
                                <td height="10" colspan="2"></td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <?php echo form_close();?>
  		          </td>
              </tr>
            </tbody>
          </table>
         </td>
      </tr>
    </tbody>
  </table>
  <!-- end of main body -->
</div>