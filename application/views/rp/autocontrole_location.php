<script type="text/javascript">
  var successfully_updated  = '<?php echo _( "Successfully updated" );?>';
  var please_select_loc     =  '<?php echo _( "Please Select location" );?>';
  var  delete_manuall       =  '<?php echo _( "Would you like to remove it from Panel ?" );?>';
</script>
<div style="width:100%">
    <!-- start of main body -->
    <table width="100%" cellspacing="0" cellpadding="0" border="0">
      <tbody>
        <tr>
          <td valign="top" align="center"><table width="98%" cellspacing="0" cellpadding="0" border="0">
              <tbody>
                <tr>
                  <td valign="top" align="center" style="border:#003366 1px solid; padding:15px 0px 0px 0px">
                    <table width="98%" cellspacing="0" cellpadding="0" border="0">
                      <tbody>
                        <tr>
                          <td align="center" style="padding-bottom:5px">
                            <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url();?>assets/mcp/images/bg.jpg) left top repeat-x;" class="page_caption">
                                <tbody>
                                  <tr>
                                    <td width="40%" align="left"><h3 class="title_099"><?php echo _( 'Locations' ); ?></h3></td>
                                    <?php if( $this->session->flashdata( 'msg' ) ) { ?>
                                      <td width="20%">
                                        <h3 class="red_star">
                                          <?php echo $this->session->flashdata( 'msg' );?>
                                        </h3>
                                      </td>
                                    <?php } ?>
                                    <td width="40%" align="right">
                                      <div onclick="history.back();" title="Back" style="background-image:url(<?php echo base_url();?>assets/mcp/images/undo.jpg); cursor:pointer; float:right" class="icon_button"></div>
                                      <div style="background-image:url(<?php echo base_url();?>assets/mcp/images/add.png);float:right;" title="Add Predifine" class="icon_button" onClick="window.location.href='<?php echo base_url();?>rp/autocontrole/addeditLocation'" id="btn_add"></div>
                                    </td>
                                  </tr>
                                </tbody>
                            </table>
                          </td>
                        </tr>
                        <tr>
                          <td align="center">
                            <table width="100%" cellspacing="0" cellpadding="0" border="0" >
                              <tbody>
                                <tr>
                                  <td style="padding-top:5px">
                                    <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/pink_table_bg.jpg) left repeat; text-align:left">
                                      <tbody>
                                        <tr>
                                          <td valign="middle" height="20" bgcolor="#003366" align="left" style="border:#003366 1px solid; padding-left:5px" class="whiteSmallBold"><?php echo _('Search Location'); ?></td>
                                        </tr>
                                        <tr>
                                          <td  valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid; padding:5px">
                                            <?php echo form_open_multipart( "rp/autocontrole/searchLocation",array( 'method'=>"post" ,'id'=>"frm_autocontrole_location_srch", 'name'=>"frm_autocontrole_location_srch")); ?>
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
                                                    <?php echo form_input(array('id'=>"search_keyword" ,'name'=>"search_keyword" , 'style'=>'width:140px;' , 'class'=>'textbox' ,'required' => 'required' ));?>
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
                                      </tbody>
                                    </table>
                                  </td>
                                </tr>
                                <tr>
                                  <td height="22" align="right">
                                    <div style="float:right; width:80%">
                                    </div>
                                  </td>
                                </tr>
                                <tbody>
                                      <tr>
                                        <td bgcolor="#003366">
                                          <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/pink_table_bg.jpg) left repeat; text-align:left;" class="auto_table">
                                            <tr>
                                              <td width="5%"  class="whiteSmallBold"> <?php echo _( 'ID' ); ?></td>
                                              <td width="25%" class="whiteSmallBold"><?php echo _( 'DCH' ); ?></td>
                                              <td width="25%" class="whiteSmallBold"><?php echo _( 'FR' ); ?></td>
                                              <td width="25%" class="whiteSmallBold"><?php echo _( 'EN' ); ?></td>
                                              <td width="20%" class="whiteSmallBold"><?php echo _('Options'); ?></td>
                                            </tr>

                                            <?php
                                            if( !empty( $locations ) ){
                                              foreach ( $locations as $key => $location ) { ?>
                                                <tr>
                                                  <td valign="middle" class="auto_location" bgcolor="#FFFFFF" style="border:#003366 1px solid" colspan="5">
                                                    <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                                      <tbody>
                                                         <tr>
                                                          <td width="5%" class="blackMediumNormal">
                                                            <?php echo $location[ 'id' ]; ?>
                                                          </td>

                                                          <td width="25%" class="blackMediumNormal">
                                                            <?php  echo $location[ 'loc_name_dch' ]; ?>
                                                          </td>
                                                          <td width="25%" class="blackMediumNormal">
                                                              <?php  echo $location[ 'loc_name_fr' ]; ?>
                                                          </td>
                                                          <td width="25%" class="blackMediumNormal">
                                                            <?php  echo $location[ 'loc_name' ]; ?>
                                                          </td>

                                                          <td width="20%" class="whiteSmallBold">
                                                            <span class="blackMediumNormal" style="padding:5px">
                                                                <img width="16" height="16" border="0" src="<?php echo base_url(); ?>assets/mcp/images/edit.jpg" alt="Edit" title="Edit" onclick="window.location.href='<?php echo base_url(); ?>rp/autocontrole/addeditlocation/<?php echo $location[ 'id' ]; ?>'" style="cursor:pointer">
                                                                <img width="16" height="16" class="delete_location" border="0" src="<?php echo base_url(); ?>assets/mcp/images/delete.jpg" alt="Delete" title="Delete" data-id="<?php echo $location[ 'id' ]; ?>">
                                                              </span>
                                                          </td>
                                                         </tr>
                                                      </tbody>
                                                    </table>
                                                  </td>
                                                </tr>
                                            <?php }
                                          }else{ ?>
                                           <tr>
                                              <td  colspan="5" bgcolor="#FFFFFF" style="padding:10px;font-size:14px;">
                                                 <p><?php echo _( 'No Locations are there.' ); ?></p>
                                              </td>
                                           </tr>
                                        <?php } ?>
                                      </table>
                                    </tbody>
                                  </td>
                                </tr>
                                <tr>
                                  <table width="100%" class="manually_entered_items">
                                      <tbody >
                                        <tr>
                                          <td colspan="2" class="blue_text"><?php echo _("Manually Entered Values");?></td>
                                          <td colspan="3" style="color: red;"><?php echo _("USED BY");?></td>
                                        </tr>
                                        <?php if(!empty($manually)){
                                             foreach ($manually as $key => $value) { ?>
                                            <tr>
                                              <td width="15%" class="yellow_text"><?php echo $value[0]; ?></td>
                                              <td width="12%" class="blue_text"><?php echo _("assigne this to");?></td>
                                              <td width="15%" style="color: red;">
                                                <?php echo $value[2];?>
                                              </td>
                                              <td width="15%">
                                                <select name="location_name"  class="textbox predefined_location">
                                                  <option value="0"><?php echo _('Select'); ?></option>
                                                  <?php 
                                                  if( get_lang( $_COOKIE['locale'] ) == 'nl_NL' ) {
                                                    $loc_name = 'loc_name_dch';
                                                  } else if( get_lang( $_COOKIE['locale'] ) == 'fr_FR' ){
                                                    $loc_name = 'loc_name_fr';
                                                  } else if( get_lang( $_COOKIE['locale'] ) == 'en_US' ){
                                                    $loc_name = 'loc_name';
                                                  } else {
                                                    $loc_name = 'loc_name_dch';
                                                  }
                                                  foreach ( $locations as $key => $location ) { ?>
                                                    <option value="<?php echo $location['id'] ?>"><?php echo $location[ $loc_name ]; ?></option>
                                                  <?php } ?>
                                                </select>
                                              </td>
                                              <td width="10%">
                                                <button class="merge_location" data-company_id="<?php echo $value[1]; ?>" data-name="<?php echo $value[0] ?>" type="submit"><?php echo _("Submit");?></button>
                                                <img width="16" height="16" border="0" src="<?php echo base_url(); ?>assets/mcp/images/edit.jpg" alt="Edit" title="Edit" onclick="window.location.href='<?php echo base_url(); ?>rp/autocontrole/addeditlocation/<?php echo $value[0].'_'.$value[1]; ?>'" style="cursor:pointer">
                                                 <img width="16" height="16" border="0" title="Delete" alt="Delete" src="<?php echo base_url(); ?>assets/mcp/images/delete.jpg" class="del_manualy_created_loc">
                                              </td>
                                            </tr>
                                          <?php } }else{ ?>
                                                  <tr>
                                                   <td bgcolor="#FFFFFF" style="text-align:center;" colspan="5">
                                                     <p><?php echo _('No Manually Locations are there.'); ?></p>
                                                  </td>
                                                 </tr>
                                            <?php }?>
                                      </tbody>
                                  </table>
                                </tr>
                              <tr>
                                <td>&nbsp;</td>
                              </tr>
                            </tbody>
                          </table>
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
  </tbody>
</table>
    <!-- end of main body -->
  </div>