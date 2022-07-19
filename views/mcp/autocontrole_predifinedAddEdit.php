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
                  if( !empty( $predif ) ){
                    $url = 'updatePredifined';
                  }else{
                    $url = 'addeditPredifined';
                  }
                  echo form_open_multipart("mcp/autocontrole/".$url."",array('method'=>"post" ,'id'=>"frm_autocontrole_predifined_addedit", 'name'=>"frm_autocontrole_type_addedit"));
                  if( !empty( $predif ) ){ ?>
                    <input type="hidden" name="id" value="<?php echo $predif[ 'id' ] ?>" >
                  <?php } ?>
                  <table width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                      <tr>
                        <td align="center" style="padding:15px 0px 10px 0px">
                          <table width="98%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/bg.jpg) left top repeat-x;" class="page_caption">
                            <tbody>
                              <tr>
                                  <td width="94%" align="left"><h3><?php echo _('Add Predifined'); ?>&nbsp;</h3></td>
                                  <td width="3%" align="right"></td>
                                  <td width="3%" align="left">
                                    <div class="icon_button">
                                       <a title="Go Back" href="<?php echo base_url('mcp/autocontrole/predifined'); ?>"><img src="<?php echo base_url();?>assets/mcp/images/undo.jpg" alt="Go Back"></a>
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
                                  $type_id          = '';
                                  $name             = '';
                                  $name_fr          = '';
                                  $name_dch         = '';
                                  $description      = '';
                                  $description_fr   = '';
                                  $description_dch  = '';
                                  $frequency        = '';
                                  $most_frequently  = '';
                                  $education        = '';
                                  $education_fr     = '';
                                  $education_dch    = '';
                                  $company_type_id  = array();
                                  $object_ids       = array();
                                if( !empty( $predif ) ){
                                  $type_id          = $predif[ 'type_id' ];
                                  $name             = $predif[ 'name' ];
                                  $name_fr          = $predif[ 'name_fr' ];
                                  $name_dch         = $predif[ 'name_dch' ];
                                  $description      = $predif[ 'description' ];
                                  $description_fr   = $predif[ 'description_fr' ];
                                  $description_dch  = $predif[ 'description_dch' ];
                                  $education        = $predif[ 'education' ];
                                  $education_fr     = $predif[ 'education_fr' ];
                                  $education_dch    = $predif[ 'education_dch' ];
                                  $frequency        = $predif[ 'frequency' ];
                                  $most_frequently  = $predif[ 'most_frequently' ];
                                  $company_type_id  = $predif[ 'company_type' ];
                                  $object_ids       = $predif[ 'object_ids' ] ? json_decode( $predif['object_ids'] ) : array();
                                  if( !empty( $company_type_id ) ) {
                                    $company_type_id = explode( "#", $company_type_id );
                                  } 
                                } 
                               ?>
                              <tr>
                                <td height="20" bgcolor="#003366" align="left" style="padding-left:10px;" class="whiteSmallBold" colspan="6"><?php echo _('Add Predifined'); ?></td>
                              </tr>
                              <tr>
                                <td height="10" colspan="2"></td>
                              </tr>
                              <tr>
                                <td height="30">
                                  <?php echo _('Select Type'); ?><span class="red_star">*</span> :
                                </td>
                                <td>
                                  <select  class="select_type_autocontrole textbox" name="type_id" required>
                                    <option><?php echo _( 'Select Type' ); ?></option>
                                    <?php
                                    if( !empty( $types ) ){
                                      foreach ( $types as $key => $type ) {
                                        if( $type[ 'id' ] == $type_id  ){ ?>
                                          <option value="<?php echo $type[ 'id' ]; ?>" selected="selected" >
                                            <?php echo $type[ 'type_name' ]; ?>
                                          </option>
                                        <?php }else{ ?>
                                          <option value="<?php echo $type[ 'id' ]; ?>" >
                                            <?php echo $type[ 'type_name' ]; ?>
                                          </option>
                                        <?php }
                                      }
                                    } ?>
                                  </select>
                                </td>
                                <td></td>
                                <td style="text-align: right;">
                                    <?php if(isset($prev_n_nxt_id['prev_id']) && $prev_n_nxt_id['prev_id']!='' ) { ?>
                                  <a href="<?php echo base_url('mcp/autocontrole/addeditPredifined/'.$prev_n_nxt_id['prev_id']); ?>"><img src="<?php echo base_url(); ?>assets/images/left-arrow.png" title="Previous" alt="Previous"></a>
                                  <?php }
                                  if(isset($prev_n_nxt_id['next_id']) && $prev_n_nxt_id['next_id']!='' ) { ?>
                                  <a href="<?php echo base_url('mcp/autocontrole/addeditPredifined/'.$prev_n_nxt_id['next_id']); ?>"><img src="<?php echo base_url(); ?>assets/images/right-arrow.png" title="Next" alt="Next"></a>
                                  <?php } ?>
                                </td>

                              </tr>
                               <tr>
                                <td height="30">
                                  <?php echo _('Select Company Type'); ?>:
                                </td>
                                <td>
                                  <select class="select_type_autocontrole textbox" multiple="multiple" name="company_type[]">
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
                                  <?php echo _( 'Name' ); ?> <span class="red_star">*</span>:
                                </td>
                                  <td>
                                  <?php echo _( 'Dutch' ) ?>:
                                  <input type="text" class="textbox" name="name_dch" id="name_dch" required="required" placeholder="Dutch" value="<?php echo $name_dch; ?>">
                                </td>
                                <td>
                                  <?php echo _( 'French' ) ?>:
                                  <input type="text"  class="textbox" name="name_fr" id="name_fr" required="required" placeholder="French" value="<?php echo $name_fr; ?>">
                                </td>
                                <td>
                                  <?php echo _( 'English' ) ?>:
                                  <input type="text" class="textbox" name="name" id="name" required="required" placeholder="English" value="<?php echo $name; ?>">
                                </td>
                              </tr>
                              <tr>
                                <td height="30">
                                   <?php echo _( 'Description' ); ?> <span class="red_star">*</span>:
                                </td>
                                  <td height="30">
                                   <textarea name="description_dch" class="textarea_auto" cols="34" rows="8" required="required" placeholder="Dutch"><?php echo $description_dch; ?></textarea>
                                </td>
                                <td height="30">
                                   <textarea name="description_fr"  class="textarea_auto" cols="34" rows="8" required="required" placeholder="French"><?php echo $description_fr; ?></textarea>
                                </td>
                                 <td height="30">
                                   <textarea name="description" class="textarea_auto" cols="34" rows="8" required="required" placeholder="English"><?php echo $description; ?></textarea>
                                </td>


                              </tr>
                               <tr>
                                <td height="30">
                                   <?php echo _( 'Education' ); ?> <span class="red_star">*</span>:
                                </td>
                                  <td height="30">
                                   <textarea name="education_dch" class="textarea_auto" cols="34" rows="8" required="required" placeholder="Dutch"><?php echo $education_dch; ?></textarea>
                                </td>
                                <td height="30">
                                   <textarea name="education_fr"  class="textarea_auto" cols="34" rows="8" required="required" placeholder="French"><?php echo $education_fr; ?></textarea>
                                </td>
                                 <td height="30">
                                   <textarea name="education" class="textarea_auto" cols="34" rows="8" required="required" placeholder="English"><?php echo $education; ?></textarea>
                                </td>


                              </tr>
                              <tr>
                                <td height="30">
                                   <?php echo _( 'Icon' ); ?> <span class="red_star"></span>:
                                </td>
                                <td height="30">
                                    <input type="file" name="upload_icon">
                                    <?php if(!empty( $predif['icon'] ) ){ ?>
                                      <img src="<?php echo base_url(); ?>assets/images/predifineAuto_img/<?php echo $predif[ 'icon' ];?>" width="80" height="80">
                                     <?php } ?>
                                </td>
                              </tr>
                              <tr>
                                <td height="30">
                                  <?php echo _('Default Frequency'); ?>:
                                </td>
                                <td>
                                  <select  class="select_type_autocontrole textbox" name="frequency">
                                    <option value=''><?php echo _( 'Select Frequency' ); ?></option>
                                    <option value="daily" <?php if( $frequency === 'daily' ){ echo "selected"; }?> ><?php echo _('Daily'); ?></option>
                                     <option value="daily_after_use" <?php if( $frequency === 'daily_after_use' ){ echo "selected"; }?> ><?php echo _('Daily After use'); ?></option>
                                    <option value="weekly" <?php if( $frequency === 'weekly' ){ echo "selected"; }?> ><?php echo _('Weekly'); ?></option>
                                    <option value="monthly" <?php if( $frequency === 'monthly' ){ echo "selected"; }?> ><?php echo _('Monthly'); ?></option>
                                    <option value="year" <?php if( $frequency === 'year' ){ echo "selected"; }?> ><?php echo _('Year'); ?></option>
                                  </select>
                                </td>
                              </tr>
                              <tr>
                                <td height="30">
                                  <?php echo _('Most frequently used'); ?>:
                                </td>
                                <td>
                                  <input type="hidden" name="most_frequently" value="0"/>
                                  <input type="checkbox" name="most_frequently" value="1" <?php if( $most_frequently == '1' ){ echo "checked"; }?> />
                                </td>
                              </tr>
                              <tr>
                                <td>
                                  <?php echo _( 'Related Object' );?>
                                </td>
                                <td>
                                  <select class="textbox" name="objects[]" multiple="" required="">
                                      <?php if( !empty( $objects ) ) {
                                        $name = 'object_name'.get_lang( $_COOKIE['locale'] );
                                        foreach ( $objects as $key => $value ) {
                                          if( in_array( $value[ 'o_id' ], $object_ids ) ){?>
                                            <option value="<?php echo $value[ 'o_id' ];?>" selected>
                                              <?php echo $value[ $name ];?>
                                            </option>
                                          <?php 
                                          } else {?>
                                            <option value="<?php echo $value[ 'o_id' ];?>">
                                              <?php echo $value[ $name ];?>
                                            </option>
                                        <?php 
                                          }
                                        }
                                      }?>
                                  </select>
                                </td>
                              </tr>
                               <tr>
                                <td height="30" colspan="2">
                                  <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                    <tbody>
                                      <tr>
                                        <td valign="middle" height="50">
                                          <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tbody>
                                              <tr>
                                                  <td width="18%" align="right" style="padding-right:25px">&nbsp;</td>
                                                  <td>
                                                    <?php
                                                      if( isset( $predif ) ){
                                                        echo form_submit( array( 'id'=>'edit','name'=>'action','value'=>'EDIT Predifined','class'=>'btnWhiteBack add_pre_defined','onClick'=>"return Checkempty()") );
                                                        echo form_submit( array( 'id'=>'edit_nd_next','name'=>'action','value'=>'Save and next','class'=>'btnWhiteBack add_pre_defined','onClick'=>"return Checkempty()") );
                                                      }else{
                                                        echo form_submit(array('id'=>'add','name'=>'action','value'=>'ADD Predifined','class'=>'btnWhiteBack add_pre_defined','onClick'=>"return Checkempty()") );
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