<?php 
if( strpos( current_url(), 'rp' ) > -1 ) {
  $form_url = base_url().'rp/autocontrole/ccp_pva_ghp/'.$country_code;
  $edit_url = base_url().'rp/autocontrole/ccp_pva_ghp_add_edit/';
  $add_url  = base_url().'rp/autocontrole/ccp_pva_ghp_add_edit/'.$country_code;
} else {
  $form_url = base_url().'mcp/autocontrole/ccp_pva_ghp/'.$country_code;
  $edit_url = base_url().'mcp/autocontrole/ccp_pva_ghp_add_edit/';
  $add_url  = base_url().'mcp/autocontrole/ccp_pva_ghp_add_edit/'.$country_code;
}?>

<script type="text/javascript">
  var confirm_del_ccp_pva_ghp = "<?php echo _( 'Would you like to delete this CCP/ PVA/ GHP ?' );?>";
</script>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
  <tbody>
    <tr>
      <td valign="top" align="center"><table width="98%" cellspacing="0" cellpadding="0" border="0">
        <tbody>
          <tr>
            <td valign="top" align="center" style="border:#003366 1px solid; padding:15px 0px 0px 0px"><table width="98%" cellspacing="0" 
         cellpadding="0" border="0">
            <tbody>
              <tr>
                <td align="center" style="padding-bottom:10px">
                  <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/bg.jpg) left top repeat-x;" class="page_caption">
                      <tbody>
                        <tr height="26">
                          <td width="30%" align="left"><h3><?php echo _('CCP/ PVA/ GHP'); ?></h3></td>
                          <td width="40%" align="center"><h3 <?php if (strpos( $this->session->flashdata('msg') , 'Successfully' ) !== false ) { echo 'style="color: #4BB543"'; }else{ echo 'style="color: #BA0000"'; } ?> ><?php echo $this->session->flashdata('msg'); ?></h3></td>
                            <td width="30%" align="right">
                              <div onclick="history.back();" title="Back" style="background-image:url(<?php echo base_url(); ?>assets/mcp/images/undo.jpg); cursor:pointer; float:right" class="icon_button">
                              </div>
                                <div style="background-image:url(<?php echo base_url(); ?>assets/mcp/images/add.png);float:right;" title="<?php echo _('Add Checklist'); ?>" class="icon_button" onClick="window.location.href='<?php echo $add_url; ?>';" id="btn_add">
                                </div>
                            </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                  </tr>
                    <tr>
                      <td align="center">
                        <table width="100%" cellspacing="0" cellpadding="0" border="0" class="blackMediumNormal">
                          <tbody>
                            <tr>
                              <td>
                                <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>                   assets/mcp/images/pink_table_bg.jpg) left repeat;">
                                  <tbody>
                                    <tr>
                                        <td height="20" bgcolor="#003366" class="whiteSmallBold"><?php echo _('Search'); ?></td>
                                    </tr>
                                    <tr>
                                        <td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid; padding:5px">
                                          <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <form action="<?php echo $form_url; ?>" enctype="multipart/form-data" method="post" id="frm_search" name="frm_search">
                                              <tbody>
                                              <tr>
                                                <td width="69" height="22" class="blackMediumNormal">
                                                  <b><?php echo _('Search By'); ?></b>
                                                </td>
                                                <td width="126">
                                                  <select style="width:120px" class="textbox" id="search_by" name="search_by" required>
                                                    <option value="">- - <?php echo _('Search By'); ?> - -</option>
                                                    <option <?php if( $search_by == 'id' ){ echo "selected='selected'"; } ?> value="id"><?php echo _('ID'); ?></option>
                                                    <option <?php if( $search_by == 'processtep' ){ echo "selected='selected'"; } ?> value="processtep"><?php echo _('PROCESSTEP'); ?></option>
                                                    <option <?php if( $search_by == 'danger' ){ echo "selected='selected'"; } ?> value="danger"><?php echo _('DANGER'); ?></option>
                                                    <option <?php if( $search_by == 'companytype' ){ echo "selected='selected'"; } ?> value="companytype"><?php echo _('Company Type'); ?></option>
                                                    <option <?php if( $search_by == 'ccp_pva_ghp' ){ echo "selected='selected'"; } ?> value="ccp_pva_ghp"><?php echo _('CCP/ PVA/ GHP'); ?></option>
                                                  </select>
                                                </td>
                                                <td width="120" class="blackMediumNormal">
                                                   <b><?php echo _('Search Keyword'); ?></b>
                                                </td>
                                                <td width="160">
                                                  <input type="text" style="width:140px" class="textbox" id="search_keyword" name="search_keyword" value="<?php echo $search_keyword; ?>" required>
                                                </td>
                                                <td width="50" class="blackMediumNormal">
                                                <td width="200"><span style="padding:0px 3px 3px 0px">
                                                  <input type="submit" value="<?php echo _('SEARCH'); ?>" class="btnWhiteBack" id="btn_search" name="btn_search">
                                                  <input type="button" onClick="this.form.search_by.selectedIndex=0; this.form.search_keyword.value='';this.form.submit();" value="<?php echo _('RESET'); ?>" class="btnWhiteBack" id="btn_reset" name="btn_reset">
                                                  <input type="hidden" value="do_filter" id="act" name="act">
                                                  <input type="hidden" value="companies" id="view" name="view">
                                                  </span>
                                                </td>
                                              </tr>
                                            </tbody>
                                          </form>
                                        </table>
                                      </td>
                                    </tr>
                                  </tbody>
                                </table>
                              </td>
                            </tr>
                            <tr>
                              <td height="40" align="right">
                               <div style="float:right; width:80%; padding:5px;">
                                   <div id="Pagination" class="Pagination"></div>
                               </div>
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/pink_table_bg.jpg) left repeat;border:1px solid #003366;">
                                  <thead> 
                                    <tr style="background:#003366;">
                                      <td width="3%" class="whiteSmallBold"><?php echo _('ID');?></td>
                                      <td width="10%" class="whiteSmallBold"><?php echo _('PROCESSTEP');?></td>
                                      <td width="10%" class="whiteSmallBold"><?php echo _('DANGER');?></td>
                                      <td width="5%" class="whiteSmallBold"><?php echo _('CCP/ PVA/ GHP');?></td>
                                      <td width="15%" class="whiteSmallBold"><?php echo _('COMPANY TYPE');?></td>
                                      <td width="7%" class="whiteSmallBold"><?php echo _('REF');?></td>
                                      <td width="4%" class="whiteSmallBold"><?php echo _('Action');?></td>
                                    </tr>
                                  </thead>
                                  <tbody>  
                                     <?php if( !empty( $ccp_pva_ghp_data ) ){
                                        foreach ($ccp_pva_ghp_data as $key => $ccp_pva_ghp ) { 
                                          $company_type_ids   = array();
                                          $company_type_name  = array();
                                          $processtep         = $ccp_pva_ghp[ 'processtep' ] ? $ccp_pva_ghp[ 'processtep' ] : '--';
                                          $processtep_dch     = $ccp_pva_ghp[ 'processtep_dch' ] ? $ccp_pva_ghp[ 'processtep_dch' ] : '--';
                                          $processtep_fr      = $ccp_pva_ghp[ 'processtep_fr' ] ? $ccp_pva_ghp[ 'processtep_fr' ] : '--';
                                          if( !empty( $ccp_pva_ghp[ 'company_type' ] ) ) {
                                              $company_type_ids = json_decode( $ccp_pva_ghp[ 'company_type' ] );
                                          }?>
                                          <tr data-id="<?php echo $ccp_pva_ghp[ 'id' ]; ?>" class="ccp_pva_ghp_row">
                                             <td width="3%" style="padding-left: 3px; padding-bottom: 1em;"><?php echo $ccp_pva_ghp[ 'id' ];?></td>
                                              <td width="10%" style="padding-bottom: 1em;" ><?php echo $processtep_dch.'<br>'.$processtep_fr.'<br>'.$processtep;?></td>
                                              <td width="10%" style="padding-bottom: 1em;">
                                                <?php if( $ccp_pva_ghp[ 'danger_dch' ] != '' || $ccp_pva_ghp[ 'danger_fr' ] != '' || $ccp_pva_ghp[ 'danger' ] != ''  ){
                                                     echo $ccp_pva_ghp[ 'danger_dch' ].'<br>'.$ccp_pva_ghp[ 'danger_fr' ].'<br>'.$ccp_pva_ghp[ 'danger' ];
                                                } else {
                                                  echo "--";
                                                }?>

                                             </td>
                                              <td width="5%" style="padding-bottom: 1em;">
                                                <?php 
                                                 echo $ccp_pva_ghp[ 'ccp_pva_ghp' ] ? strtoupper($ccp_pva_ghp[ 'ccp_pva_ghp' ]) : '--';
                                                  ?>
                                              </td>
                                              <td width="15%" style="padding-bottom: 1em;">
                                                 <?php if( isset( $company_type ) && !empty( $company_type ) ) {
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
                                                  }?>
                                              </td>
                                              <td width="7%" style="padding-bottom: 1em;"><?php echo $ccp_pva_ghp[ 'ref' ];?></td>
                                              <td width="4%" style="padding-bottom: 1em;">
                                                <span style="padding:5px" class="blackMediumNormal">
                                                   <img width="16" height="16" border="0" src="<?php echo base_url(); ?>assets/mcp/images/edit.jpg" alt="Edit" title="Edit" onclick="window.location.href='<?php echo $edit_url; ?><?php echo $ccp_pva_ghp[ 'id' ]; ?>/<?php echo $country_code;?>'" style="cursor:pointer">
                                                   <img width="16" height="16" title="Delete" alt="Delete" src="<?php echo base_url(); ?>assets/mcp/images/delete.jpg" class="delete_ccp_pva_ghp" data-id="<?php echo $ccp_pva_ghp[ 'id' ]; ?>">
                                                  </span>
                                              </td>
                                          </tr>
                                          <tr>
                                      <?php  }
                                      } else{ ?>
                                      <tr><td colspan="7" align="center" style="color: red;"> <?php if( isset( $search_keyword ) && $search_keyword != '' ){ echo _( "No Search result found" ); }else{ echo _( "No CCP PVA GHP Available" ); } ?></td></tr>
                                      <?php  }?>                    
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