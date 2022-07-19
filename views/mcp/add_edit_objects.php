<?php 
$lang                   = get_lang( $_COOKIE['locale'] );
$id                     = '';
$object_name_fr         = '';
$object_name_dch        = '';
$object_name_nl         = '';
$object_name            = '';
$type_id                = '';
$most_frequently        = 0;
$group_id               = '';
$group_id_nl            = '';
$related_predifined     = array();
$company_type_saved     = array();
$related_type_ids       = array();
if( isset( $object_details ) && !empty( $object_details ) ) {
  $id                   = $object_details[ 'o_id' ];
  $object_name          = $object_details[ 'object_name' ];
  $object_name_dch      = $object_details[ 'object_name_dch' ];
  $object_name_nl       = $object_details[ 'object_name_nl' ];
  $object_name_fr       = $object_details[ 'object_name_fr' ];
  $type_id              = $object_details[ 'type_id' ];
  $most_frequently      = $object_details[ 'most_frequently' ];
  $group_id             = $object_details[ 'group_id' ];
  $group_id_nl          = $object_details[ 'group_id_nl' ];
  if( $object_details[ 'related_predifined' ] ){
    $related_predifined             = json_decode( $object_details[ 'related_predifined' ] ,true);    
  }
  
  $company_type_saved             = explode( "#", $object_details[ 'company_type' ] );
  $related_type_ids               = explode( "#", $object_details[ 'related_type_ids' ] );
}
?>
<head>
<link href="<?php echo base_url(); ?>assets/mcp/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/mcp/css/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />
<style type="text/css">
.page_caption h3{
	font-family: Verdana, Arial, Helvetica, sans-serif;
    font-size: 13px;
    font-weight: bold;
    color: #667297;
    margin: 0px;
    padding: 0px;
}
.edit_obj h1 {
  color: #000000;
  font-family: Geneva,Arial,Helvetica,sans-serifL;
  font-size: 22px;
  font-weight: bold;
  margin: 0;
  padding: 0;
}
.page_caption h3 {
  color: #667297;
  font-family: Verdana,Arial,Helvetica,sans-serif;
  font-size: 13px;
  font-weight: bold;
  margin: 0;
}
.edit_obj h3 {
  color: #667297;
  font-family: Verdana,Arial,Helvetica,sans-serif;
  font-size: 13px;
  font-weight: bold;
  margin: 0;
  padding: 0;
}
.reverse_data_obj .icon_button {
  background-color: #ffffff;
  background-position: center center;
  background-repeat: no-repeat;
  border-color: #e8e8e8 #c1c1c1 #c1c1c1 #e8e8e8;
  border-style: solid;
  border-width: 1px 2px 2px 1px;
  height: 26px;
  margin-left: 2px;
  padding: 4px;
  width: 26px;
}
table{
border-collapse:inherit;
}
.edit_obj #menu ul.MenuBarHorizontal li a {
  padding: 0.5em .80em 0.5em 1em !important;
}

</style>
<script type="text/javascript">
  var delete_object         = '<?php echo _("Would you like to delete this object") ?>';
  var select_text           = '<?php echo _("Select") ?>';
  var related_predifined    = '<?php echo _("Related predifined") ?>';
