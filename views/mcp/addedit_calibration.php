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
                    $url = 'rp/autocontrole/update_calibration';
                  } else {
                    $url = 'mcp/autocontrole/update_calibration';
                  }
                  echo form_open_multipart($url,array('method'=>"post" )); 
                  if( isset( $temp_group ) && !empty( $temp_group ) ){  ?>
                    <input type="hidden" name="id" value="<?php echo $temp_group[ 'id' ] ?>" >
                  <?php } ?>
                  <table width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                      <tr>
                        <td align="center" style="padding:15px 0px 10px 0px">
                          <table width="98%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/bg.jpg) left top repeat-x;" class="page_caption">
                            <tbody>
                              <tr>
                                  <td width="94%" align="left"><h3> <?php echo _('Work Room'); ?></h3></td>
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
                                <td height="20" bgcolor="#003366" align="left" style="padding-left:10px;" class="whiteSmallBold" colspan="4"><?php echo _('Add Workroom'); ?></td>
                              </tr>
                              <tr>
                                <td height="10" colspan="2"></td>
                              </tr>
                              <tr>
                                <td width="9%" height="30" class="wd_text">
                                  <?php echo _('Workroom Name'); ?><span class="red_star">*</span>
                                </td>
                                <?php 
                                  $workroom_name      = '';
                                  $workroom_name_fr   = '';
                                  $workroom_name_dch  = '';
                                  $ideal_temp         = '';
                                  $reference_temp     = '';
                            
                                  if( isset( $temp_group ) ){
                                    $workroom_name      = $temp_group[ 'workroom_name' ]; 
                                    $workroom_name_fr   = $temp_group[ 'workroom_name_fr' ]; 
                                    $workroom_name_dch  = $temp_group[ 'workroom_name_dch' ]; 
                                    $ideal_temp         = $temp_group[ 'ideal_temp' ]; 
                                    $reference_temp     = $temp_group[ 'reference_temp' ]; 
                                  }
                                ?>
                                
                                  <td width="20%" height="31" style="padding-left:10px;">
                                  <?php echo _( 'Dutch' ) ?>:
                                  <?php echo form_input(array('type'=>"text",'style'=>"width:140px", 'class'=>"textbox",'id'=>"type_name_dch",'name'=>"workroom_name_dch", 'placeholder'=>"DU",'required'=> 'required', 'value' => $workroom_name_dch ,'title'=> 'Dutch') );?>
                                </td>
                                <td width="20%" height="31" style="padding-left:10px;">
                                  <?php echo _( 'French' ) ?>:
                                  <?php echo form_input(array('type'=>"text",'style'=>"width:140px", 'class'=>"textbox",'id'=>"type_name_fr",'name'=>"workroom_name_fr", 'placeholder'=>"FR",'required'=> 'required', 'value' => $workroom_name_fr ,'title'=>"French") );?>
                                </td>
                                <td width="20%" height="31" style="padding-left:10px;">
                                  <?php echo _( 'English' ) ?>:
                                    <?php echo form_input(array('type'=>"text",'style'=>"width:140px", 'class'=>"textbox",'id'=>"type_name",'name'=>"workroom_name", 'placeholder'=>"EN" ,'required'=> 'required' , 'value' => $workroom_name ,'title'=>'English') );?>
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
                                                <?php echo _('Reference Temperature'); ?><span class="red_star">*</span>
                                               </td>
                                               <td width="47%" height="31" style="padding-left:10px;">
                                                  <?php 
                                                    $precision = 0;
                                                    $symbol = '';
                                                    if( isset( $reference_temp ) ){
                                                      if(  isset( $reference_temp[0] ) &&  $reference_temp[0] == '-' ){
                                                        $symbol = '-';
                                                        $reference_temp   = substr( $reference_temp ,  1 );;
                                                      }else{
                                                        $reference_temp  = $reference_temp;
                                                      }

                                                      $arr = explode( '.',  $reference_temp );
                                                      if( sizeof( $arr ) > 1 ){
                                                        $precision = strlen( substr( strrchr( $reference_temp , "." ), 1 ) );
                                                      }
                                                     
                                                    }?>
                                                  <?php echo form_input(array('type'=>"text", 'class'=>"textdigit",'id'=>"reference_temp",'name'=>"reference_temp",'required'=> 'required' , 'value' =>  $symbol.defined_money_format( $reference_temp , $precision ) ) );?>
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
                                <td height="30" colspan="4">
                                  <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                    <tbody>
                                      <tr>
                                        <td valign="middle" height="50">
                                          <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tbody>
                                              <tr>
                                                <td width="10%" height="30" class="wd_text">
                                                <?php echo _('Gives Alert if Difference is Higher Than'); ?><span class="red_star">*</span>
                                               </td>
                                                </td>
                                                <td width="47%" height="31" style="padding-left:10px;">
                                                   <?php 
                                                    $precision = 0;
                                                    $symbol = '';
                                                    if( isset( $ideal_temp ) ){
                                                      if(  isset( $ideal_temp[0] ) &&  $ideal_temp[0] == '-' ){
                                                        $symbol = '-';
                                                        $ideal_temp   = substr( $ideal_temp ,  1 );;
                                                      }else{
                                                        $ideal_temp  = $ideal_temp;
                                                      }

                                                      $arr = explode( '.',  $ideal_temp );
                                                      if( sizeof( $arr ) > 1 ){
                                                        $precision = strlen( substr( strrchr( $ideal_temp , "." ), 1 ) );
                                                      }
                                                     
                                                    }?>
                                                    <?php echo form_input(array('type'=>"text",'class'=>"textdigit",'id'=>"ideal_temp",'name'=>"ideal_temp",'required'=> 'required' , 'value' => $symbol.defined_money_format( $ideal_temp , $precision ) ) );?>

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
                                                      if( isset( $temp_group ) ){ 
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
