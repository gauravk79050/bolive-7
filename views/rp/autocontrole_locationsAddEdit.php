 <div style="width:100%">
    <table width="100%" cellspacing="0" cellpadding="0" border="0">
      <tbody>
        <tr>
          <td valign="top" align="center"><table width="98%" cellspacing="0" cellpadding="0" border="0">
            <tbody>
              <tr>
                <td valign="middle" align="center" style="border:#8F8F8F 1px solid;">
				          <?php  
                  if( !empty( $location ) ){
                    $url = 'updateLocation';
                  }else{
                    $url = 'addeditLocation';
                  }
                  echo form_open_multipart("rp/autocontrole/".$url."",array('method'=>"post" ,'id'=>"frm_autocontrole_loc_addedit", 'name'=>"frm_autocontrole_location_addedit")); 
                  if( !empty( $location ) ){ ?>
                    <input type="hidden" name="id" value="<?php echo $location[ 'id' ] ?>" >
                  <?php } ?>
                  <table width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                      <tr>
                        <td align="center" style="padding:15px 0px 10px 0px">
                          <table width="98%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/bg.jpg) left top repeat-x;" class="page_caption">
                            <tbody>
                              <tr>
                                  <td width="94%" align="left"><h3><?php echo _('Location'); ?>&nbsp;</h3></td>
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
                                <td height="20" bgcolor="#003366" align="left" style="padding-left:10px;" class="whiteSmallBold" colspan="4"><?php echo _('Add Location'); ?></td>
                              </tr>
                              <tr>
                                <td height="10" colspan="2"></td>
                              </tr>
                              <tr>
                                <td width="10%" height="30" class="wd_text">
                                  <?php echo _('Location'); ?><span class="red_star">*</span>
                                </td>
                                <?php 
                                  $loc_name     = '';
                                  $loc_name_fr  = '';
                                  $loc_name_dch = '';
                                  if( isset( $location ) ){
                                    $loc_name     = $location[ 'loc_name' ]; 
                                    $loc_name_fr  = $location[ 'loc_name_fr' ]; 
                                    $loc_name_dch = $location[ 'loc_name_dch' ]; 
                                  }
                                  if( isset( $comp_loc_name ) ) {
                                    $loc_name     = explode( "_", $comp_loc_name )[0];
                                    $loc_name_fr  = explode( "_", $comp_loc_name )[0];
                                    $loc_name_dch = explode( "_", $comp_loc_name )[0];
                                  }
                                ?>
                                <td width="30%" height="31" style="padding-left:10px;">
                                  <?php echo _('Dutch'); ?>:
                                  <?php echo form_input(array('type'=>"text",'style'=>"width:140px", 'class'=>"textbox",'id'=>"loc_name_dch",'name'=>"loc_name_dch", 'placeholder'=>"DU",'required'=> 'required', 'value' => $loc_name_dch ,'title'=> 'Dutch') );?>
                                </td>
                                <td width="30%" height="31" style="padding-left:10px;">
                                  <?php echo _('French'); ?>:
                                  <?php echo form_input(array('type'=>"text",'style'=>"width:140px", 'class'=>"textbox",'id'=>"loc_name_fr",'name'=>"loc_name_fr", 'placeholder'=>"FR",'required'=> 'required', 'value' => $loc_name_fr ,'title'=>"French") );?>
                                </td>
                                <td width="30%" height="31" style="padding-left:10px;">
                                  <?php echo _('English'); ?>:
                                  <?php echo form_input(array('type'=>"text",'style'=>"width:140px", 'class'=>"textbox",'id'=>"loc_name",'name'=>"loc_name", 'placeholder'=>"EN" ,'required'=> 'required' , 'value' => $loc_name ,'title'=>'English') );?>
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
                                                      if( isset( $location ) ){ 
                                                        echo form_submit( array( 'id'=>'edit','name'=>'action','value'=>'EDIT LOCATION','class'=>'btnWhiteBack') );
                                                      }else{
                                                        echo form_submit(array('id'=>'add','name'=>'action','value'=>'ADD LOCATION','class'=>'btnWhiteBack') );
                                                        if( isset( $comp_loc_name ) ) {
                                                          echo "<input type='hidden' value='".$comp_loc_name."' name='comp_loc_name'>";
                                                        }
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