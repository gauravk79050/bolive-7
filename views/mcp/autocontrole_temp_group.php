<script type="text/javascript">
  var  delete_auto_temp_group =  '<?php echo _( "Would you like to remove it ?" );?>';
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
                                  <h3><?php echo _('Temperature Group'); ?></h3>
                                </td>
                                <?php if( $this->session->flashdata( 'msg' ) ) { ?>
                                  <td width="20%">
                                    <h3 class="red_star">
                                      <?php echo $this->session->flashdata( 'msg' );?>
                                    </h3>
                                  </td>
                                <?php } ?>
                                <td width="40%" align="right">
                                  <div onclick="history.back();" title="Back" style="background-image:url(<?php echo base_url(); ?>assets/mcp/images/undo.jpg); cursor:pointer; float:right" class="icon_button">
                                  </div>
                                  <div style="background-image:url(<?php echo base_url(); ?>assets/mcp/images/add.png);float:right;" title="<?php echo _('Add New Temperature Group'); ?>" class="icon_button" onClick="window.location.href='<?php echo base_url();?>mcp/autocontrole/addedit_temp_group/<?php echo $country_code;?>'" id="btn_add">
                                  </div>
                                </td>
                              </tr>
                            </tbody>
                          </table></td>
                      </tr>
                      <tr>
                        <td align="center"><table width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tbody>
                              <tr>
                                <td height="22" align="right">
  								                <div style="float:right; width:80%">
  								                </div>
								                </td>
                              </tr>
                              <tr>
                                <td bgcolor="#003366">
                                  <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/pink_table_bg.jpg) left repeat; text-align:left;">
                                    <tbody style="border:#003366 1px solid">
                                      <tr>
                                        <td width="5%"></td>
                                        <td width="7%"  class="whiteSmallBold"><?php echo _('ID'); ?></td>
                                        <td width="20%" class="whiteSmallBold"><?php echo _('Dutch'); ?></td>
                                        <td width="20%" class="whiteSmallBold"><?php echo _('French'); ?></td>
                                        <td width="20%" class="whiteSmallBold"><?php echo _('English'); ?></td>
                                        <td width="10%" class="whiteSmallBold" align="center"><?php echo _('Ideal Temperature'); ?></td>
                                        <td width="20%" class="whiteSmallBold" align="center"><?php echo _('Options'); ?></td>
                                      </tr>
                                       <tr>
                                          <td style="border:#003366 1px solid" colspan="7" valign="middle" bgcolor="#FFFFFF">
                                            <table cellspacing="0" cellpadding="0" width="100%" border="0">
                                              <tbody id="temperature_group">
                                                <?php
                                                  if( !empty( $autocontrole_temp_group ) ){
                                                    foreach ( $autocontrole_temp_group as $key => $value ) { ?>
                                                      <tr  data-id="<?php echo $value[ 'id' ]; ?>"  class="checklist_row">
                                                      <td width="5%"  bgcolor="#FFFFFF" class="blackMediumNormal">
                                                          <img width="16" class="dragrow_autocont" height="16"  border="0" src="<?php echo base_url(); ?>assets/mcp/images/dragable.png" alt="Drag" title ="Drag" >
                                                        </td>
                                                        <td width="7%"  valign="middle" bgcolor="#FFFFFF" class="blackMediumNormal">
                                                          <?php echo $value[ 'id' ]; ?>
                                                        </td>
                                                        <td width="20%" valign="middle" bgcolor="#FFFFFF" class="blackMediumNormal">
                                                          <?php echo $value[ 'temp_group_name_dch' ]; ?>
                                                        </td>
                                                        <td width="20%" valign="middle" bgcolor="#FFFFFF" class="blackMediumNormal">
                                                          <?php echo $value[ 'temp_group_name_fr' ]; ?>
                                                        </td>
                                                        <td width="20%" valign="middle" bgcolor="#FFFFFF" class="blackMediumNormal">
                                                          <?php echo $value[ 'temp_group_name' ]; ?>
                                                         </td>
                                                         <td width="10%" align="center" valign="middle" bgcolor="#FFFFFF" class="blackMediumNormal">
                                                          <?php echo $value[ 'ideal_temp' ].'&deg C'; ?>
                                                         </td>
                                                        <td width="20%" align="center" valign="middle" bgcolor="#FFFFFF">
                                                          <span class="blackMediumNormal" style="padding:5px">
                                                            <img width="16" height="16" border="0" src="<?php echo base_url(); ?>assets/mcp/images/edit.jpg" alt="Edit" title="Edit" onclick="window.location.href='<?php echo base_url(); ?>mcp/autocontrole/addedit_temp_group/<?php echo $value[ 'id' ].'/'.$country_code; ?>'" style="cursor:pointer">
                                                          </span>
                                                          <img class="delete_temp_group" src="<?php echo base_url( '/assets/mcp/images/delete.jpg' );?> " alt="Delete" title="Delete" data-id="<?php echo $value[ 'id' ]; ?>" border="0" width="16" height="16">
                                                        </td>
                                                      </tr>
                                                    <?php }
                                                  }else{ ?>
		                                             <tr>
		                                                <td  colspan="6" bgcolor="#FFFFFF" style="padding:10px;font-size:14px;">
		                                                   <p><?php echo _( 'No Temperature Group are there.' ); ?></p>
		                                                </td>
		                                             </tr>
		                                          <?php } ?>
                                                </tbody>
                                              </table>
                                            </td>
                                          </tr>
                                    </tbody>
                                  </table>
                                </td>
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
