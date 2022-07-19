<script type="text/javascript">
function get_login(id,username,password)
{
   //alert( id+'--'+username+'--'+password );
   jQuery('#login_'+id).css('text-decoration','none');
   jQuery('#login_'+id).html('&nbsp;&nbsp;&nbsp;<img src="<?php echo base_url(); ?>assets/mcp/images/ajax-loader.gif">&nbsp;&nbsp;&nbsp;');
   
   jQuery.post('<?php echo base_url(); ?>cp/login/validate',
         {
		    'act':'do_login',
			'submit':'LOGIN',
			'username':username,
			'password':password
			
		 },function(data){
            
			window.open('<?php echo base_url(); ?>cp');
			jQuery('#login_'+id).css('text-decoration','underline');
			jQuery('#login_'+id).html('LOGIN');
   });
}
</script>
<?php //print_r($this->session->all_userdata()); ?>
<div style="width:100%">
  <!-- start of main body -->
  <table width="100%" cellspacing="0" cellpadding="0" border="0">
    <tbody>
      <tr>
        <td valign="top" align="center"><table width="98%" cellspacing="0" cellpadding="0" border="0">
            <tbody>
              <tr>
                <td valign="top" align="center" style="border:#003366 1px solid; padding:15px 0px 0px 0px" colspan="2"><table width="98%" cellspacing="0" 
				 cellpadding="0" border="0">
                    <tbody>
                      <tr>
                        <td align="center" style="padding-bottom:10px"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background
						  :url(<?php echo base_url(); ?>assets/mcp/images/bg.jpg) left top repeat-x;" class="page_caption">
                            <tbody>
                              <tr height="26">
                                <td width="50%" align="left"><h3><?php echo _('Companies Associated'); ?></h3></td>
                                <td width="50%" align="right"><div onclick="history.back();" title="Back" style="background-image:url(<?php echo base_url(); ?>assets/mcp/images/undo.jpg); cursor:pointer; float:right" class="icon_button"></div>
                                 
								</td>
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
										<td class="whiteSmallBold"><?php echo _('Company Name');?></td>
										<td class="whiteSmallBold"><?php echo _('Company Type');?></td>
                                        <td class="whiteSmallBold"><?php echo _('First Name');?></td>
                                        <td class="whiteSmallBold"><?php echo _('Last Name');?></td>
                                        <td class="whiteSmallBold"><?php echo _('Email');?></td>
                                        <td class="whiteSmallBold"><?php echo _('Phone');?></td>
                                        <td class="whiteSmallBold"><?php echo _('Status');?></td>
                                        <td class="whiteSmallBold"><?php echo _('Action');?></td>
                                      </tr>
                                    </thead>
                                    <tbody id="partner_list">
									<?php if(!empty($companies)) { foreach($companies as $c) { ?>
									  <tr>
									    <td class="blackMediumNormal" height="30px"><?php echo $c->id; ?></td>
										<td class="blackMediumNormal"><?php echo $c->company_name; ?></td>
										<td class="blackMediumNormal"><?php echo $c->company_type_name; ?></td>
										<td class="blackMediumNormal"><?php echo $c->first_name; ?></td>
										<td class="blackMediumNormal"><?php echo $c->last_name; ?></td>
										<td class="blackMediumNormal"><?php echo $c->email; ?></td>
										<td class="blackMediumNormal"><?php echo $c->phone; ?></td>
										<td class="blackMediumNormal"><?php echo ($c->affiliate_status)?'PAID':'UNPAID'; ?></td>
										<td class="blackMediumNormal">
										   <a href="javascript:void(0);" onclick="get_login('<?php echo $c->id; ?>','<?php echo $c->username; ?>','<?php echo $c->password; ?>');" title="<?php echo _('Login to Client\'s Control Panel'); ?>" id="login_<?php echo $c->id; ?>"><?php echo _('LOGIN'); ?></a>
										</td>
									  </tr>
									<?php } } else { ?>
									  <tr>
									    <td colspan="10" align="center" style="color:red; font-weight:bold;padding:10px;">
										  <?php echo _('Sorry ! No companies assigned yet.'); ?>
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