<script type="text/javascript">
function get_rp_login(id,username,password)
{
   jQuery('#login_'+id).css('text-decoration','none');
   jQuery('#login_'+id).html('&nbsp;&nbsp;&nbsp;<img src="<?php echo base_url(); ?>assets/mcp/images/ajax-loader.gif">&nbsp;&nbsp;&nbsp;');
   
   jQuery.post('<?php echo base_url(); ?>rp/rplogin',
         {
		    'btn_submit':'do_login',
			'submit':'LOGIN',
			'p_username':username,
			'p_password':password
			
		 },function(data){
            
			window.open('<?php echo base_url(); ?>rp');
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
                                <td width="50%" align="left"><h3><?php echo _('Partners'); ?></h3></td>
                                <td width="50%" align="right"><div onclick="history.back();" title="Back" style="background-image:url(<?php echo base_url(); ?>assets/mcp/images/undo.jpg); cursor:pointer; float:right" class="icon_button"></div>
                                  <div style="background-image:url(<?php echo base_url(); ?>assets/mcp/images/add.png);float:right;" title="<?php echo _('Add Partner'); ?>" class="icon_button" onClick="window.location.href='<?php echo base_url(''); ?>mcp/partners/add_partner';" id="btn_add"> </div></td>
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
									<?php if(!empty($partners)) { foreach($partners as $p) {
										$monthly_income = 0;
										if(isset($p->partner_total_amt)){
											$partner_total_amts = explode(',',$p->partner_total_amt);
											//$monthly_income = array_sum($partner_total_amts)/3;
											// $monthly_income = array_sum($partner_total_amts);
										}
										?>
									  <tr>
									    <td class="blackMediumNormal" height="30px"><?php echo $p->id; ?></td>
										<td class="blackMediumNormal"><?php echo $p->p_first_name; ?></td>
										<td class="blackMediumNormal"><?php echo $p->p_last_name; ?></td>
										<td class="blackMediumNormal"><?php echo $p->p_email; ?></td>
										<td class="blackMediumNormal"><?php echo $p->p_phone; ?></td>
										<td class="blackMediumNormal"><?php echo $p->p_username; ?></td>
										<td class="blackMediumNormal"><?php echo $p->p_password; ?></td>
										<td class="blackMediumNormal"><?php echo $p->p_code; ?></td>
										<!-- <td class="blackMediumNormal"><?php echo round($monthly_income,2).' &euro;';//$p->p_monthly_income; ?></td> -->
										<td align="center" class="blackMediumNormal">
										   <a href="javascript:void(0);" onclick="get_rp_login('<?php echo $p->id; ?>','<?php echo $p->p_username; ?>','<?php echo $p->p_password; ?>');" title="<?php echo _('Login as Reseller'); ?>" id="login_<?php echo $p->id; ?>"><?php echo _('LOGIN'); ?></a>
										   <a href="<?php echo base_url(); ?>mcp/partners/edit_partner/<?php echo $p->id; ?>" title="<?php echo _('Edit Partner Details'); ?>"><?php echo _('Edit'); ?></a>
										   <a href="<?php echo base_url(); ?>mcp/partners/delete_partner/<?php echo $p->id; ?>" title="<?php echo _('Remove Partner'); ?>"><?php echo _('Delete'); ?></a>
										</td>
									  </tr>
									<?php } } else { ?>
									  <tr>
									    <td colspan="10" align="center" style="color:red; font-weight:bold;padding:10px;">
										  <?php echo _('Sorry ! No Partner added yet.'); ?>
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
