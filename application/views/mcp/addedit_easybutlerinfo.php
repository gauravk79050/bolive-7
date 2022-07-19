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
                    $url = 'rp/easybutler/update_easybutlerinfo';
                  } else {
                    $url = 'mcp/easybutler/update_easybutlerinfo';
                  }
                  echo form_open_multipart($url,array('method'=>"post" )); 
                  if( isset( $easybutlerinfo ) && !empty( $easybutlerinfo ) ){  ?>
                    <input type="hidden" name="id" value="<?php echo $easybutlerinfo[ 'id' ] ?>" >
                  <?php } ?>
                  <table width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                      <tr>
                        <td align="center" style="padding:15px 0px 10px 0px">
                          <table width="98%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/bg.jpg) left top repeat-x;" class="page_caption">
                            <tbody>
                              <tr>
                                  <td width="94%" align="left"><h3> <?php echo _('EB Leads'); ?></h3></td>
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
                                <td height="20" bgcolor="#003366" align="left" style="padding-left:10px;" class="whiteSmallBold" colspan="4"><?php echo _('Add EB Lead'); ?></td>
                              </tr>
                              <tr>
                                <td height="10" colspan="2"></td>
                              </tr>
                              <tr>
                                <td width="10%" height="30" class="wd_text">
                                  <?php echo _('Resturant Name'); ?><span class="red_star">*</span>
                                </td>
                                <?php 
                                 $resturant_name = '';
                                 $city = '';
                                 $name = '';
                                 $phone = '';
                                  if( isset( $easybutlerinfo ) ){
                                    $resturant_name      = $easybutlerinfo[ 'resturant_name' ]; 
                                    $city                = $easybutlerinfo[ 'city' ]; 
                                    $name                = $easybutlerinfo[ 'name' ]; 
                                    $phone                = $easybutlerinfo[ 'phone' ]; 
                                  }
                                ?>
                                
                                  <td width="20%" height="31" style="padding-left:10px;">
                                  <?php echo form_input(array('type'=>"text",'style'=>"width:140px", 'class'=>"textbox",'id'=>"resturant_name",'name'=>"resturant_name", 'placeholder'=> _('Resturant Name'),'required'=> 'required', 'value' => $resturant_name ,'title'=> _('Resturant Name') ) );?>
                                </td>
                               <td width="10%" height="30" class="wd_text">
                                <?php echo _('City'); ?><span class="red_star">*</span>
                                </td>
                                <td width="20%" height="31" style="padding-left:10px;">
                                  <?php echo form_input(array('type'=>"text",'style'=>"width:140px", 'class'=>"textbox",'id'=>"city",'name'=>"city", 'placeholder'=> _('City'),'required'=> 'required', 'value' => $city ,'title'=> _('City') ) );?>
                                </td>
                              </tr>
                              <tr>
                                <td height="30" colspan="4">
                                  <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                    <tbody>
                                      <tr>
                                        <td valign="middle" height="50">
                                          <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tbody>
                                              <tr>
                                                <td width="10%" height="30" class="wd_text">
                                                <?php echo _('Name'); ?><span class="red_star">*</span>
                                                </td>
                                                  <td width="20%" height="31" style="padding-left:10px;">
                                                    <?php echo form_input(array('type'=>"text",'style'=>"width:140px", 'class'=>"textbox",'id'=>"name",'name'=>"name", 'placeholder'=> _('Name'),'required'=> 'required', 'value' => $name ,'title'=> _('Name') ) );?>
                                                  </td>
                                                  <td width="10%" height="30" class="wd_text">
                                                <?php echo _('Phone'); ?><span class="red_star"></span>
                                                </td>
                                                  <td width="20%" height="31" style="padding-left:10px;">
                                                    <?php echo form_input(array('type'=>"text",'style'=>"width:140px", 'class'=>"textbox",'id'=>"phone",'name'=>"phone", 'placeholder'=> _('Phone'), 'value' => $phone ,'title'=> _('Phone') ) );?>
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
                                                  <td class="wd_text">
                                                    <?php 
                                                      if( isset( $easybutlerinfo ) ){ 
                                                        echo form_submit( array( 'id'=>'edit','name'=>'action','value'=>'Edit','class'=>'btnWhiteBack') );
                                                      }else{
                                                        echo form_submit(array('id'=>'add','name'=>'action','value'=>'Add','class'=>'btnWhiteBack') );
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
