 <div style="width:100%">
    <table width="100%" cellspacing="0" cellpadding="0" border="0">
      <tbody>
        <tr>
          <td valign="top" align="center"><table width="98%" cellspacing="0" cellpadding="0" border="0">
            <tbody>
              <tr>
                <td valign="middle" align="center" style="border:#8F8F8F 1px solid;">
				          <?php  
                  if( !empty( $domains ) ){
                    $url = 'updateDomain';
                  }else{
                    $url = 'domain_addedit';
                  }
                  echo form_open_multipart("rp/autocontrole/".$url."",array('method'=>"post" ,'id'=>"frm_autocontrole_domain_addedit", 'name'=>"frm_autocontrole_domain_addedit")); 
                  if( !empty( $domains ) ){ ?>
                    <input type="hidden" name="id" value="<?php echo $domains[ 'id' ] ?>" >
                  <?php } ?>
                  <table width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                      <tr>
                        <td align="center" style="padding:15px 0px 10px 0px">
                          <table width="98%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/bg.jpg) left top repeat-x;" class="page_caption">
                            <tbody>
                              <tr>
                                  <td width="94%" align="left"><h3> <?php echo _('Domains'); ?></h3></td>
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
                                <td height="20" bgcolor="#003366" align="left" style="padding-left:10px;" class="whiteSmallBold" colspan="4">
                                  <?php if( !empty( $domains ) ){ 
                                    echo _('Edit Domain'); 
                                     }else{ 
                                      echo _('Add Domain');
                                    } ?>
                                </td>
                              </tr>
                              <tr>
                                <td height="10" colspan="2"></td>
                              </tr>
                              <tr>
                                <td width="10%" height="30" class="wd_text">
                                  <?php echo _('Domains'); ?><span class="red_star">*</span>
                                </td>
                                <?php 
                                  $domain_name    = '';
                                  $domain_name_fr = '';
                                  $domain_name_dch = '';
                                  if( isset( $domains ) ){
                                    $domain_name    = $domains[ 'domain_name' ]; 
                                    $domain_name_fr = $domains[ 'domain_name_fr' ]; 
                                    $domain_name_dch = $domains[ 'domain_name_dch' ]; 
                                  }
                                ?>
                                
                                  <td width="30%" height="31" style="padding-left:10px;">
                                  <?php echo _( 'Dutch' ) ?>:
                                  <?php echo form_input(array('type'=>"text",'style'=>"width:140px", 'class'=>"textbox",'id'=>"domain_name_dch",'name'=>"domain_name_dch", 'placeholder'=>"DU",'required'=> 'required', 'value' => $domain_name_dch ,'title'=> 'Dutch') );?>
                                </td>
                                <td width="30%" height="31" style="padding-left:10px;">
                                  <?php echo _( 'French' ) ?>:
                                  <?php echo form_input(array('type'=>"text",'style'=>"width:140px", 'class'=>"textbox",'id'=>"domain_name_fr",'name'=>"domain_name_fr", 'placeholder'=>"FR",'required'=> 'required', 'value' => $domain_name_fr ,'title'=>"French") );?>
                                </td>
                                <td width="30%" height="31" style="padding-left:10px;">
                                  <?php echo _( 'English' ) ?>:
                                    <?php echo form_input(array('type'=>"text",'style'=>"width:140px", 'class'=>"textbox",'id'=>"domain_name",'name'=>"domain_name", 'placeholder'=>"EN" ,'required'=> 'required' , 'value' => $domain_name ,'title'=>'English') );?>
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
                                                      if( isset( $domains ) ){ 
                                                        echo form_submit( array( 'id'=>'edit','name'=>'action','value'=>'EDIT DOMAIN','class'=>'btnWhiteBack') );
                                                      }else{
                                                        echo form_submit(array('id'=>'add','name'=>'action','value'=>'ADD DOMAIN','class'=>'btnWhiteBack') );
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