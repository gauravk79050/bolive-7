<script type="text/javascript">
  var select                = '<?php echo _( "Select" );?>';
  var successfully_updated  = '<?php echo _( "Successfully updated" );?>';
  var please_select_type    =  '<?php echo _( "Please Select type" );?>';
  var  delete_manuall       =  '<?php echo _( "Would you like to remove it from MCP Panel ?" );?>';
</script>

<div style="width:100%">
    <!-- start of main body -->
    <table width="100%" cellspacing="0" cellpadding="0" border="0">
      <tbody>
        <tr>
          <td valign="top" align="center"><table width="98%" cellspacing="0" cellpadding="0" border="0">
              <tbody>
                <tr>
                  <td valign="top" align="center" style="padding:15px 0px 0px 0px">
                    <table width="98%" cellspacing="0" cellpadding="0" border="0">
                      <tbody>
                        <tr>
                          <td align="center" style="padding-bottom:5px">
                            <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url();?>assets/mcp/images/bg.jpg) left top repeat-x;" class="page_caption">
                                <tbody>
                                  <tr>
                                    <td width="20%" align="left"><h3 class="title_099"><?php echo _( 'Predifined' ); ?></h3></td>
                                    <?php if( $this->session->flashdata( 'msg' ) ) { ?>
                                      <td width="60%" style="text-align: center;">
                                        <h3 class="red_star">
                                          <?php echo $this->session->flashdata( 'msg' );?>
                                        </h3>
                                      </td>
                                    <?php } ?>
                                    <td width="20%" align="right">
                                      <div onclick="history.back();" title="Back" style="background-image:url(<?php echo base_url();?>assets/mcp/images/undo.jpg); cursor:pointer; float:right" class="icon_button"></div>
                                      <div style="background-image:url(<?php echo base_url();?>assets/mcp/images/add.png);float:right;" title="Add Predifined" class="icon_button" onClick="window.location.href='<?php echo base_url();?>rp/autocontrole/addeditPredifined'" id="btn_add"></div>
                                    </td>
                                  </tr>
                                </tbody>
                            </table>
						              </td>
                        </tr>
                        <tr>
                          <td align="center">
                            <table width="100%" cellspacing="0" cellpadding="0" border="0">
                              <tbody>
                                <tr>
                                  <td style="padding-top:5px">
                                    <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/pink_table_bg.jpg) left repeat; text-align:left">
                                      <tbody>
                                        <tr>
                                          <td valign="middle" height="20" bgcolor="#003366" align="left" style="border:#003366 1px solid; padding-left:5px" class="whiteSmallBold"><?php echo _('Search Predifined'); ?></td>
                                        </tr>
                                        <tr>
                                          <td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid; padding:5px">
                                           <?php echo form_open_multipart( "rp/autocontrole/searchPredifined",array( 'method'=>"post" ,'id'=>"frm_autocontrole_predifined_srch", 'name'=>"frm_autocontrole_predifined_srch")); ?>
										                        <table width="100%" cellspacing="0" cellpadding="0" border="0">  
										                          <tbody>
                                                <tr>
                                                  <td width="69" height="22" class="blackMediumNormal"><?php echo _('Search By'); ?></td>
                                                  <td width="126">
                                                    <select name="search_type" class="textbox" required="required">
                                                      <option value=""><?php echo _('Select'); ?></option>
                                                      <option value="id"><?php echo _('ID'); ?></option>
                                                      <option value="name" ><?php echo _('Name'); ?></option>
                                                      <option value="description" ><?php echo _('Description'); ?></option>
                                                    </select>
													                       </td>
                                                  <td width="109" class="blackMediumNormal"><?php echo _('Search Keyword'); ?></td>
                                                  <td width="160">
                                                    <?php echo form_input(array('id'=>"search_keyword" ,'name'=>"search_keyword" , 'style'=>'width:140px;' , 'class'=>'textbox' ,'required' => 'required'));?>
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
                                
                                <?php  if( !empty( $predifineds ) ) {  ?>
                                  <?php  foreach( $predifineds as $key =>  $predifined ){ 
                                  $counter = 0;
                                  $size = sizeof( $predifined );
                                    foreach ( $predifined as $k => $value ) {
                                      $company_type_ids = array();
                                      $company_type_name = array();
                                      if( !empty( $value[ 'company_type' ] ) ) {
                                          $company_type_ids = explode( "#", $value[ 'company_type' ] );
                                      }
                                      $counter++; ?>
                                      <tr class="pqrop">
                                        <td bgcolor="#fff">
                                          <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/pink_table_bg.jpg) left repeat; text-align:left;">
                                            <tbody>
                                              <?php if( $counter == 1 ){ ?>
                                                <tr class="typename">
                                                  <td height="22" align="left" bgcolor="#FFFFFF" colspan="8" >
                                                    <div style="width:80%"> 
                                                      <b><?php echo  $value['type_name']; ?></b>
                                                    </div>
                                                  </td>
                                                </tr>
                                              <?php } ?>
                                              <?php if( is_numeric( $k ) ){ ?>
                                                <?php if( $counter == 1 ){ ?>
                                                  <tr>
                                                    <td width="3%"  class="">
                                                      <?php echo _( 'ID' ); ?>
                                                    </td>
                                                    <td width="7%" class="">
                                                      <?php echo _( 'Icon' ); ?>
                                                    </td>
                                                    <td width="20%" class="">
                                                      <?php echo _( 'Name' ); ?>
                                                    </td>

                                                    <td width="20%" class="">
                                                      <?php echo _( 'Description' ); ?>
                                                    </td>
                                                    <td width="20%" class="">
                                                      <?php echo _( 'Education' ); ?>
                                                    </td>
                                                    <td width="15%" class="">
                                                      <?php echo _( 'Company Type' ); ?>
                                                    </td>
                                                     <td width="10%" class="">
                                                      <?php echo _( 'Object Name' ); ?>
                                                    </td>
                                                    <td width="5%" class="" align="center">
                                                      <?php echo _('Options'); ?>
                                                    </td>
                                                  </tr>
                                                <?php } ?>
                                                  <tr>
                                                    <td valign="middle" bgcolor="#FFFFFF" style="border-left:1px solid #000;border-right:1px solid #000;" colspan="8" >
                                                      <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                                        <tbody>
                                                          <tr data-key="<?php echo $key; ?>" data-size="<?php echo $size; ?>">
                                                             <td width="3%" class="blackMediumNormal" ><?php echo $value[ 'id' ]; ?></td>
                                                             <td width="7%" >
                                                                <?php if(!empty($value[ 'icon' ])): ?>
                                                                <img width="75" height="75" class="icon_predifined" src="<?php echo base_url( ); ?>assets/images/predifineAuto_img/<?php echo $value[ 'icon' ]; ?>">
                                                                <?php endif; ?>
                                                              </td>
                                                              <td width="20%" class="blackMediumNormal">
                                                                <?php echo $value[ 'name' ].'<br><br>'.$value[ 'name_fr' ].'<br><br>'.$value[ 'name_dch' ]; ?>
                                                              </td>
                                                              <td width="20%" class="blackMediumNormal">
                                                                <?php echo $value[ 'description' ].'<br><br>'.$value[ 'description_fr' ].'<br><br>'.$value[ 'description_dch' ]; ?>
                                                              </td>
                                                               <td width="20%" class="blackMediumNormal">
                                                                <?php echo $value[ 'education' ].'<br><br>'.$value[ 'education_fr' ].'<br><br>'.$value[ 'education_dch' ]; ?>
                                                              </td>
                                                              <td width="15%" class="blackMediumNormal">
                                                                <?php if( isset( $company_type ) && !empty( $company_type ) ) {
                                                                        foreach ( $company_type as $k1 => $c_type_id ) {
                                                                          if( in_array( $c_type_id[ 'id' ], $company_type_ids ) 
                                                                             ){
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
                                                              <td width="10%" class="blackMediumNormal">
                                                                 <?php 
                                                                  if( !empty( $value[ 'object_ids' ] ) && !empty( json_decode( $value[ 'object_ids' ] ) ) ) {
                                                                      $object_ids = json_decode( $value[ 'object_ids' ] );
                                                                      $object_name = array();
                                                                      $name = 'object_name'.get_lang( $_COOKIE['locale'] );
                                                                      foreach ( $objects as $obj_key => $obj_id ) {
                                                                        if( in_array( $obj_id[ 'o_id' ], $object_ids ) ){ 
                                                                          array_push( $object_name, $obj_id[ $name ] );
                                                                        }
                                                                      }
                                                                      if( !empty( $object_name ) ) {
                                                                          echo implode( ", ", $object_name );
                                                                      } else {
                                                                          echo "--";
                                                                      }
                                                                  } else {
                                                                      echo "--";
                                                                  }?>
                                                              </td>
                                                              <td width="5%"  align="center" class="blackMediumNormal" >
                                                                <span class="blackMediumNormal" style="padding:5px">
                                                                  <img width="16" height="16" border="0" src="<?php echo base_url(); ?>assets/mcp/images/edit.jpg" alt="Edit" title="Edit" onclick="window.location.href='<?php echo base_url(); ?>rp/autocontrole/addeditPredifined/<?php echo $value[ 'id' ]; ?>'" style="cursor:pointer">
                                                                  <img width="16" height="16" class="delete_predif" border="0" src="<?php echo base_url(); ?>assets/mcp/images/delete.jpg" alt="Delete" title="Delete" data-id="<?php echo $value[ 'id' ]; ?>">
                                                                </span>
                                                              </td>
                                                           </tr>
                                                          <?php if( $size == $counter ) { $c=5;?>
                                                           <tr class="last_row">
                                                            <td colspan="8" style="background-color: #003366">&nbsp;</td>
                                                          </tr>
                                                          <?php } ?>                                                           
                                                      </table>
                                                    </td>
                                                  </tr>
                                              </tbody>
                                            </table>
                                          </td>
                                        </tr>
                                    <?php }
                                      } 

                                    } 
                                  } else { ?>
                                     <tr>
                                        <td colspan="3" style="border:#003366 1px solid; padding:10px;font-size:14px;" >
                                           <p><?php echo _('No Predifined are there.'); ?></p>
                                        </td>
                                     </tr>
                                  <?php } ?>
                                  <tr class="last_row">
                                       <td colspan="8" >&nbsp;</td>
                                  </tr>
                                  <tr >
                                    <table width="100%" class="manually_entered_items">
                                      <tbody >
                                        <tr>
                                          <td colspan="4" class="blue_text"><?php echo _("Manually Entered Values");?></td>
                                           <td colspan="3" style="color: red;"><?php echo _("USED BY");?></td>
                                        </tr>
                                        <?php if(!empty($manually)){
                                         foreach ($manually as $key => $value) { ?>                                       
                                        <tr>
                                          <td width="15%" class="yellow_text manually_created_predif_text"><?php echo $value[1] ?></td>
                                          <td width="10%" class="blue_text"><?php echo _("entered into");?></td>
                                          <td width="18%" class="yellow_text">
                                          <?php foreach ($autocontrole_type as $key1 => $type) {
                                              if($type['id']==$value[0]){
                                                echo htmlspecialchars($type['type_name']);
                                              }
                                          }?>
                                          </td>
                                          <td width="12%" class="blue_text"><?php echo _("assigne this to");?></td>
                                          <td width="10%" style="color: red;"><?php echo $value[3];?></td>
                                          <td width="15%">
                                            <select name="task_type"  class="textbox task_type">
                                                <?php foreach ($autocontrole_type as $key => $type) { ?>
                                                  <option value="<?php echo $type['id'] ?>"><?php echo $type['type_name']; ?></option>
                                                <?php } ?>
                                            </select>
                                          </td>
                                            <td width="10%">
                                              <img width="16" height="16" border="0" data-id="36" title="Delete" alt="Delete" src="<?php echo base_url(); ?>assets/mcp/images/delete.jpg" class="del_manualy_created_predif">
                                              <button class="merge_predefined" data-enterd_into_type="<?php echo $value[0]; ?>" data-company_id="<?php echo $value[2]; ?>" data-name="<?php echo $value[1] ?>" type="submit">
                                                  <?php echo _("Submit");?>
                                                </button>
                                            </td>
                                        </tr>
                                      <?php } }else{ ?>
                                              <tr>
                                               <td bgcolor="#FFFFFF" style="border-left:1px solid #000;border-right:1px solid #000; text-align:center;" colspan="6">
                                                 <p><?php echo _('No Manually predefined are there.'); ?></p>
                                              </td>
                                             </tr>
                                        <?php }?>
                                      </tbody>
                                    </table>
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