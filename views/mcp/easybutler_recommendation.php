<?php 
if( strpos( current_url(), 'rp' ) > -1 ) {
  $edit_url = base_url().'rp/easybutler/addedit_easybutlerinfo/';
  $add_url = base_url().'rp/easybutler/addedit_easybutlerinfo';
} else {
  $edit_url = base_url().'mcp/easybutler/addedit_easybutlerinfo/';
  $add_url = base_url().'mcp/easybutler/addedit_easybutlerinfo';
}?>
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
                                  <h3><?php echo _('EB Leads'); ?></h3>
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
                                  <div style="background-image:url(<?php echo base_url(); ?>assets/mcp/images/add.png);float:right;" title="<?php echo _('Add New Temperature Group'); ?>" class="icon_button" onClick="window.location.href='<?php echo $add_url;?>'" id="btn_add">
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
                                        <td width="10%"  class="whiteSmallBold"><?php echo _('ID'); ?></td>
                                        <td width="20%" class="whiteSmallBold"><?php echo _('Resturant'); ?></td>
                                        <td width="15%" class="whiteSmallBold"><?php echo _('City'); ?></td>
                                        <td width="20%" class="whiteSmallBold"><?php echo _('Name'); ?></td>
                                        <td width="15%" class="whiteSmallBold" align="center"><?php echo _('Phone'); ?></td>
                                        <td width="20%" class="whiteSmallBold" align="center"><?php echo _('Options'); ?></td>
                                      </tr>
                                       <tr>
                                          <td style="border:#003366 1px solid" colspan="12" valign="middle" bgcolor="#FFFFFF">
                                            <table cellspacing="0" cellpadding="0" width="100%" border="0">
                                              <tbody>
                                                <?php
                                                  if( !empty( $easybutlerinfo ) ){
                                                    foreach ( $easybutlerinfo as $key => $value ) { ?>
                                                      <tr>
                                                        <td width="10%"  valign="middle" bgcolor="#FFFFFF" class="blackMediumNormal">
                                                          <?php echo $value[ 'id' ]; ?>
                                                        </td>
                                                        <td width="15%" valign="middle" bgcolor="#FFFFFF" class="blackMediumNormal">
                                                          <?php echo $value[ 'resturant_name' ]; ?>
                                                        </td>
                                                        <td width="15%" valign="middle" bgcolor="#FFFFFF" class="blackMediumNormal">
                                                          <?php echo $value[ 'city' ]; ?>
                                                        </td>
                                                        <td width="15%" valign="middle" bgcolor="#FFFFFF" class="blackMediumNormal">
                                                          <?php echo $value[ 'name' ]; ?>
                                                         </td>
                                                          <td width="15%" align="center" valign="middle" bgcolor="#FFFFFF" class="blackMediumNormal">
                                                          <?php echo $value[ 'phone' ]; ?>
                                                         </td>
                                                        <td width="15%" align="center" valign="middle" bgcolor="#FFFFFF">
                                                          <span class="blackMediumNormal" style="padding:5px">
                                                            <img width="16" height="16" border="0" src="<?php echo base_url(); ?>assets/mcp/images/edit.jpg" alt="Edit" title="Edit" onclick="window.location.href='<?php echo $edit_url; ?><?php echo $value[ 'id' ]; ?>'" style="cursor:pointer">
                                                          </span>
                                                          <img class="delete_easybutlerinfo" src="<?php echo base_url( '/assets/mcp/images/delete.jpg' );?> " alt="Delete" title="Delete" data-id="<?php echo $value[ 'id' ]; ?>" border="0" width="16" height="16">
                                                        </td>
                                                      </tr>
                                                    <?php }
                                                  }else{ ?>
		                                             <tr>
		                                                <td  colspan="6" bgcolor="#FFFFFF" style="padding:10px;font-size:14px;">
		                                                   <p><?php echo _( 'No Workroom are there.' ); ?></p>
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
<script type="text/javascript">
  /* delete autocontrol temp group.*/
  jQuery( document ).on( 'click','.delete_easybutlerinfo' , function( $ ){ 
    var $this = jQuery( this );
    var id = $this.data( 'id' );
      jQuery.ajax({
        url : base_url +'mcp/easybutler/delete_easybutlerinfo',
        data: {
          'id' : id,
        },
        async : !1,
        type: "POST",
        success: function( data ) {
              if( data == 'success' ){
                  $this.closest( 'tr' ).remove();
              }
            }
      });
    
  });

</script>