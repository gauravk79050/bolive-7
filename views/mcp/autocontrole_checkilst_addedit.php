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
                  if( !empty( $checklist_detail ) ){
                    $url = 'updateChecklist';
                  }else{
                    $url = 'checklist_add_edit';
                  }
                  echo form_open_multipart("mcp/autocontrole/".$url."",array('method'=>"post" ,'id'=>"frm_autocontrole_checklist_addedit", 'name'=>"frm_autocontrole_checklist_addedit"));
                  if( !empty( $checklist_detail ) ){ ?>
                    <input type="hidden" name="id" value="<?php echo $checklist_detail[ 'id' ] ?>" >
                  <?php } ?>
                  <table width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                      <tr>
                        <td align="center" style="padding:15px 0px 10px 0px">
                          <table width="98%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/bg.jpg) left top repeat-x;" class="page_caption">
                            <tbody>
                              <tr>
                                  <td width="94%" align="left"><h3><?php echo _('Checklist'); ?>&nbsp;</h3></td>
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
                                $domain_id              = '';
                                $question               = '';
                                $question_fr            = '';
                                $question_dch           = '';
                                $subtype_ids            = '';
                                $ref                    = '';
                                $easing_measure         = '';
                                $connected_tasks        = '';
                                $additional_info        = '';
                                $additional_info_dch    = '';
                                $additional_info_fr     = '';
                                $company_type_id        = array();
                                if( !empty( $checklist_detail ) ){
                                  $domain_id                = $checklist_detail[ 'domain_id' ];
                                  $question                 = $checklist_detail[ 'question' ];
                                  $question_fr              = $checklist_detail[ 'question_fr' ];
                                  $question_dch             = $checklist_detail[ 'question_dch' ];
                                  $additional_info_dch      = $checklist_detail[ 'additional_info_dch' ];
                                  $additional_info          = $checklist_detail[ 'additional_info' ];
                                  $additional_info_fr       = $checklist_detail[ 'additional_info_fr' ];
                                  $company_type_id          = $checklist_detail[ 'company_type' ];
                                  if( !empty( $company_type_id ) ) {
                                    $company_type_id = json_decode( $company_type_id );
                                  }
                                  $ref              = $checklist_detail[ 'ref' ];
                                  $easing_measure   = $checklist_detail[ 'easing_measure' ];
                                  $connected_tasks  = $checklist_detail[ 'connected_tasks' ] != '' ? json_decode( $checklist_detail[ 'connected_tasks' ] ,true ) : array( );
                                }
                               ?>
                              <tr>
                                <td height="20" bgcolor="#003366" align="left" style="padding-left:10px;" class="whiteSmallBold" colspan="6"><?php echo _('Add Checklist'); ?></td>
                              </tr>
                              <tr>
                                <td height="10" colspan="2"></td>
                              </tr>
                              <tr>
                                <td height="30">
                                  <?php echo _('Domain'); ?><span class="red_star">*</span> :
                                </td>
                                <td>
                                  <select  class="textbox" name=" domain_id" required>
                                    <option value=""><?php echo _( 'Select Domain' ); ?></option>
                                    <?php
                                    if( !empty( $domains ) ){ 
                                      foreach ( $domains as $key => $domain ) {
                                        if( $domain[ 'id' ] == $domain_id  ){ ?>
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
                                   <?php echo _( 'Question' ); ?> <span class="red_star">*</span>:
                                </td>
                                  <td height="30">
                                   <textarea name="question_dch" class="textarea_auto" cols="34" rows="8" required="required" placeholder="Dutch"><?php echo $question_dch; ?></textarea>
                                </td>
                                <td height="30">
                                   <textarea name="question_fr"  class="textarea_auto" cols="34" rows="8" required="required" placeholder="French"><?php echo $question_fr; ?></textarea>
                                </td>
                                 <td height="30">
                                   <textarea name="question" class="textarea_auto" cols="34" rows="8" required="required" placeholder="English"><?php echo $question; ?></textarea>
                                </td>
                              </tr>
                              <tr>
                                <td height="30">
                                   <?php echo _( 'Additional info' ); ?>:
                                </td>
                                  <td height="30">
                                   <textarea name="additional_info_dch" class="textarea_auto" cols="34" rows="8" placeholder="Dutch"><?php echo $additional_info_dch; ?></textarea>
                                </td>
                                <td height="30">
                                   <textarea name="additional_info_fr"  class="textarea_auto" cols="34" rows="8" placeholder="French"><?php echo $additional_info_fr; ?></textarea>
                                </td>
                                 <td height="30">
                                   <textarea name="additional_info" class="textarea_auto" cols="34" rows="8" placeholder="English"><?php echo $additional_info; ?></textarea>
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
                                  <input type="text" name="ref" value="<?php echo $ref; ?>">
                                </td>
                              </tr>
                              <tr>
                                <td height="30">
                                  <?php echo _('Easing Measure'); ?>:
                                </td>
                                <td>
                                  <input type="checkbox" style="margin: 0px!important" name="easing_measure" value="1" <?php if( $easing_measure == '1' ){ echo "checked='checked'"; } ?>>
                                  <input type="hidden" value='<?php echo $task_name_var; ?>' class="all_tasks_list">
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
                                    <?php echo _('Connected TASKS'); ?><span class="red_star">*</span> :
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
                                                  <td align="right" style="padding-right:48px">&nbsp;</td>
                                                  <td colspan="2">
                                                    <?php
                                                      if( isset( $checklist_detail ) ){
                                                        echo form_submit( array( 'id'=>'edit','name'=>'action','value'=>'EDIT CHECKLIST','class'=>'btnWhiteBack add_pre_defined','onClick'=>"return Checkempty()") );
                                                      }else{
                                                        echo form_submit(array('id'=>'add','name'=>'action','value'=>'ADD CHECKLIST','class'=>'btnWhiteBack add_pre_defined','onClick'=>"return Checkempty()") );
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