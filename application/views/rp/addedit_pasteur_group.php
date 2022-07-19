 <link href="<?php echo base_url(); ?>assets/mcp/new_css/jquery.timepicker.min.css" rel="stylesheet" type="text/css" />
 <link href="<?php echo base_url(); ?>assets/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
 <style type="text/css">
   .textbox_textdigit{
    background-color: #ebeff4;
    background-repeat: repeat-x;
    border: 1px solid #7f9db9;
    color: #012e49;
    font-family: Arial,Helvetica,sans-serif;
    font-size: 13px;
    font-weight: normal;
    padding: 1px;
    height: 31px;
   }
.input-append{
  width:90px;
}
.timepicker-picker table tr td:nth-child(2), .timepicker-picker table tr td:nth-child(1) {
    display: none;
}
 </style>
 <div style="width:100%">
    <table width="100%" cellspacing="0" cellpadding="0" border="0">
      <tbody>
        <tr>
          <td valign="top" align="center"><table width="98%" cellspacing="0" cellpadding="0" border="0">
            <tbody>
              <tr>
                <td valign="middle" align="center" style="border:#8F8F8F 1px solid;">
				          <?php  
                  echo form_open_multipart("rp/autocontrole/update_pasteur_group",array('method'=>"post" )); 
                  if( isset( $pasteur_group ) && !empty( $pasteur_group ) ){ ?>
                    <input type="hidden" name="id" value="<?php echo $pasteur_group[ 'id' ] ?>" >
                  <?php } ?>
                  <input type="hidden" name="country_code" value="<?php echo $country_code ?>" >
                  <table width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                      <tr>
                        <td align="center" style="padding:15px 0px 10px 0px">
                          <table width="98%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/bg.jpg) left top repeat-x;" class="page_caption">
                            <tbody>
                              <tr>
                                  <td width="94%" align="left"><h3> <?php echo _('Pasteurisation Group'); ?></h3></td>
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
                                <td height="20" bgcolor="#003366" align="left" style="padding-left:10px;" class="whiteSmallBold" colspan="4"><?php echo _('Add Pasteurisation Group'); ?></td>
                              </tr>
                              <tr>
                                <td height="10" colspan="2"></td>
                              </tr>
                              <tr>
                                <td width="9%" height="30" class="wd_text">
                                  <?php echo _('Group Name'); ?><span class="red_star">*</span>
                                </td>
                                <?php 
                                  $pasteur_name      = '';
                                  $pasteur_name_fr   = '';
                                  $pasteur_name_dch  = '';
                                  $ideal_temp        = '';
                                  $min_temp          = '';
                                  $max_temp          = '';
                                  $ideal_time        = date("H:i");
                                  $min_time          = date("H:i");
                                  $max_time          = date("H:i");
                                  if( isset( $pasteur_group ) ){
                                    $pasteur_name      = $pasteur_group[ 'pasteur_name' ]; 
                                    $pasteur_name_fr   = $pasteur_group[ 'pasteur_name_fr' ]; 
                                    $pasteur_name_dch  = $pasteur_group[ 'pasteur_name_dch' ]; 
                                    $ideal_temp        = $pasteur_group[ 'ideal_temp' ]; 
                                    $min_temp          = $pasteur_group[ 'min_temp' ]; 
                                    $max_temp          = $pasteur_group[ 'max_temp' ];
                                    $ideal_time        = $pasteur_group[ 'ideal_time' ]; 
                                    $min_time          = $pasteur_group[ 'min_time' ]; 
                                    $max_time          = $pasteur_group[ 'max_time' ]; 
                                  }
                                ?>
                                
                                  <td width="20%" height="31" style="padding-left:10px;">
                                  <?php echo _( 'Dutch' ) ?>:
                                  <?php echo form_input(array('type'=>"text",'style'=>"width:140px", 'class'=>"textbox",'id'=>"type_name_dch",'name'=>"pasteur_name_dch", 'placeholder'=>"DU",'required'=> 'required', 'value' => $pasteur_name_dch ,'title'=> 'Dutch') );?>
                                </td>
                                <td width="20%" height="31" style="padding-left:10px;">
                                  <?php echo _( 'French' ) ?>:
                                  <?php echo form_input(array('type'=>"text",'style'=>"width:140px", 'class'=>"textbox",'id'=>"type_name_fr",'name'=>"pasteur_name_fr", 'placeholder'=>"FR",'required'=> 'required', 'value' => $pasteur_name_fr ,'title'=>"French") );?>
                                </td>
                                <td width="20%" height="31" style="padding-left:10px;">
                                  <?php echo _( 'English' ) ?>:
                                    <?php echo form_input(array('type'=>"text",'style'=>"width:140px", 'class'=>"textbox",'id'=>"type_name",'name'=>"pasteur_name", 'placeholder'=>"EN" ,'required'=> 'required' , 'value' => $pasteur_name ,'title'=>'English') );?>
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
                                                <td width="18%" height="30" class="wd_text">
                                                <?php echo _('Default Temperature'); ?><span class="red_star">*</span>
                                               </td>
                                               <td width="25%" height="31" style="padding-left:10px;">
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

                                                  <?php echo form_input(array('type'=>"text", 'class'=>"textbox_textdigit",'id'=>"ideal_temp",'name'=>"ideal_temp",'required'=> 'required' , 'value' =>  $symbol.defined_money_format( $ideal_temp , $precision ) ) );?>
                                              </td>
                                               <td width="15%" height="30" class="wd_text">
                                                <?php echo _('Default Time'); ?><span class="red_star">*</span>
                                               </td>
                                               <td width="46%" height="31" style="padding-left:10px;">
                                                  <div class="input-group input-append date pasteur_timepicker">
                                                    <input class="textbox_textdigit timepicker pasteur_time" data-format="mm:ss" name="ideal_time" type="text" value="<?php echo $ideal_time;?>" id="ideal_time">
                                                    <span class="input-group-addon add-on">
                                                      <i data-time-icon="glyphicon glyphicon-time" data-date-icon="glyphicon glyphicon-calendar"></i>
                                                    </span>
                                                  </div>
                                                  <?php //echo form_input(array('type'=>"text", 'class'=>"textbox_textdigit timepicker",'id'=>"ideal_time",'name'=>"ideal_time",'required'=> 'required' , 'value' => $ideal_time ) );?>
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
                                                <td width="24%" height="30" class="wd_text">
                                                  <?php echo _('Give alert if T is between'); ?><span class="red_star">*</span>
                                                </td>
                                                <td width="1%" height="31" style="padding-left:10px;">
                                                   <?php 
                                                    $precision = 0;
                                                    $symbol = '';
                                                    if( isset( $min_temp ) ){
                                                      if(  isset( $min_temp[0] ) &&  $min_temp[0] == '-' ){
                                                        $symbol = '-';
                                                        $min_temp   = substr( $min_temp ,  1 );;
                                                      }else{
                                                        $min_temp  = $min_temp;
                                                      }

                                                      $arr = explode( '.',  $min_temp );
                                                      if( sizeof( $arr ) > 1 ){
                                                        $precision = strlen( substr( strrchr( $min_temp , "." ), 1 ) );
                                                      }
                                                     
                                                    }?>
                                                    <?php echo form_input(array('type'=>"text",'class'=>"textbox_textdigit",'id'=>"min_temp",'name'=>"min_temp",'required'=> 'required' , 'value' => $symbol.defined_money_format( $min_temp , $precision ) ) );?>

                                                </td>
                                                <td width="6%" height="30" class="wd_text">
                                                  <?php echo _('and'); ?>
                                                </td>
                                                 <td width="8%" height="31" style="padding-left:10px;">
                                                     <?php 
                                                    $precision = 0;
                                                    $symbol = '';
                                                    if( isset( $max_temp ) ){
                                                      if(  isset( $max_temp[0] ) &&  $max_temp[0] == '-' ){
                                                        $symbol = '-';
                                                        $max_temp   = substr( $max_temp ,  1 );;
                                                      }else{
                                                        $max_temp  = $max_temp;
                                                      }

                                                      $arr = explode( '.',  $max_temp );
                                                      if( sizeof( $arr ) > 1 ){
                                                        $precision = strlen( substr( strrchr( $max_temp , "." ), 1 ) );
                                                      }
                                                     
                                                    }?>
                                                    <?php echo form_input(array('type'=>"text",'class'=>"textbox_textdigit",'id'=>"max_temp",'name'=>"max_temp",'required'=> 'required' , 'value' => $symbol.defined_money_format( $max_temp ,$precision  ) ) );?>
                                                </td>

                                                <td width="30%" height="30" class="wd_text">
                                                  <?php echo _('Give alert if Time is between'); ?><span class="red_star">*</span>
                                                </td>
                                                <td width="1%" height="31" style="padding-left:10px;">
                                                  <div class="input-group input-append date pasteur_timepicker">
                                                    <input class="textbox_textdigit timepicker pasteur_time" data-format="mm:ss" name="min_time" type="text" value="<?php echo $min_time;?>" id="min_time">
                                                    <span class="input-group-addon add-on">
                                                      <i data-time-icon="glyphicon glyphicon-time" data-date-icon="glyphicon glyphicon-calendar"></i>
                                                    </span>
                                                  </div>

                                                    <?php //echo form_input(array('type'=>"text",'class'=>"textbox_textdigit timepicker",'id'=>"min_time",'name'=>"min_time",'required'=> 'required' , 'value' => $min_time ) );?>

                                                </td>
                                                <td width="6%" height="30" class="wd_text">
                                                  <?php echo _('and'); ?>
                                                </td>
                                                 <td width="30%" height="31" style="padding-left:10px;">
                                                    <?php //echo form_input(array('type'=>"text",'class'=>"textbox_textdigit timepicker",'id'=>"max_time",'name'=>"max_time",'required'=> 'required' , 'value' => $max_time ) );?>
                                                    <div class="input-group input-append date pasteur_timepicker">
                                                      <input class="textbox_textdigit timepicker pasteur_time" data-format="mm:ss" name="max_time" type="text" value="<?php echo $max_time;?>" id="max_time">
                                                      <span class="input-group-addon add-on">
                                                        <i data-time-icon="glyphicon glyphicon-time" data-date-icon="glyphicon glyphicon-calendar"></i>
                                                      </span>
                                                    </div>
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
                                                      if( isset( $pasteur_group ) ){ 
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
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/new_js/jquery.timepicker.min.js"></script>
<script type="text/javascript">
  $('.pasteur_timepicker').datetimepicker({
        pickDate: false,
        format: 'mm:ss',
        'minuteStep': 5
    });
</script>