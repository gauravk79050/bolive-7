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
                                    <td width="30%" align="left"><h3><?php echo _( 'Category' ); ?></h3></td>
                                    <?php if( $this->session->flashdata( 'msg' ) ) { ?>
                                      <td width="40%">
                                        <h3 class="red_star">
                                          <?php echo $this->session->flashdata( 'msg' );?>
                                        </h3>
                                      </td>
                                    <?php } ?>
                                    <td width="30%" align="right">
                                      <div onclick="history.back();" title="Back" style="background-image:url(<?php echo base_url();?>assets/mcp/images/undo.jpg); cursor:pointer; float:right" class="icon_button"></div>
                                      <div style="background-image:url(<?php echo base_url();?>assets/mcp/images/add.png);float:right;" title="Add Category" class="icon_button" onClick="window.location.href='<?php echo base_url();?>mcp/autocontrole/addeditcategory'" id="btn_add"></div>
                                    </td>
                                  </tr>
                                </tbody>
                            </table>
                          </td>
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
                                <tbody>
                                      <tr>
                                        <td bgcolor="#003366">
                                          <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/pink_table_bg.jpg) left repeat; text-align:left;" class="auto_procedure_table">
                                            <thead>
                                              <tr>
                                                <td width="10%" class="whiteSmallBold"><?php echo _( 'Id' ); ?></td>
                                                <td width="20%" class="whiteSmallBold"><?php echo _( 'Name' ); ?></td>
                                                <td width="30%" class="whiteSmallBold"><?php echo _( 'Action' ); ?></td>
                                              </tr>
                                            </thead>
                                            <tbody>
                                              <?php
                                              if( !empty( $category ) ){
                                                foreach ( $category as $key => $cat ) { ?>
                                                  <tr data-id="<?php echo $cat[ 'id' ]; ?>" class="proc_row">
                                                    <td width="10%" bgcolor="#FFFFFF" class="blackMediumNormal">
                                                      <?php  echo $cat[ 'id' ]; ?>
                                                    </td>
                                                    <td width="20%" bgcolor="#FFFFFF" class="blackMediumNormal">
                                                      <?php  echo $cat[ 'name' ]; ?>
                                                    </td>
                                                    <td width="30%" class="whiteSmallBold" bgcolor="#FFFFFF"  >
                                                      <span class="blackMediumNormal">
                                                          <img width="16" height="16" border="0" src="<?php echo base_url(); ?>assets/mcp/images/edit.jpg" alt="Edit" title="Edit" onclick="window.location.href='<?php echo base_url(); ?>mcp/autocontrole/addeditcategory/<?php echo $cat[ 'id' ]; ?>'" style="cursor:pointer">
                                                          <img style="padding-left:14px" width="16" height="16" class="delete_category" border="0" src="<?php echo base_url(); ?>assets/mcp/images/delete.jpg" alt="Delete" title="Delete" data-id="<?php echo $cat[ 'id' ]; ?>">
                                                        </span>
                                                    </td>
                                                  </tr>
                                              <?php }
                                            }else{ ?>
                                             <tr>
                                                <td  colspan="5" bgcolor="#FFFFFF" style="padding:10px;font-size:14px;">
                                                   <p><?php echo _( 'No Category are there.' ); ?></p>
                                                </td>
                                             </tr>
                                          <?php } ?>
                                        </tbody>
                                      </table>
                                    </tbody>
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