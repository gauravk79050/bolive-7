<script type="text/javascript">
  var no_type_id = '<?php echo _( "No companytype present" ); ?>'
</script>
<div style="width:100%">
  <!-- start of main body -->
  <table width="100%" cellspacing="0" cellpadding="0" border="0">
    <tbody>
      <tr>
        <td valign="top" align="center"><table width="98%" cellspacing="0" cellpadding="0" border="0">
          <tbody>
            <tr>
              <td valign="top" align="center" style="border:#003366 1px solid; padding:15px 0px 0px 0px"><table width="98%" cellspacing="0" cellpadding="0" border="0">
                <tbody>
                  <tr>
                    <td align="center" style="padding-bottom:5px"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/bg.jpg) left top repeat-x;" class="page_caption">
                      <tbody>
                        <tr>
                          <td width="50%" align="left"><h3><?php echo _('Company Type Manager'); ?></h3></td>
                          <td width="50%" align="right"><div onclick="history.back();" title="Back" style="background-image:url(<?php echo base_url(); ?>assets/mcp/images/undo.jpg); cursor:pointer; float:right" class="icon_button"></div>
                            <div style="background-image:url(<?php echo base_url(); ?>assets/mcp/images/add.png);float:right;" title="<?php echo _('Add New Company Type'); ?>" class="icon_button" onClick="window.location.href='<?php echo base_url();?>mcp/company_type/company_type_add'" id="btn_add"></div></td>
                          </tr>
                        </tbody>
                      </table></td>
                    </tr>
                    <tr>
                      <td align="center"><table width="100%" cellspacing="0" cellpadding="0" border="0">
                        <tbody>
                          <tr>
                            <td style="padding-top:5px"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/pink_table_bg.jpg) left repeat; text-align:left;">
                              <tbody>
                                <tr>
                                  <td valign="middle" height="20" bgcolor="#003366" align="left" style="border:#003366 1px solid; padding-left:5px" class="whiteSmallBold"><?php echo _('Search Company Type'); ?></td>
                                </tr>
                                <tr>
                                  <td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid; padding:5px"><table width="100%" cellspacing="0" cellpadding="0" border="0">
                                    <?php echo form_open("mcp/company_type",array('name'=>'searchform','id'=>'searchform'));?>
                                    <tbody>
                                      <tr>
                                        <td width="69" height="22" class="blackMediumNormal"><?php echo _('Search By'); ?></td>
                                        <td width="126"><?php $options=array('0'=>'- - '._('Search By').' - -','id'=>'ID','company_type'=>'Type Name'); echo form_dropdown('search_by', $options, 'id');
                                          ?></td>
                                          <td width="109" class="blackMediumNormal"><?php echo _('Search Keyword'); ?></td>
                                          <td width="160"><?php echo form_input(array('id'=>"search_keyword" ,'name'=>"search_keyword"));?></td>
                                          <td width="345"><span style="padding:0px 3px 3px 0px" > <?php echo form_submit(array('id'=>'search','name'=>'search','value'=>_('SEARCH'),'class'=>'btnWhiteBack'));?> <?php echo form_button(array('type'=>'reset','content'=>_('RESET'),'value'=>'true','class'=>'btnWhiteBack'));?> </span> </td>
                                        </tr>
                                     </tbody>
                                     <?php echo form_close();?>

                                     <script type="text/javascript">
                                      var frmvalidator = new Validator("searchform");
                                      frmvalidator.EnableMsgsTogether();
                                      frmvalidator.addValidation("search_by","dontselect=0","<?php echo _('Please select a column on which to search'); ?>");
                                      frmvalidator.addValidation("search_keyword","req","<?php echo _('Please enter search keyword'); ?>");

                                    </script>

                                  </table></td>
                                </tr>
                              </tbody>
                            </table></td>
                          </tr>
                          <tr>
                            <td height="22" align="right">
                              <div style="float:right; width:80%"> 
                                <?php /*?><span class="paging_nolink">&lt;&lt;<?php echo _('Vorige'); ?></span>&nbsp;<span class="paging_selected">1</span>&nbsp;<span class="paging_nolink"><?php echo _('Volgende'); ?>&gt;&gt;</span><?php */?> 
                              </div>
                            </td>
                          </tr>
                          <tr>
                            <td bgcolor="#003366"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/pink_table_bg.jpg) left repeat; text-align:left;">
                              <tbody>
                                <tr>
                                  <td width="7%" class="whiteSmallBold"><?php echo _('ID'); ?></td>
                                  <td width="13%" class="whiteSmallBold"><?php echo _('Company Type Name'); ?></td>
                                  <td width="20%" class="whiteSmallBold"><?php echo _('Status'); ?></td>
                                  <td width="20%" class="whiteSmallBold"><?php echo _('Default THEME'); ?></td>
                                  <td width="50%" align="right" style="padding-right:40px" class="whiteSmallBold"><?php echo _('Options'); ?></td>
                                </tr>
                                <tr>
                                  <td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid" colspan="5">
                                    <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                      <tbody>
                                       <?php if(!empty($company_type)) { ?>
                                       <?php foreach($company_type as $ct):?>

                                        <tr>
                                          <td width="7%" height="40" class="blackMediumNormal type_id"><?php  echo $ct->id;?></td>
                                          <td width="13%" class="blackMediumNormal"><?php echo $ct->company_type_name;?></td>
                                          <td width="20%" class="blackMediumNormal"><?php $options=array('ACTIVE'=>_('ACTIVE'),'INACTIVE'=>_('INACTIVE')); $js = "id='status' onChange='status($ct->id,this.value)'"; echo form_dropdown('status',$options,$ct->status,$js); ?></td>
                                          <td>
                                            <select class="company_typetheme" name="theme" onchange="save_theme_fn(<?php echo $ct->id; ?>,this.value)">
                                              <option><?php echo _( "Select" ); ?></option>
                                              <option <?php if( isset( $ct->theme ) && $ct->theme == '1' ){ echo "selected='selected'"; } ?> value="1"><?php echo _( "Retail" ); ?></option>
                                              <option <?php if( isset( $ct->theme ) && $ct->theme == '2' ){ echo "selected='selected'"; } ?> value="2"><?php echo _( "Catering" ); ?></option>
                                              <option <?php if( isset( $ct->theme ) && $ct->theme == '3' ){ echo "selected='selected'"; } ?> value="3"><?php echo _( "Medical" ); ?></option>
                                              <option <?php if( isset( $ct->theme ) && $ct->theme == '4' ){ echo "selected='selected'"; } ?> value="4"><?php echo _( "Horeca" ); ?></option>
                                            </select>
                                          </td>
                                          <td width="50%" align="right" style="padding-right:40px" class="blackMediumNormal"><span style="padding:5px" class="blackMediumNormal"><img width="16" height="16" border="0" style="cursor:pointer" onClick="window.location.href='<?php echo base_url();?>mcp/company_type/company_type_update/<?php echo $ct->id;?>'" title="Edit" alt="Edit" src="<?php echo base_url(); ?>assets/mcp/images/edit.jpg"></span></td>
                                        </tr>

                                      <?php endforeach ;?>
                                      <?php } else { ?>
                                      <tr>
                                        <td colspan="4" align="center" style="color:red;font-weight:bold;padding:5px;">
                                         <?php echo _('Sorry ! No company type found.'); ?>
                                       </td>
                                     </tr>
                                     <?php } ?>
                                   </tbody>
                                 </table>
                               </td>
                             </tr>
                           </tbody>
                         </table></td>
                       </tr>
                       <tr>
                        <td>&nbsp;</td>
                      </tr>
                    </tbody>
                  </table></td>
                </tr>
              </tbody>
            </table></td>
          </tr>
        </tbody>
      </table></td>
    </tr>
  </tbody>
</table>
<!-- end of main body -->
</div>
<script type="text/javascript">													
  function status(id,status)
  {													
   jQuery.post("<?php echo (base_url().'mcp/company_type/company_status');?>",
     {'id':id, 'status':status},
     function(status)
     {
      if(status.trim()=='status_updated')
      { 
       alert ('<?php echo _('Status of company type has been updated successfully.'); ?>');
       return true;
     }
     else
     {
       alert("<?php echo _('Sorry ! Status of company type could not be updated.'); ?>");
     }
   });
 }	 
 /*=================================================================================
=            Function to save company type THEME from mcp/company_type            =
=================================================================================*/
function save_theme_fn( type_id, theme ){
  var $jq=jQuery.noConflict();
  if( type_id ){
    $jq.ajax({
            url: base_url + section+'/company_type/save_theme',
            data : {
                'theme'   : theme,
                'type_id' : type_id,
            },
            async: !1,
            type: "POST",
            success: function( data ) {
            }
        });
  }else{
      alert( no_type_id );
    }
}
/*=====  End of Function to save company type THEME from mcp/company_type  ======*/                                                   
</script>