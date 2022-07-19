
<script type="text/javascript">
  var  delete_object        = "<?php echo _( 'Would you like to delete Object ?' ) ?>";
  var  delete_error         = "<?php echo _( 'Error occured please try again' ) ?>";
  var select                = '<?php echo _( "Select" );?>';
  var successfully_updated  = '<?php echo _( "Successfully updated" );?>';
  var do_you_want_to_delete = '<?php echo _( "Would you like to delete this manually created object" );?>';
  var select_related_type   = '<?php echo _( "Please select related type" );?>';
</script>
<div style="width:100%">
  <!-- start of main body -->
  <table width="100%" cellspacing="0" cellpadding="0" border="0">
    <tbody>
      <tr>
        <td valign="top" align="center"><table width="98%" cellspacing="0" cellpadding="0" border="0">
          <tbody>
            <tr>
              <td valign="top" align="center" style="padding:15px 0px 0px 0px"><table width="98%" cellspacing="0" cellpadding="0" border="0">
                <tbody>
                  <tr>
                    <td align="center" style="padding-bottom:5px"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/bg.jpg) left top repeat-x;" class="page_caption">
                        <tbody>
                          <tr>
                            <td width="40%" align="left">
                              <h3 class="title_099"><?php echo _('OBJECTS'); ?></h3>
                            </td>
                            <?php if( $this->session->flashdata( 'msg' ) ) { ?>
                              <td width="20%">
                                <h3 class="red_star">
                                  <?php echo $this->session->flashdata( 'msg' );?>
                                </h3>
                              </td>
                            <?php } ?>
                           <td width="40%" align="right">
                               <div style="background-image:url(<?php echo base_url();?>assets/mcp/images/add.png);float:right;" title="Add Objects" class="icon_button" onClick="window.location.href='<?php echo base_url();?>mcp/autocontrole/addObjects'" id="btn_add"></div>
                            </td>
                          </tr>
                        </tbody>
                      </table></td>
                  </tr>
                  <tr>
                    <td align="center">
                      <table width="100%" cellspacing="0" cellpadding="0" border="0">
                          <tbody>
                        <tr>
                        <td height="22" align="right">
                          <div style="float:right; width:80%">

                        </div>
                    </td>
                  </tr>
                  <tr>
                  <td>
                    <td style="padding-top:5px">
                      <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/pink_table_bg.jpg) left repeat; text-align:left">
                        <tbody>
                          <tr>
                            <td valign="middle" height="20" bgcolor="#003366" align="left" style="border:#003366 1px solid; padding-left:5px" class="whiteSmallBold"><?php echo _('Search type'); ?></td>
                          </tr>
                          <tr>
                            <td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid; padding:5px">
                             <?php echo form_open_multipart( "mcp/autocontrole/searchObjects",array( 'method'=>"post" ,'id'=>"frm_autocontrole_objects_srch", 'name'=>"frm_autocontrole_objects_srch")); ?>
                              <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tbody>
                                  <tr>
                                    <td width="69" height="22" class="blackMediumNormal"><?php echo _('Search By'); ?></td>
                                    <td width="126">
                                      <select name="search_type" class="textbox" required="required">
                                        <option value=""><?php echo _('Select'); ?></option>
                                        <option value="id"><?php echo _('ID'); ?></option>
                                        <option value="name" ><?php echo _('Name'); ?></option>
                                      </select>
                                   </td>
                                    <td width="109" class="blackMediumNormal"><?php echo _('Search Keyword'); ?></td>
                                    <td width="160">
                                      <?php echo form_input(array('id'=>"search_keyword" ,'name'=>"search_keyword" , 'style'=>'width:140px;' , 'class'=>'textbox' ,'required' => 'required'));?>
                                    </td>
                                    <td width="345">
                                      <span style="padding:0px 3px 3px 0px">
                                        <?php echo form_submit(array('id'=>'search','name'=>'search','value'=>'SEARCH','class'=>'btnWhiteBack'));?>&nbsp;
                                        <?php echo form_button(array('type'=>'reset','content'=>'RESET','value'=>'true','class'=>'btnWhiteBack'));?>
                                      </span>
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                              <?php echo form_close();?>
                            </td>
                          </tr>
                           <tr>
                            <td height="22" align="right">
                              <div style="float:right; width:80%">
                              </div>
                            </td>
                          </tr>
                          <tr class="pqrop">
                            <td bgcolor="#003366">
                              <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/pink_table_bg.jpg) left repeat; text-align:left;">
                            <tbody>
                              <tr>
                                <td width="5%"  class="whiteSmallBold">
                                  <?php echo _( 'ID' ); ?>
                                </td>
                                <td width="10%" class="whiteSmallBold">
                                  <?php echo _( 'Object name Fr' ); ?>
                                </td>
                                <td width="10%" class="whiteSmallBold">
                                  <?php echo _( 'Object name NL_BE' ); ?>
                                </td>
                                <td width="10%" class="whiteSmallBold">
                                  <?php echo _( 'Object name NL_NL' ); ?>
                                </td>
                                <td width="10%" class="whiteSmallBold">
                                  <?php echo _( 'Object name En' ); ?>
                                </td>
                                <td width="10%" class="whiteSmallBold">
                                  <?php echo _( 'Category' ); ?>
                                </td>
                                <td width="20%" class="whiteSmallBold">
                                  <?php echo _( 'Company Type' ); ?>
                                </td>
                                 <td width="20%" class="whiteSmallBold">
                                  <?php echo _( 'Related Type' ); ?>
                                </td>
                                <td width="5%" class="whiteSmallBold">
                                  <?php echo _('Options'); ?>
                                </td>
                              </tr>
                            <?php
                              if( !empty( $autocontrole_objects ) ){
                                foreach ( $autocontrole_objects as $k => $value ) { ?>
                                <tr>
                                  <td valign="middle" bgcolor="#FFFFFF" style="border-left:1px solid #000;border-right:1px solid #000;" colspan="9" >
                                    <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                      <tbody>
                                        <tr data-key="<?php echo $k; ?>">
                                           <td width="5%" class="blackMediumNormal" ><?php echo $value[ 'o_id' ]; ?></td>
                                            <td width="10%" class="blackMediumNormal">
                                               <?php echo $value[ 'object_name_fr' ]; ?>
                                            </td>
                                            <td width="10%" class="blackMediumNormal">
                                               <?php echo $value[ 'object_name_dch' ]; ?>
                                            </td>
                                            <td width="10%" class="blackMediumNormal">
                                               <?php echo $value[ 'object_name_nl' ]; ?>
                                            </td>
                                            <td width="10%" class="blackMediumNormal">
                                              <?php echo $value[ 'object_name' ]; ?>
                                            </td>
                                            <td width="10%" class="blackMediumNormal">
                                              <?php if( !empty( $objects_category ) ){
                                                  if( array_key_exists( $value[ 'type_id' ] , $objects_category ) ){
                                                    echo $objects_category[ $value[ 'type_id' ] ]['name'];
                                                  }else{
                                                    echo _( 'No Category' );
                                                  }
                                                }else{
                                                  echo _( 'No Category' );
                                                } ?>
                                            </td>
                                             <td width="20%" class="blackMediumNormal">
                                                 <?php 
                                                 $company_type_name = array( );
                                                 $company_type_ids = explode( '#', $value[ 'company_type' ] );
                                                 if( !empty(  $company_type_ids ) ){
                                                   if( isset( $company_type ) && !empty( $company_type ) ) {
                                                      foreach ( $company_type as $k => $c_type_id ) {
                                                        if( in_array( $c_type_id[ 'id' ], $company_type_ids ) ){ 
                                                          array_push( $company_type_name, $c_type_id[ 'company_type_name' ] );
                                                        }
                                                      }
                                                      if( !empty( $company_type_name ) ) {
                                                        echo implode( ", ", $company_type_name );
                                                      } else {
                                                        echo "--";
                                                      }
                                                    }
                                                 }else{
                                                  echo '--';
                                                  }?>
                                              </td>
                                              <td width="20%" class="blackMediumNormal">
                                               <?php 
                                               $related_type_name  = array( );
                                               $related_type_ids   = explode( '#', $value[ 'related_type_ids' ] );
                                               if( !empty(  $related_type_ids ) ){
                                                 if( isset( $autocontrole_type ) && !empty( $autocontrole_type ) ) {
                                                    foreach ( $autocontrole_type as $k => $type_id ) {
                                                      if( in_array( $type_id[ 'id' ], $related_type_ids ) ){ 
                                                        array_push( $related_type_name, $type_id[ 'type_name' ] );
                                                      }
                                                    }
                                                    if( !empty( $related_type_name ) ) {
                                                      echo implode( ", ", $related_type_name );
                                                    } else {
                                                      echo "--";
                                                    }
                                                  }
                                               }else{
                                                echo '--';
                                                }?>
                                            </td>
                                            <td width="5%" class="blackMediumNormal" >
                                              <span class="blackMediumNormal" style="padding:5px">
                                                <img width="16" height="16" border="0" src="<?php echo base_url(); ?>assets/mcp/images/edit.jpg" alt="Edit" title="Edit" onclick="window.location.href='<?php echo base_url(); ?>mcp/autocontrole/editObjects/<?php echo $value[ 'o_id' ]; ?>'" style="cursor:pointer">
                                                <img width="16" height="16" class="delete_objects" border="0" src="<?php echo base_url(); ?>assets/mcp/images/delete.jpg" alt="Delete" title="Delete" data-id="<?php echo $value[ 'o_id' ]; ?>">
                                              </span>
                                            </td>
                                         </tr>
                                      </tbody>
                                    </table>
                                  </td>
                                </tr>
                              <?php }
                              } else {?>
                               <tr>
                                 <td bgcolor="#FFFFFF" style="border-left:1px solid #000;border-right:1px solid #000; text-align:center;" colspan="8">
                                   <p><?php echo _('No Objects are there.'); ?></p>
                                </td>
                               </tr>
                             <?php }?>
                            <tr class="last_row">
                              <td colspan="5" style="background-color: #003366">&nbsp;</td>
                            </tr>
                          </tbody>
                        </table>
                      </td>
                    </tr>
                     <tr>
                        <table width="100%" class="manually_entered_items">
                          <tbody >
                            <tr>
                              <td colspan="4" class="blue_text"><?php echo _("Manually Entered Values");?></td>
                              <td colspan="5" style="color: red;"><?php echo _("USED BY");?></td>
                            </tr>
                            <?php if(!empty($manually)){
                             foreach ($manually as $key => $value) {
                              ?>                                       
                            <tr>
                              <td width="15%" class="yellow_text"><?php echo $value[ 'object_name' ]; ?></td>
                              <td width="10%" class="blue_text"><?php echo _("entered into");?></td>
                              <td width="10%" class="yellow_text">
                              <?php echo $value[ 'type_name' ] != '' ? implode( ',', $value[ 'type_name' ] ) : "" ?>
                              </td>
                              <td width="12%" class="blue_text"><?php echo _("assigne this to");?></td>
                              <td width="8%" style="color: red;">
                                <?php echo $value[ 'comp_username' ];?>
                              </td>
                              <td width="13%">
                                <select name="task_type"  class="textbox group_id">
                                    <?php 
                                    if( isset( $all_groups ) && !empty( $all_groups ) ){
                                      foreach ( $all_groups as $k => $group ) { ?>
                                        <option <?php echo isset( $value[ 'obj_grp' ] ) && $value[ 'obj_grp' ] == $group['id'] ? 'selected="selected"' : ''; ?> value="<?php echo $group['id'] ?>"><?php echo $group['temp_group_name']; ?></option>
                                      <?php } 
                                    } ?>
                                </select>
                              </td>
                              <td width="13%">
                                <select name="task_type"  class="textbox objects_category">
                                    <?php 
                                    if( isset( $objects_category ) && !empty( $objects_category ) ){
                                      foreach ( $objects_category as $k => $obj_cat ) { ?>
                                        <option <?php echo isset( $value[ 'obj_cat' ] ) && $value[ 'obj_cat' ] == $obj_cat['id'] ? 'selected="selected"' : ''; ?> value="<?php echo $obj_cat['id'] ?>"><?php echo $obj_cat['name']; ?></option>
                                      <?php } 
                                    } ?>
                                </select>
                              </td>
                              <td width="12%">
                                  <select name="company_type[]" multiple="multiple" class="textbox company_type_name">
                                      <?php
                                        foreach ( $company_type as $k => $c_type_id ) {
                                          if( in_array( $c_type_id[ 'id' ], $value[ 'user_type_id' ] ) ){?> 
                                            <option value="<?php echo $c_type_id['id'];?>" selected ><?php echo $c_type_id['company_type_name'];?></option>
                                          <?php 
                                          } else {?>
                                            <option value="<?php echo $c_type_id['id'];?>"><?php echo $c_type_id['company_type_name'];?></option>
                                          <?php 
                                          }
                                        }?>
                                </select>
                              </td>
                              <td width="10%">
                                  <select name="related_type_name[]" multiple="multiple" class="textbox related_type_name">
                                       <?php
                                        foreach ( $autocontrole_type as $k => $type_id ) {
                                          if(  in_array($type_id[ 'id' ], $value[ 'type_id' ] ) ){?> 
                                            <option value="<?php echo $type_id['id'];?>" selected ><?php echo $type_id['type_name'];?></option>
                                          <?php 
                                          } else {?>
                                            <option value="<?php echo $type_id['id'];?>"><?php echo $type_id['type_name'];?></option>
                                          <?php 
                                          }
                                        }?>
                                </select>
                              </td>
                              <td width="10%"><button class="merge_object" data-enterd_into_type="<?php echo $value[ 'type_name' ] != '' ? implode( ',', $value[ 'type_name' ] ) : "" ?>"  data-company_id="<?php echo $value[ 'company_id' ];?>" data-name="<?php echo $value[ 'object_name' ] ?>" type="submit"><?php echo _("Submit");?></button><img width="16" height="16" border="0" data-id="12" title="<?php echo _( "Delete" ); ?>" alt="<?php echo _( "Delete" ); ?>" src="<?php echo base_url( ); ?>assets/mcp/images/delete.jpg" class="delete_manually_value" data-objectname="<?php echo $value[ 'object_name' ] ?>" data-section="object" data-company_id="<?php echo $value[ 'company_id' ];?>" ></td>
                            </tr>
                          <?php } }else{ ?>
                                  <tr>
                                   <td bgcolor="#FFFFFF" style="border-left:1px solid #000;border-right:1px solid #000; text-align:center;" colspan="7">
                                     <p><?php echo _('No Manually predefined are there.'); ?></p>
                                  </td>
                                 </tr>
                            <?php }?>
                          </tbody>
                        </table>
                      </tr>
                    </tbody>
                </table>
              </table>
            </tr>
          </tbody>
        </td>
      </tr>

    </tbody>
  </table>       
</div>
  <!-- <table width="100%" class="manually_entered_items">
    <tbody >
      <tr>
        <td colspan="7">Manually Entered Values</td>
      </tr>
    </tbody>
  </table> -->
  <!-- end of main body -->