
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
                                  <h3><?php echo _('Domains'); ?></h3>
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
                                  <div style="background-image:url(<?php echo base_url(); ?>assets/mcp/images/add.png);float:right;" title="<?php echo _('Add New Domain'); ?>" class="icon_button" onClick="window.location.href='<?php echo base_url();?>rp/autocontrole/domain_addedit'" id="btn_add">
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
                                        <td width="7%"  class="whiteSmallBold"><?php echo _('ID'); ?></td>
                                        <td width="20%" class="whiteSmallBold"><?php echo _('Dutch'); ?></td>
                                        <td width="20%" class="whiteSmallBold"><?php echo _('French'); ?></td>
                                        <td width="13%" class="whiteSmallBold"><?php echo _('English'); ?></td>
                                        <td width="20%" class="whiteSmallBold"><?php echo _('Options'); ?></td>
                                      </tr>
                                       <tr>
                                          <td style="border:#003366 1px solid" colspan="5" valign="middle" bgcolor="#FFFFFF">
                                            <table cellspacing="0" cellpadding="0" width="100%" border="0">
                                              <tbody>
                                                <?php
                                                  if( !empty( $domains ) ){
                                                    foreach ( $domains as $key => $domain ) { ?>
                                                      <tr>
                                                        <td width="7%"  valign="middle" bgcolor="#FFFFFF" class="blackMediumNormal">
                                                          <?php echo $domain[ 'id' ]; ?>
                                                        </td>
                                                        <td width="20%" valign="middle" bgcolor="#FFFFFF" class="blackMediumNormal">
                                                          <?php echo $domain[ 'domain_name_dch' ]; ?>
                                                        </td>
                                                        <td width="20%" valign="middle" bgcolor="#FFFFFF" class="blackMediumNormal">
                                                          <?php echo $domain[ 'domain_name_fr' ]; ?>
                                                        </td>
                                                        <td width="13%" valign="middle" bgcolor="#FFFFFF" class="blackMediumNormal">
                                                          <?php echo $domain[ 'domain_name' ]; ?>
                                                         </td>
                                                        <td width="20%"  valign="middle" bgcolor="#FFFFFF">
                                                          <span class="blackMediumNormal" style="padding:5px">
                                                            <img width="16" height="16" border="0" src="<?php echo base_url(); ?>assets/mcp/images/edit.jpg" alt="Edit" title="Edit" onclick="window.location.href='<?php echo base_url(); ?>rp/autocontrole/domain_addedit/<?php echo $domain[ 'id' ]; ?>'" style="cursor:pointer">
                                                          </span>
                                                        </td>
                                                      </tr>
                                                    <?php }
                                                  }else{ ?>
		                                             <tr>
		                                                <td  colspan="5" bgcolor="#FFFFFF" style="padding:10px;font-size:14px;">
		                                                   <p><?php echo _( 'No Domains are there.' ); ?></p>
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
