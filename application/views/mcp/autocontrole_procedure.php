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
                                    <td width="30%" align="left"><h3><?php echo _( 'Procedures' ); ?></h3></td>
                                    <?php if( $this->session->flashdata( 'msg' ) ) { ?>
                                      <td width="40%">
                                        <h3 class="red_star">
                                          <?php echo $this->session->flashdata( 'msg' );?>
                                        </h3>
                                      </td>
                                    <?php } ?>
                                    <td width="30%" align="right">
                                      <div onclick="history.back();" title="Back" style="background-image:url(<?php echo base_url();?>assets/mcp/images/undo.jpg); cursor:pointer; float:right" class="icon_button"></div>
                                      <div style="background-image:url(<?php echo base_url();?>assets/mcp/images/add.png);float:right;" title="Add Predifine" class="icon_button" onClick="window.location.href='<?php echo base_url();?>mcp/autocontrole/addeditprocedure'" id="btn_add"></div>
                                    </td>
                                  </tr>
                                </tbody>
                            </table>
                          </td>
                        </tr>
                        <tr>
                          <td align="center">
                            <?php echo form_open_multipart( "mcp/autocontrole/searchProcedure",array( 'method'=>"post" ,'id'=>"frm_autocontrole_procedure_srch", 'name'=>"frm_autocontrole_procedure_srch")); ?>
                            <table width="100%" cellspacing="0" cellpadding="0" border="0">
                              <tbody>
                                <tr>
                                  <td style="padding-top:5px">
                                    <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/pink_table_bg.jpg) left repeat; text-align:left">
                                      <tbody>
                                        <tr>
                                          <td valign="middle" height="20" bgcolor="#003366" align="left" style="border:#003366 1px solid; padding-left:5px" class="whiteSmallBold"><?php echo _('Search procedure'); ?></td>
                                        </tr>
                                        <tr>
                                          <td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid; padding:5px">
                                            <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                              <tbody>
                                                <tr>
                                                  <td width="69" height="22" class="blackMediumNormal"><?php echo _('Search By'); ?></td>
                                                  <td width="126">
                                                    <select name="search_type" class="textbox" required="required">
                                                         <option value=""><?php echo _('Select'); ?></option>
                                                        <option value="id"><?php echo _('ID'); ?></option>
                                                        <option value="name" ><?php echo _('Name'); ?></option>
                                                        <option value="types" ><?php echo _('Type'); ?></option>
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
                                          <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/pink_table_bg.jpg) left repeat; text-align:left;" <?php if( $this->router->fetch_method() != 'searchProcedure') { ?> id="auto_procedure" <?php } ?> class="auto_procedure_table">
                                            <thead>
                                              <tr>
                                                <td width="5%"  class="whiteSmallBold"></td>
                                               <td width="10%" class="whiteSmallBold"><?php echo _( 'Name DCH' ); ?></td>
                                                <td width="10%" class="whiteSmallBold"><?php echo _( 'Name FR' ); ?></td>
                                                 <td width="10%" class="whiteSmallBold"><?php echo _( 'Name EN' ); ?></td>
                                                <td width="20%" class="whiteSmallBold"><?php echo _( 'Type' ); ?></td>
                                                <td width="20%" class="whiteSmallBold"><?php echo _('Options'); ?></td>
                                              </tr>
                                            </thead>
                                            <tbody>
                                              <?php
                                              if( !empty( $procedures ) ){
                                                foreach ( $procedures as $key => $procedure ) { ?>
                                                  <tr data-id="<?php echo $procedure[ 'id' ]; ?>" class="proc_row">
                                                    <td width="5%"  bgcolor="#FFFFFF" class="blackMediumNormal">
                                                      <?php if( $this->router->fetch_method() != 'searchProcedure') { ?>
                                                        <img width="16" class="dragrow_autocont" height="16"  border="0" src="<?php echo base_url(); ?>assets/mcp/images/dragable.png" alt="Drag" title ="Drag" >
                                                      <?php } ?>
                                                    </td>
                                                    <td width="10%" bgcolor="#FFFFFF" class="blackMediumNormal">
                                                      <?php  echo $procedure[ 'name_dch' ]; ?>
                                                    </td>
                                                    <td width="10%" bgcolor="#FFFFFF" class="blackMediumNormal">
                                                      <?php  echo $procedure[ 'name_fr' ]; ?>
                                                    </td>
                                                    <td width="10%" bgcolor="#FFFFFF" class="blackMediumNormal">
                                                     <?php  echo $procedure[ 'name' ]; ?>
                                                    </td>
                                                    <td width="20%"  bgcolor="#FFFFFF" class="blackMediumNormal">
                                                        <?php  if( $procedure[ 'types' ] != '' ){
                                                          $types = json_decode( $procedure[ 'types' ],true );
                                                          $name  = '';
                                                          foreach ( $types as $key => $type_id ) {
                                                            if( array_key_exists( $type_id, $company_type ) ){
                                                              $name .=  ucfirst( $company_type[ $type_id ] ).', ';
                                                            }
                                                          }
                                                          $name = rtrim( $name ,',' );
                                                          echo $name;
                                                          }else{
                                                            echo '-';
                                                          } ?>
                                                    </td>
                                                    <td width="20%" class="whiteSmallBold" bgcolor="#FFFFFF"  >
                                                      <span class="blackMediumNormal autocontrole_operations" style="padding:5px">
                                                        <?php if( $procedure[ 'file_dch' ] != '' ){ ?>
                                                            <a href="<?php echo base_url(); ?>assets/images/predifineAuto_img/<?php echo $procedure[ 'file_dch' ]?>" target="_blank">
                                                              <img width="16" height="16"  border="0" src="<?php echo base_url(); ?>assets/mcp/images/pdf.png" alt="DCH PDF" title="DCH PDF" >
                                                            </a>
                                                          <?php } ?>
                                                          <?php if( $procedure[ 'file_fr' ] != '' ){ ?>
                                                            <a href="<?php echo base_url(); ?>assets/images/predifineAuto_img/<?php echo $procedure[ 'file_fr' ]?>" target="_blank">
                                                              <img width="16" height="16"  border="0" src="<?php echo base_url(); ?>assets/mcp/images/pdf.png" alt="FR PDF" title="FR PDF" >
                                                            </a>
                                                          <?php } ?>
                                                          <?php if( $procedure[ 'file' ] != '' ){ ?>
                                                            <a href="<?php echo base_url(); ?>assets/images/predifineAuto_img/<?php echo $procedure[ 'file' ]?>" target="_blank">
                                                              <img width="16" height="16"  border="0" src="<?php echo base_url(); ?>assets/mcp/images/pdf.png" alt="PDF" title="EN PDF" >
                                                            </a>
                                                          <?php } ?>
                                                          <img width="16" height="16" border="0" src="<?php echo base_url(); ?>assets/mcp/images/edit.jpg" alt="Edit" title="Edit" onclick="window.location.href='<?php echo base_url(); ?>mcp/autocontrole/addeditprocedure/<?php echo $procedure[ 'id' ]; ?>'" style="cursor:pointer">
                                                          <img width="16" height="16" class="delete_procedure" border="0" src="<?php echo base_url(); ?>assets/mcp/images/delete.jpg" alt="Delete" title="Delete" data-id="<?php echo $procedure[ 'id' ]; ?>">
                                                        </span>
                                                    </td>
                                                  </tr>
                                              <?php }
                                            }else{ ?>
                                             <tr>
                                                <td  colspan="5" bgcolor="#FFFFFF" style="padding:10px;font-size:14px;">
                                                   <p><?php echo _( 'No Procedures are there.' ); ?></p>
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