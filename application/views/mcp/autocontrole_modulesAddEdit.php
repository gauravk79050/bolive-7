<div style="width:100%">
    <table width="100%" cellspacing="0" cellpadding="0" border="0">
      <tbody>
        <tr>
          <td valign="top" align="center"><table width="98%" cellspacing="0" cellpadding="0" border="0">
            <tbody>
              <tr>
                <td valign="middle" align="center" style="border:#8F8F8F 1px solid;">
                  <?php  
                    if( strpos( current_url(), 'rp' ) > -1 ) {
                      $url = 'rp/autocontrole/addeditmodule';
                    } else {
                      $url = 'mcp/autocontrole/addeditmodule';
                    }
                  echo form_open_multipart( $url."",array('method'=>"post" ,'id'=>"frm_autocontrole_module_addedit", 'name'=>"frm_autocontrole_module_addedit")); 
                  if( !empty( $module ) ){ ?>
                    <input type="hidden" name="id" value="<?php echo $module[ 'id' ] ?>" >
                  <?php } ?>
                  <table width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                      <tr>
                        <td align="center" style="padding:15px 0px 10px 0px">
                          <table width="98%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/bg.jpg) left top repeat-x;" class="page_caption">
                            <tbody>
                              <tr>
                                  <td width="94%" align="left"><h3> <?php echo _('Module'); ?></h3></td>
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
                              <tr>
                                <td height="20" bgcolor="#003366" align="left" style="padding-left:10px;" class="whiteSmallBold" colspan="4"><?php echo _('Add Module'); ?></td>
                              </tr>
                              <tr>
                                <td height="10" colspan="2"></td>
                              </tr>
                              <tr>
                                <td width="13%" height="30" class="wd_text">
                                  <?php echo _('Module'); ?><span class="red_star">*</span>
                                </td>
                                <?php 
                                  $module_en   = '';
                                  $module_fr   = '';
                                  $module_dch  = '';
                                  $file_name   = '';
                                  $file_name_fr= '';
                                  $file_name_dch= '';
                                  if( isset( $module ) ){
                                    $module_en   = $module['module']; 
                                    $module_fr   = $module[ 'module_fr' ]; 
                                    $module_dch  = $module[ 'module_dch' ]; 
                                    $file_name   = $module[ 'file' ]; 
                                    $file_name_fr   = $module[ 'file_fr' ]; 
                                    $file_name_dch  = $module[ 'file_dch' ]; 
                                  }
                                ?>
                                
                                  <td width="30%" height="31" style="padding-left:10px;">
                                  <?php echo _( 'Dutch' ) ?>:
                                  <?php echo form_input(array('type'=>"text",'style'=>"width:140px", 'class'=>"textbox",'id'=>"module_dch",'name'=>"module_dch", 'placeholder'=>"DU",'required'=> 'required', 'value' => $module_dch ,'title'=> 'Dutch') );?>
                                </td>
                                <td width="30%" height="31" style="padding-left:10px;">
                                  <?php echo _( 'French' ) ?>:
                                  <?php echo form_input(array('type'=>"text",'style'=>"width:140px", 'class'=>"textbox",'id'=>"module_fr",'name'=>"module_fr", 'placeholder'=>"FR",'required'=> 'required', 'value' => $module_fr ,'title'=>"French") );?>
                                </td>
                                <td width="30%" height="31" style="padding-left:10px;">
                                  <?php echo _( 'English' ) ?>:
                                    <?php echo form_input(array('type'=>"text",'style'=>"width:140px", 'class'=>"textbox",'id'=>"module",'name'=>"module", 'placeholder'=>"EN" ,'required'=> 'required' , 'value' => $module_en ,'title'=>'English') );?>
                                </td>
                              </tr>
                              <tr>
                                <td  width="10%" height="30" class="wd_text"><?php echo _( "PDF Dutch" ); ?><span class="red_star">*</span></td>
                                <td><input type="file" name="upload_file_dch" <?php if( $file_name_dch == '' ){ ?> required="required" <?php } ?> >
                                  <?php if( $file_name_dch != ''){ ?>
                                  <input type="hidden" name="file_name_dch" value="<?php echo $file_name_dch; ?>">
                                  <a target="_blank" href="<?php echo base_url() ?>assets/autocontrole/module_pdf/<?php echo $file_name_dch; ?>">
                                  <img width="16" height="16" border="0" title="PDF" alt="PDF" src="<?php echo base_url() ?>assets/mcp/images/pdf.png">
                                  </a>
                                  <?php } ?>
                                </td>
                              </tr>
                              
                              <tr>
                                <td  width="10%" height="30" class="wd_text"><?php echo _( "PDF French" ); ?><span class="red_star">*</span></td>
                                <td><input type="file" name="upload_file_fr" <?php if(  $file_name_fr =='' ){ ?> required="required" <?php } ?> >
                                  <?php if( $file_name_fr != ''){ ?>
                                  <input type="hidden" name="file_name_fr" value="<?php echo $file_name_fr; ?>">
                                  <a target="_blank" href="<?php echo base_url() ?>assets/autocontrole/module_pdf/<?php echo $file_name_fr; ?>">
                                  <img width="16" height="16" border="0" title="PDF" alt="PDF" src="<?php echo base_url() ?>assets/mcp/images/pdf.png">
                                  </a>
                                  <?php } ?>
                                </td>
                              </tr>
                              <tr>
                                <td  width="10%" height="30" class="wd_text"><?php echo _( "PDF English" ); ?><span class="red_star">*</span></td>
                                <td><input type="file" name="upload_file"  <?php if(  $file_name == '' ){ ?> required="required" <?php } ?> >
                                  <?php if( $file_name != ''){ ?>
                                  <input type="hidden" name="file_name" value="<?php echo $file_name; ?>">
                                  <a target="_blank" href="<?php echo base_url() ?>assets/autocontrole/module_pdf/<?php echo $file_name; ?>">
                                  <img width="16" height="16" border="0" title="PDF" alt="PDF" src="<?php echo base_url() ?>assets/mcp/images/pdf.png">
                                  </a>
                                  <?php } ?>
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
                                                      if( isset( $module ) ){ 
                                                        echo form_submit( array( 'id'=>'edit','name'=>'action','value'=>'EDIT MODULE','class'=>'btnWhiteBack') );
                                                      }else{
                                                        echo form_submit(array('id'=>'add','name'=>'action','value'=>'ADD MODULE','class'=>'btnWhiteBack') );
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