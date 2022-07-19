<script type="text/javascript">
function get_dep_login(id,username,password)
{
   jQuery('#login_'+id).css('text-decoration','none');
   jQuery('#login_'+id).html('&nbsp;&nbsp;&nbsp;<img src="<?php echo base_url(); ?>assets/mcp/images/ajax-loader.gif">&nbsp;&nbsp;&nbsp;');
   
   jQuery.post('<?php echo base_url(); ?>dep/login',
         {
		    'btn_submit':'do_ajax_login',
			'submit':'LOGIN',
			'dep_username':username,
			'dep_password':password
			
		 },function(data){
            
			window.open('<?php echo base_url(); ?>dep');
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
                                <td width="50%" align="left"><h3><?php echo _('Data Entry Partner'); ?></h3></td>
                                <td width="50%" align="right"><div onclick="history.back();" title="Back" style="background-image:url(<?php echo base_url(); ?>assets/mcp/images/undo.jpg); cursor:pointer; float:right" class="icon_button"></div>
                                  <div style="background-image:url(<?php echo base_url(); ?>assets/mcp/images/add.png);float:right;" title="<?php echo _('Add DEP'); ?>" class="icon_button" onClick="window.location.href='<?php echo base_url(''); ?>mcp/dep/add';" id="btn_add"> </div></td>
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
                                        <td align="center" class="whiteSmallBold"><?php echo _('Action');?></td>
                                      </tr>
                                    </thead>
                                    <tbody id="partner_list">
									<?php if(!empty($dep_partners)) { foreach($dep_partners as $p) { ?>
									  <tr>
									    <td class="blackMediumNormal" height="30px"><?php echo $p->id; ?></td>
										<td class="blackMediumNormal"><?php echo $p->dep_first_name; ?></td>
										<td class="blackMediumNormal"><?php echo $p->dep_last_name; ?></td>
										<td class="blackMediumNormal"><?php echo $p->dep_email; ?></td>
										<td class="blackMediumNormal"><?php echo $p->dep_phone; ?></td>
										<td class="blackMediumNormal"><?php echo $p->dep_username; ?></td>
										<td class="blackMediumNormal"><?php echo $p->dep_password; ?></td>
										<td class="blackMediumNormal">
										   <a href="javascript:void(0);" onclick="get_dep_login('<?php echo $p->id; ?>','<?php echo $p->dep_username; ?>','<?php echo $p->dep_password; ?>');" title="<?php echo _('Login as DEP'); ?>" id="login_<?php echo $p->id; ?>"><?php echo _('LOGIN'); ?></a>
										   <a href="<?php echo base_url(); ?>mcp/dep/edit/<?php echo $p->id; ?>" title="<?php echo _('Edit Partner Details'); ?>"><?php echo _('Edit'); ?></a>
										   <a href="<?php echo base_url(); ?>mcp/dep/delete/<?php echo $p->id; ?>" title="<?php echo _('Remove DEP'); ?>" onClick="return confirm('<?php echo _("Are you sure ? you want to delete this DEP ?");?>');"><?php echo _('Delete'); ?></a>
										</td>
									  </tr>
									<?php } } else { ?>
									  <tr>
									    <td colspan="10" align="center" style="color:red; font-weight:bold;padding:10px;">
										  <?php echo _('Sorry ! No Data Entry Partner added yet.'); ?>
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
