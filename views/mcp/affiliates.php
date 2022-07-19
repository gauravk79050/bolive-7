<script type="text/javascript">
function get_ap_login(id,username,password)
{
   jQuery('#login_'+id).css('text-decoration','none');
   jQuery('#login_'+id).html('&nbsp;&nbsp;&nbsp;<img src="<?php echo base_url(); ?>assets/mcp/images/ajax-loader.gif">&nbsp;&nbsp;&nbsp;');
   
   jQuery.post('<?php echo base_url(); ?>ap/aplogin',
         {
		    'btn_submit':'do_login',
			'submit':'LOGIN',
			'p_username':username,
			'p_password':password
			
		 },function(data){
            
			window.open('<?php echo base_url(); ?>ap');
			jQuery('#login_'+id).css('text-decoration','underline');
			jQuery('#login_'+id).html('LOGIN');
   });
}
</script>
<div style="width:100%">
  <!-- start of main body -->
  <table width="100%" cellspacing="0" cellpadding="0" border="0">
    <tbody>
      <tr>
        <td valign="top" align="center"><table width="98%" cellspacing="0" cellpadding="0" border="0">
            <tbody>
              <tr>
                <td valign="top" align="center" style="border:#003366 1px solid; padding:15px 0px 0px 0px"><table width="98%" cellspacing="0" 
				 cellpadding="0" border="0">
                    <tbody>
                      <tr>
                        <td align="center" style="padding-bottom:10px"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background
						  :url(<?php echo base_url(); ?>assets/mcp/images/bg.jpg) left top repeat-x;" class="page_caption">
                            <tbody>
                              <tr height="26">
                                <td width="50%" align="left"><h3><?php echo _('Affiliates'); ?></h3></td>
                                <td width="50%" align="right"><div onClick="history.back();" title="Back" style="background-image:url(<?php echo base_url(); ?>assets/mcp/images/undo.jpg); cursor:pointer; float:right" class="icon_button"></div>
                                  <div style="background-image:url(<?php echo base_url(); ?>assets/mcp/images/add.png);float:right;" title="<?php echo _('Add Affliliate'); ?>" class="icon_button" onClick="window.location.href='<?php echo base_url(''); ?>mcp/affiliates/add_affiliate';" id="btn_add"> </div></td>
                              </tr>
                            </tbody>
                          </table></td>
                      </tr>
                      <tr>
                        <td align="center"><table width="100%" cellspacing="0" cellpadding="0" border="0" class="blackMediumNormal">
                            <tbody>
                              <tr>
                                <td height="22" align="right"><div style="float:right; width:80%; padding:5px;">
                                    <?php echo $this->pagination->create_links(); ?>
									<div id="Pagination" class="Pagination"></div>
                                  </div></td>
                              </tr>
                              <tr>
                                <td><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/pink_table_bg.jpg) left repeat;border:1px solid #003366;">
                                    <thead>
                                      <tr style="background:#003366;">
                                        <td class="whiteSmallBold"><?php echo _('ID');?></td>
                                        <td class="whiteSmallBold"><?php echo _('First Name');?></td>
                                        <td class="whiteSmallBold"><?php echo _('Last Name');?></td>
                                        <td class="whiteSmallBold"><?php echo _('Email');?></td>
                                        <td class="whiteSmallBold"><?php echo _('Phone');?></td>
                                        <td class="whiteSmallBold"><?php echo _('Username');?></td>
                                        <td class="whiteSmallBold"><?php echo _('Password');?></td>
                                        <td class="whiteSmallBold"><?php echo _('Partner Code');?></td>
										                    <!-- <td class="whiteSmallBold"><?php echo _('Monthly Income');?></td> -->
                                        <td align="center" class="whiteSmallBold"><?php echo _('Action');?></td>
                                      </tr>
                                    </thead>
                                    <tbody id="partner_list">
                      									<?php if(!empty($affiliates)) { foreach($affiliates as $a) { ?>
                      									  <tr>
                      									    <td class="blackMediumNormal" height="30px"><?php echo $a->id; ?></td>
                      										<td class="blackMediumNormal"><?php echo $a->a_first_name; ?></td>
                      										<td class="blackMediumNormal"><?php echo $a->a_last_name; ?></td>
                      										<td class="blackMediumNormal"><?php echo $a->a_email; ?></td>
                      										<td class="blackMediumNormal"><?php echo $a->a_phone; ?></td>
                      										<td class="blackMediumNormal"><?php echo $a->a_username; ?></td>
                      										<td class="blackMediumNormal"><?php echo $a->a_password; ?></td>
                      										<td class="blackMediumNormal"><?php echo $a->a_code; ?></td>
                      										<!-- <td class="blackMediumNormal"><?php echo $a->a_monthly_income; ?></td> -->
                      										<td align="center" class="blackMediumNormal">
                      										   <a href="javascript:void(0);" onClick="get_ap_login('<?php echo $a->id; ?>','<?php echo $a->a_username; ?>','<?php echo $a->a_password; ?>');" title="<?php echo _('Login as Affiliate'); ?>" id="login_<?php echo $a->id; ?>"><?php echo _('LOGIN'); ?></a>
                      										   <a href="<?php echo base_url(); ?>mcp/affiliates/edit_affiliate/<?php echo $a->id; ?>" title="<?php echo _('Edit Affiliate Details'); ?>"><?php echo _('Edit'); ?></a>
                      										   <a href="<?php echo base_url(); ?>mcp/affiliates/delete_affiliate/<?php echo $a->id; ?>" title="<?php echo _('Remove Affiliate'); ?>"><?php echo _('Delete'); ?></a>
                      										</td>
                      									  </tr>
                      									<?php } } else { ?>
                      									  <tr>
                      									    <td colspan="10" align="center" style="color:red; font-weight:bold;padding:10px;">
                      										  <?php echo _('Sorry ! No affiliates added yet.'); ?>
                      										</td>
                      									  </tr>
                      									<?php } ?>
                                  </tbody>
                                </table></td>
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
  </table>
</div>