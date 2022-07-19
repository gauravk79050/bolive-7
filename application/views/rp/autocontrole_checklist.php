<script type="text/javascript">
  var confirm_del_checklist = "<?php echo _( 'Would you like to delete this checklist ?' );?>";
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
                          <td width="50%" align="left"><h3><?php echo _('Checklist'); ?></h3></td>
                            <td width="50%" align="right">
                              <div onclick="history.back();" title="Back" style="background-image:url(<?php echo base_url(); ?>assets/mcp/images/undo.jpg); cursor:pointer; float:right" class="icon_button">
                              </div>
                                <div style="background-image:url(<?php echo base_url(); ?>assets/mcp/images/add.png);float:right;" title="<?php echo _('Add Checklist'); ?>" class="icon_button" onClick="window.location.href='<?php echo base_url(''); ?>rp/autocontrole/checklist_add_edit';" id="btn_add">
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
                                        <td height="20" bgcolor="#003366" class="whiteSmallBold"><?php echo _('Search Checklist'); ?></td>
                                    </tr>
                                    <tr>
                                        <td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid; padding:5px">
                                          <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <form action="<?php echo base_url().'rp/autocontrole/checklist'; ?>" enctype="multipart/form-data" method="post" id="frm_search" name="frm_search">
                                              <tbody>
                                              <tr>
                                                <td width="69" height="22" class="blackMediumNormal">
                                                  <b><?php echo _('Search By'); ?></b>
                                                </td>
                                                <td width="126">
                                                  <select style="width:120px" class="textbox" id="search_by" name="search_by" required>
                                                    <option value="">- - <?php echo _('Search By'); ?> - -</option>
                                                    <option value="id"><?php echo _('ID'); ?></option>
                                                    <option value="checklist_item"><?php echo _('Checklist Item'); ?></option>
                                                    <option value="domain"><?php echo _('Domain'); ?></option>
                                                    <option value="companytype"><?php echo _('Company Type'); ?></option>
                                                  </select>
                                                </td>
                                                <td width="120" class="blackMediumNormal">
                                                   <b><?php echo _('Search Keyword'); ?></b>
                                                </td>
                                                <td width="160">
                                                  <input type="text" style="width:140px" class="textbox" id="search_keyword" name="search_keyword" required>
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
                                      <td width="5%"  class="whiteSmallBold"></td>
                                      <td width="3%" class="whiteSmallBold"><?php echo _('ID');?></td>
                                      <td width="10%" class="whiteSmallBold"><?php echo _('Checklist Item');?></td>
                                      <td width="10%" class="whiteSmallBold"><?php echo _('Additional info');?></td>
                                      <td width="5%" class="whiteSmallBold"><?php echo _('Domain');?></td>
                                      <td width="11%" class="whiteSmallBold"><?php echo _('Company Type');?></td>
                                      <td width="4%" class="whiteSmallBold" align="center"><?php echo _('Easing');?></td>
                                      <td width="12%" class="whiteSmallBold"><?php echo _('Tasks Related');?></td>
                                      <td width="4%" class="whiteSmallBold"><?php echo _('Action');?></td>
                                    </tr>
                                  </thead>
                                  <tbody id="company_list">  
                                     <?php if( !empty( $checklists ) ){ 
                                        foreach ($checklists as $key => $checklist ) { 
                                          $company_type_ids = array();
                                          $company_type_name = array();
                                          if( !empty( $checklist[ 'company_type' ] ) ) {
                                              $company_type_ids = json_decode( $checklist[ 'company_type' ] );
                                          }?>
                                          <tr data-id="<?php echo $checklist[ 'id' ]; ?>" class="checklist_row">
                                            <td width="5%"  bgcolor="#FFFFFF" class="blackMediumNormal">
                                              <img width="16" class="dragrow_autocont" height="16"  border="0" src="<?php echo base_url(); ?>assets/mcp/images/dragable.png" alt="Drag" title ="Drag" >
                                            </td>
                                             <td width="3%" style="padding-left: 3px;"><?php echo $checklist[ 'id' ];?></td>
                                              <td width="10%"><?php echo $checklist[ 'question_dch' ].'<br>'.$checklist[ 'question_fr' ].'<br>'.$checklist[ 'question' ];?></td>
                                              <td width="10%">
                                                <?php if( $checklist[ 'additional_info_dch' ] != '' || $checklist[ 'additional_info_fr' ] != '' || $checklist[ 'additional_info' ] != ''  ){
                                                     echo $checklist[ 'additional_info_dch' ].'<br>'.$checklist[ 'additional_info_fr' ].'<br>'.$checklist[ 'additional_info' ];
                                                } else {
                                                  echo "--";
                                                }?>

                                             </td>
                                              <td width="5%">
                                                <?php 
                                                  if( !empty( $domains ) && array_key_exists( $checklist[ 'domain_id' ], $domains )){ 
                                                    echo $domains[ $checklist[ 'domain_id' ] ][ 'domain_name' ];
                                                  }else{
                                                    echo '--';
                                                  }?>
                                                </td>
                                              <td width="11%">
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
                                              <td width="4%"  align="center">
                                                 <?php 
                                                 if( $checklist[ 'easing_measure' ] != '' ){ 
                                                    if( $checklist[ 'easing_measure' ] ){ 
                                                        echo _( 'yes' );
                                                      }else{
                                                        echo _('no');
                                                      }
                                                 }else{
                                                   echo _('no');
                                                 } ?>
                                              </td>
                                              <td width="12%" style="padding-left: 40px">
                                                <?php 
                                                 if( $checklist[ 'connected_tasks' ] != '' ){
                                                    $connected_tasks = json_decode( $checklist[ 'connected_tasks' ] ,true );
                                                    echo sizeof( $connected_tasks );
                                                    // $string = '';
                                                    // foreach ( $connected_tasks as $key => $ease_m ) {
                                                    //   if( array_key_exists( $ease_m , $allTask ) ){
                                                    //       $string = $allTask[ $ease_m ][ 'task_name' ].', '.$string;
                                                    //   }
                                                    // }
                                                    // echo rtrim( $string , ', ' );
                                                 }else{
                                                  echo '--';
                                                 } ?>
                                             </td>
                                              <td width="4%" >
                                                <span style="padding:5px" class="blackMediumNormal">
                                                   <img width="16" height="16" border="0" src="<?php echo base_url(); ?>assets/mcp/images/edit.jpg" alt="Edit" title="Edit" onclick="window.location.href='<?php echo base_url(); ?>rp/autocontrole/checklist_add_edit/<?php echo $checklist[ 'id' ]; ?>'" style="cursor:pointer">
                                                   <img width="16" height="16" title="Delete" alt="Delete" src="<?php echo base_url(); ?>assets/mcp/images/delete.jpg" class="delete_checklist" data-id="<?php echo $checklist[ 'id' ]; ?>">
                                                  </span>
                                              </td>
                                          </tr>
                                          <tr>
                                      <?php  }
                                      } else{ ?>
                                      <tr><td colspan="7" align="center" style="color: red;"> <?php echo _( "No checklist Available" ); ?></td></tr>
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