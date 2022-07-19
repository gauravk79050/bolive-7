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
                  if( !empty( $category ) ){
                    $url = 'updatecategory_objects';
                  }else{
                    $url = 'addedit_objects_category';
                  }
                  echo form_open_multipart("mcp/autocontrole/".$url."",array('method'=>"post" ,'id'=>"frm_autocontrole_category_addedit", 'name'=>"frm_autocontrole_type_addedit")); 
                  if( !empty( $category ) ){ ?>
                    <input type="hidden" name="id" value="<?php echo $category[ 'id' ] ?>" >
                  <?php } ?>
                  <table width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                      <tr>
                        <td align="center" style="padding:15px 0px 10px 0px">
                          <table width="98%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/bg.jpg) left top repeat-x;" class="page_caption">
                            <tbody>
                              <tr>
                                  <td width="94%" align="left"><h3><?php echo _('Add Category'); ?>&nbsp;</h3></td>
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
                                  
                                  $object_name              = '';
                                  $object_name_fr           = '';
                                  $object_name_dch          = '';
                                  $cat_type                 = '';
                                if( !empty( $category ) ){
                                  $object_name              = $category[ 'object_name' ];
                                  $object_name_fr           = $category[ 'object_name_fr' ];
                                  $object_name_dch          = $category[ 'object_name_dch' ];
                                  $cat_type                 = $category[ 'cat_type' ];
                                }
                               ?>
                              <tr>
                                <td height="20" bgcolor="#003366" align="left" style="padding-left:10px;" class="whiteSmallBold" colspan="6"><?php echo _('Add Category'); ?></td>
                              </tr>
                              <tr>
                                <td height="10" colspan="2"></td>
                              </tr>
                              <tr>
                                <td height="30">
                                  <?php echo _('Name'); ?><span class="red_star">*</span> :
                                </td>
                                <td>
                                    <input type="text" class="textbox" name="object_name_dch" required="required" placeholder="DCH" value="<?php echo $object_name_dch; ?>" title="Object category DCH">
                                </td>
                                <td>
                                    <input type="text" class="textbox" name="object_name_fr" required="required" placeholder="FR" value="<?php echo $object_name_fr; ?>" title="Object category Fr">
                                </td>
                                <td>
                                    <input type="text" class="textbox" name="object_name" required="required" placeholder="EN" value="<?php echo $object_name; ?>" title="Object category EN">
                                </td>
                              </tr>
                              <tr>
                                <td height="30">
                                  <?php echo _('Type'); ?><span class="red_star">*</span> :
                                </td>
                                <td>
                                  <select class="textbox" required name="cat_type">
                                    <option value=""><?php echo _( "Select Type" );?></option>
                                    <option value="1" <?php if( $cat_type == '1' ) { echo 'selected'; }?> ><?php echo _( "Objects" );?></option>
                                    <option value="2" <?php if( $cat_type == '2' ) { echo 'selected'; }?>><?php echo _( "Materials" );?></option>
                                    <option value="3" <?php if( $cat_type == '3' ) { echo 'selected'; }?>><?php echo _( "Vermin" );?></option>
                                  </select>
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
                                                  <td width="30%" align="right" style="padding-right:25px">&nbsp;</td>
                                                  <td>
                                                    <?php 
                                                      if( isset( $category ) ){ 
                                                        echo form_submit( array( 'id'=>'edit','name'=>'action','value'=>'edit category','class'=>'btnWhiteBack') );
                                                      }else{
                                                        echo form_submit(array('id'=>'add','name'=>'action','value'=>'add category','class'=>'btnWhiteBack') );
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