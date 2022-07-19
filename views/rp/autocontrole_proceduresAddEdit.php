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
                  if( !empty( $procedure ) ){
                    $url = 'updateProcedure';
                  }else{
                    $url = 'addeditProcedure';
                  }
                  echo form_open_multipart("rp/autocontrole/".$url."",array('method'=>"post" ,'id'=>"frm_autocontrole_Procedure_addedit", 'name'=>"frm_autocontrole_type_addedit")); 
                  if( !empty( $procedure ) ){ ?>
                  <input type="hidden" name="id" value="<?php echo $procedure[ 'id' ] ?>" >
                  <?php } ?>
                  <table width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                      <tr>
                        <td align="center" style="padding:15px 0px 10px 0px">
                          <table width="98%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/bg.jpg) left top repeat-x;" class="page_caption">
                            <tbody>
                              <tr>
                                  <td width="94%" align="left"><h3><?php echo _('Add Procedure'); ?>&nbsp;</h3><h3><a target="_blank" style="color:blue;" class="category_mng" href="<?php echo base_url(); ?>rp/autocontrole/category"><?php echo _('Category managment'); ?></a></h3></td>
                                  <td width="3%" align="right"></td>
                                  <td width="3%" align="left">
                                    <div class="icon_button"> 
                                      <img width="16" height="16" border="0" style="cursor:pointer" onclick="javascript:history.back();" title="<?php echo _('Go Back'); ?>" alt="Go Back" src="<?php echo base_url();?>assets/mcp/images/undo.jpg">
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

                              $name           = '';
                              $types          = '';
                              $cat            ='';
                              if( !empty( $procedure ) ){
                                $name           = $procedure[ 'name' ];
                                $name_fr        = $procedure[ 'name_fr' ];
                                $name_dch       = $procedure[ 'name_dch' ];
                                $types          = $procedure[ 'types' ];
                                $catg           = $procedure[ 'category' ];
                              }
                              ?>
                              <tr>
                                <td height="20" bgcolor="#003366" align="left" style="padding-left:10px;" class="whiteSmallBold" colspan="6"><?php echo _('Add Procedure'); ?></td>
                              </tr>
                              <tr>
                                <td height="10" colspan="2"></td>
                              </tr>


                              <tr class="inline-autocontrole">
                                <td class="wd_text" width="12%" height="30">
                                  <?php echo _('Procedure'); ?><span class="red_star">*</span>:
                                </td>
                                <td  width="25%">
                                 <?php echo _('Dutch'); ?>:
                                 <input type="text" class="textbox" name="name_dch" id="name_dch" required="required" placeholder="DU" value="<?php if(isset($name_dch)){ echo $name_dch; } ?>" title="Procedure name Dutch">
                               </td>
                               <td height="30">
                                <?php echo _('French'); ?>:
                              </td>
                              <td>
                                <input type="text" class="textbox" name="name_fr" id="name_fr" required="required" placeholder="FR" value="<?php if(isset($name_fr)){ echo $name_fr; } ?>" title="Procedure name French">
                              </td>
                              <td height="30" >
                                <?php echo _('English'); ?>:
                              </td>
                              <td class="inline-autocontrole">
                                <input type="text" class="textbox" name="name" id="name" required="required" placeholder="EN" value="<?php if(isset($name)){ echo $name; } ?>" title="Procedure name English">
                              </td>
                            </tr>
                            <tr>
                              <td class="wd_text" height="30">
                               <?php echo _( 'PDF Dutch' ); ?> <span class="red_star">*</span>
                             </td>
                             <td height="30">
                              <input type="file" name="upload_file_dch" <?php if( empty( $procedure ) ){ ?> required="required" <?php } ?> >
                              <?php if(!empty( $procedure ) ){ ?>
                              <a href="<?php echo base_url(); ?>assets/images/predifineAuto_img/<?php echo $procedure[ 'file_dch' ]?>" target="_blank">
                                <img width="16" height="16" class="pdf" border="0" src="<?php echo base_url(); ?>assets/mcp/images/pdf.png" alt="PDF" title="PDF" >
                              </a>
                              <?php echo $procedure[ 'file_dch' ]; ?>
                              <?php } ?>
                            </td>
                          </tr>
                          <tr>
                            <td class="wd_text" height="30">
                             <?php echo _( 'PDF French' ); ?> <span class="red_star">*</span>
                           </td>
                           <td height="30">
                            <input type="file" name="upload_file_fr" <?php if( empty( $procedure ) ){ ?> required="required" <?php } ?> >
                            <?php if(!empty( $procedure ) ){ ?>
                            <a href="<?php echo base_url(); ?>assets/images/predifineAuto_img/<?php echo $procedure[ 'file_fr' ]?>" target="_blank">
                              <img width="16" height="16" class="pdf" border="0" src="<?php echo base_url(); ?>assets/mcp/images/pdf.png" alt="PDF" title="PDF" >
                            </a>
                            <?php echo $procedure[ 'file_fr' ]; ?>
                            <?php } ?>
                          </td>
                        </tr>
                        <tr>
                          <td class="wd_text" height="30"  width="13%">
                           <?php echo _( 'PDF English' ); ?> <span class="red_star">*</span>
                         </td>
                         <td height="30">
                          <input type="file" name="upload_file" <?php if( empty( $procedure ) ){ ?> required="required" <?php } ?> >
                          <?php if(!empty( $procedure ) ){ ?>
                          <a href="<?php echo base_url(); ?>assets/images/predifineAuto_img/<?php echo $procedure[ 'file' ]?>" target="_blank">
                            <img width="16" height="16" class="pdf" border="0" src="<?php echo base_url(); ?>assets/mcp/images/pdf.png" alt="PDF" title="PDF" >
                          </a>
                          <?php echo $procedure[ 'file' ]; ?>
                          <?php } ?>
                        </td>
                      </tr>

                      <tr>
                        <td class="wd_text" height="30">
                          <?php echo _('Select Category'); ?>
                        </td>
                        <td>
                         <select name="category" class="textbox">
                          <option ><?php echo _('Select'); ?></option>
                          <?php if( !empty($category) ){ ?>
                          <?php   foreach ( $category as $key => $cat ) { ?>
                          <?php if($cat['id']==$catg) {?>
                          <option value="<?php echo $cat['id']; ?>" selected="selected"><?php echo $cat['name'] ?></option>
                          <?php }else{ ?>
                          <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name'] ?></option>
                          <?php  }
                        }
                      } ?>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td class="wd_text" height="30">
                    <?php echo _( 'Types' ); ?>:  
                  </td>
                  <td colspan="3">
                    <?php 
                    if( !empty( $company_types ) ){
                      foreach ( $company_types as $key => $type ) { 
                        if( $key% 2 == '0' ){ 
                          echo '<br>';
                          if( $types != '' ){
                            $predif_type = json_decode( $types ,true );
                          }
                        } ?>
                        <input type="checkbox" name="types[]"  value="<?php echo $type['id']?>" <?php if( $types != ''  && in_array( $type[ 'id' ],$predif_type ) ){ ?> checked ="checked" <?php } ?> >
                        <span class="company_type">
                          <?php echo ucfirst( $type[ 'company_type_name' ] ); ?>
                        </span>
                        <?php }
                      }?>
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
                                      if( isset( $procedure ) ){ 
                                        echo form_submit( array( 'id'=>'edit','name'=>'action','value'=>'EDIT PROCEDURE','class'=>'btnWhiteBack') );
                                      }else{
                                        echo form_submit(array('id'=>'add','name'=>'action','value'=>'ADD PROCEDURE','class'=>'btnWhiteBack') );
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