</script>
</head>
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
                  echo form_open_multipart("mcp/autocontrole/updateObjects",array('method'=>"post" ,'id'=>"frm_autocontrole_objects_addedit", 'name'=>"frm_autocontrole_objects_addedit"));
                  ?>
                  <table width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                      <tr>
                        <td align="center" style="padding:15px 0px 10px 0px">
                          <table width="98%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/bg.jpg) left top repeat-x;" class="page_caption">
                            <tbody class="reverse_data_obj">
                              <tr>
                                  <td width="3%" align="left"><h3><?php if( empty( $object_details ) ){ echo _('Add Object' ); }else{ echo _('Edit Object' ); } ?>&nbsp;</h3></td>
                                  <td width="40%" align="left">
                                    <a target="_blank" href="<?php echo base_url(); ?>mcp/autocontrole/object_categories">
                                      <span style="font-size: 12px;color: blue;">
                                        <?php echo _( 'Category managment'); ?>
                                      </span>
                                    </a>
                                  </td>
                                  <td width="3%" align="left">
                                    <div class="icon_button" style="height: 17px !important;width: 17px !important;">
                                      <a title="Go Back" href="<?php echo base_url('mcp/autocontrole/objects'); ?>"><img src="<?php echo base_url();?>assets/mcp/images/undo.jpg" alt="Go Back"></a>
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
                                <td height="20" bgcolor="#003366" align="left" style="padding-left:10px;" class="whiteSmallBold" colspan="6"><?php if( empty( $object_details ) ){ echo _('Add Object' ); }else{ echo _('Edit Object' ); } ?></td>
                              </tr>
                              <tr>
                                <td height="10" colspan="2"></td>
                              </tr>
                              <tr>
                                <td height="30">
                                  <?php echo _( "Name" ); ?> <span class="red_star">*</span>:
                                </td>
                                  <td>
                                  NL_BE:
                                  <input type="text" class="textbox" name="object_name_dch" id="object_name_dch" required="required" placeholder="Belgie" value="<?php echo $object_name_dch; ?>">
                                </td>
                                <td>
                                  NL:
                                  <input type="text" class="textbox" name="object_name_nl" id="object_name_nl" required="required" placeholder="Netherland" value="<?php echo $object_name_nl; ?>">
                                </td>
                                <td>
                                  FR:
                                  <input type="text"  class="textbox" name="object_name_fr" id="object_name_fr" required="required" placeholder="French" value="<?php echo $object_name_fr; ?>">
                                </td>
                                <td>
                                  EN:
                                  <input type="text" class="textbox" name="object_name" id="object_name" required="required" placeholder="English" value="<?php echo $object_name; ?>">
                                </td>
                                <td>
                                  <?php if(isset($prev_n_nxt_id['prev_id']) && $prev_n_nxt_id['prev_id']!='' ) { ?>
                                  <a href="<?php echo base_url('mcp/autocontrole/editObjects/'.$prev_n_nxt_id['prev_id']); ?>"><img src="<?php echo base_url(); ?>assets/images/left-arrow.png" title="Previous" alt="Previous"></a>
                                  <?php }
                                  if(isset($prev_n_nxt_id['next_id']) && $prev_n_nxt_id['next_id']!='' ) { ?>
                                  <a href="<?php echo base_url('mcp/autocontrole/editObjects/'.$prev_n_nxt_id['next_id']); ?>"><img src="<?php echo base_url(); ?>assets/images/right-arrow.png" title="Next" alt="Next"></a>
                                  <?php } ?>
                                </td>


                              </tr>
                              <tr>
                                <td><?php echo _( 'Select Category' ) ?></td>
                                <td>
                                <?php  if( !empty( $objects_category ) ){ ?>
                                     <select style="width:145px"  class="textbox" name="type_id">
                                        <?php 
                                          if( !empty( $objects_category ) ){ 
                                            foreach ( $objects_category as $key => $value ) { ?>
                                              <option <?php if( $value[ 'id' ] == $type_id ){ echo 'selected="selected"'; } ?> value="<?php  echo $value[ 'id' ]; ?>"><?php echo $value[ 'object_cat_name' ]; ?></option>
                                            <?php }
                                          }
                                        ?>
                                    </select>
                                    <?php }else{
                                      echo _( 'Please create Some Objects Category First');
                                      } ?>
                                </td>
                              </tr>
                               <tr>
                                <td height="30">
                                  <?php echo _('Select Group'); ?> NL_BE<span class="red_star">*</span> :
                                </td>
                                <td>
                                  <select style="width:145px" class="textbox" name="group_id" required>
                                    <option value="" ><?php echo _( "Select" ); ?></option>
                                   <?php 
                                    if( !empty( $all_groups ) ){
                                      foreach ( $all_groups as $key => $group ) { ?>
                                          <option <?php if( $group_id != "" &&  $group_id == $group[ 'id' ] ){ echo "selected='selected'"; } ?> value="<?php echo $group[ 'id' ]; ?>" ><?php echo $group[ 'temp_group_name' ]; ?></option>
                                     <?php }
                                     } ?>
                                  </select>
                                </td>
                              </tr>
                              <tr>
                                <td height="30">
                                  <?php echo _('Select Group'); ?> NL<span class="red_star">*</span> :
                                </td>
                                <td>
                                  <select style="width:145px" class="textbox" name="group_id_nl" required>
                                    <option value="" ><?php echo _( "Select" ); ?></option>
                                   <?php 
                                    if( !empty( $all_nl_groups ) ){
                                      foreach ( $all_nl_groups as $key => $group ) { ?>
                                          <option <?php if( $group_id_nl != "" &&  $group_id_nl == $group[ 'id' ] ){ echo "selected='selected'"; } ?> value="<?php echo $group[ 'id' ]; ?>" ><?php echo $group[ 'temp_group_name' ]; ?></option>
                                     <?php }
                                     } ?>
                                  </select>
                                </td>
                              </tr>
                              <tr>
                                <td height="30">
                                  <?php echo _('Company Type'); ?><span class="red_star">*</span> :
                                </td>
                                <td>
                                  <select style="width:145px"   class="select_subtype textbox" name="company_type[]" multiple="" required>
                                   <?php 
                                    if( !empty( $company_type ) ){
                                      foreach ( $company_type as $key => $value ) {
                                        if( in_array( $value[ 'id' ], $company_type_saved ) ){ ?>
                                          <option value="<?php echo $value[ 'id' ]; ?>" selected="selected" >
                                            <?php echo $value[ 'company_type_name' ]; ?>
                                          </option>
                                        <?php }else{ ?>
                                          <option value="<?php echo $value[ 'id' ]; ?>" >
                                            <?php echo $value[ 'company_type_name' ]; ?>
                                          </option>
                                        <?php }
                                      }
                                    } ?>
                                  </select>
                                </td>
                              </tr>
                               <tr>
                                <td height="30">
                                  <?php echo _('Related to'); ?><span class="red_star">*</span> :
                                </td>
                                <td>
                                  <select style="width:145px"  class="select_autocontrole_type textbox" name="related_type_ids[]" multiple="" required>
                                   <?php 
                                   $use_auto_by_key_value = array( );
                                    if( !empty( $autocontrole_type ) ){
                                      foreach ( $autocontrole_type as $key => $value ) {
                                        if( in_array( $value[ 'id' ], $related_type_ids ) ){
                                          $use_auto_by_key_value[ $value[ 'id' ] ] = $value[ 'type_name' ];
                                         ?>
                                          <option value="<?php echo $value[ 'id' ]; ?>" selected="selected" ><?php echo $value[ 'type_name' ]; ?>
                                          </option>
                                        <?php }else{ ?>
                                          <option value="<?php echo $value[ 'id' ]; ?>" >
                                            <?php echo $value[ 'type_name' ]; ?>
                                          </option>
                                        <?php }
                                      }
                                    } ?>
                                  </select>
                                </td>
                              </tr>
                             <?php 
                              if( isset( $predifined_options ) && !empty( $predifined_options ) ){
                                foreach ( $predifined_options as $key => $predifined ) { ?>
                                  <tr id="rel_pred<?php echo $key; ?>" class="related_pred_row" data-rel-to_type="<?php echo $key; ?>">
                                    <td height="30">
                                    <input type="hidden" value="<?php echo $key; ?>" name="related_to[]">
                                      <?php echo _('Related predifined'); ?><span class="red_star">*</span> :
                                    </td>
                                    <td>
                                      <span class="selected_pred_text"><?php if( array_key_exists( $key, $use_auto_by_key_value ) ){ echo $use_auto_by_key_value[ $key ]; } ?>:</span>
                                      <select style="width:145px"  class="select_predifined textbox" name="related_predifined[]" required>
                                        <option value=""><?php echo _( "Select" ); ?> </option>
                                        <?php if( !empty( $predifined ) ){ ?>
                                          <?php foreach ( $predifined  as $k => $value ) { ?>                                     
                                            <option value="<?php echo $value[ 'id' ]; ?>" <?php if( array_key_exists( $key, $related_predifined ) && $related_predifined[ $key ] == $value[ 'id' ] ){ ?> selected="selected" <?php } ?> ><?php echo $value[ 'name' ]; ?></option>
                                         <?php }
                                        } ?>
                                     </select>
                                    </td>
                                  </tr>
                                <?php }
                              } ?>
                                <tr>
                                  <td height="30">
                                    <?php echo _('Most frequently used'); ?>:
                                  </td>
                                  <td>
                                    <input type="checkbox" name="most_frequently" <?php if( $most_frequently == '1' ){ echo "checked"; }?> />
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
                                                  <td width="37%" align="right" style="padding-right:25px">&nbsp;</td>
                                                  <td>
                                                    <input type="hidden" value="<?php echo $id;?>" name="id"></input>
                                                    <button class="btnWhiteBack" type="submit" value="Submit"><?php echo _( "Submit" );?></button>
                                                    <?php if( isset( $id ) && $id != ''){ 
                                                    echo form_submit( array( 'id'=>'edit_nd_next','name'=>'action','value'=>'Save and next','class'=>'btnWhiteBack add_pre_defined','onClick'=>"return Checkempty()") );
                                                    }  ?>
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
<script type="text/javascript">
  $( document ).on( 'click' , '.select_autocontrole_type option' , function( ){
   var selected_predifined = $( document ).find( '.select_predifined' ).val( );
   var selected_type       = $( this ).val( );
   var all_selected_type   = $( this ).parents( 'select' ).val( );
   var selected_text       = $( this ).text( );
   var $this               = $( this );
   $.ajax({
        url: base_url + 'mcp/autocontrole/get_related_predifineds',
        data : {
            selected_type   : selected_type
        },
        async: !1,
        type: "POST",
        dataType : 'json',
        success: function( data ) {
          try{
            var existing_element = [];
              $( document ).find( '.related_pred_row' ).each( function( ){
                existing_element.push( $( this ).attr('data-rel-to_type' ) );
                if( jQuery.inArray( $( this ).attr('data-rel-to_type' ), all_selected_type ) === -1 ){
                  $( this ).remove( );
                }
              });
            if( jQuery.inArray( selected_type, all_selected_type ) !== -1 && jQuery.inArray( selected_type, existing_element ) === -1 ){
               $( '.select_predifined' ).val( selected_predifined ); 
              var option_html;
              option_html+= '<tr id="rel_pred'+selected_type+'" data-rel-to_type="'+selected_type+'" class="related_pred_row" required>';
                option_html+= '<td>';
                option_html+= '<input type="hidden" value="'+selected_type+'" name="related_to[]">';
                option_html+=  related_predifined;
                option_html+= '<span class="red_star">*</span> :';
                option_html+= '</td>';
                option_html+= '<td><span class="selected_pred_text">';
                    option_html+= selected_text+' :</span>';
                    option_html+= '<select name="related_predifined[]" class="select_predifined textbox" style="width:145px">';
                      option_html+= '<option>';
                        option_html+= select_text;
                      option_html+= '</option>';
                      if( data.length > 0 ){
                        $( data ).each( function( index,value ){
                          option_html+= '<option value="'+value.id+'">';
                            option_html+= value.name;
                          option_html+= '</option>';
                        });
                      }
                    option_html+= '</select>';      
                option_html+= '</td>';
              option_html+= '</tr>';
               $this.closest( 'tr' ).after( option_html );              
            }
             // if( selected_predifined ){
             //  $( '.select_predifined' ).val( selected_predifined );              
             // }
          }catch( e ){
            $( '.select_predifined' ).html( '' );
            console.log( e );
          }
        }
    });
  });
</script>