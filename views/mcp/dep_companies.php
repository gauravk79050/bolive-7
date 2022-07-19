<script type="text/javascript">
function get_rp_login(id,username,password)
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

function get_availability(companyId){

	
	
}
</script>

<style type="text/css" rel="stylesheet" >
.blackMediumNormal {
    padding: 5px 0 5px 5px;
}
</style>
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
                      <?php if($this->session->flashdata("result") && $this->session->flashdata("message")){?>
                      <tr>
                      	<td>
                      		<div class="<?php echo $this->session->flashdata("result");?>">
                      			<?php echo $this->session->flashdata("message");?>
                      		</div>
                      	</td>
                      </tr>
                      <?php }?>
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
                                        <td class="whiteSmallBold"><?php echo _('DEP Name');?></td>
                                        <td class="whiteSmallBold"><?php echo _('DEP Email');?></td>
                                        <td class="whiteSmallBold"><?php echo _('Company Name');?></td>
                                        <td class="whiteSmallBold"><?php echo _('Compamy Email');?></td>
                                        <td class="whiteSmallBold"><?php echo _('Companies website');?></td>
                                        <td class="whiteSmallBold"><?php echo _('IP Address');?></td>
                                        <td align="center" class="whiteSmallBold"><?php echo _('Action');?></td>
                                      </tr>
                                    </thead>
                                    <tbody id="partner_list">
									<?php if(!empty($dep_companies)) { foreach($dep_companies as $p) { ?>
									  <tr>
										<td class="blackMediumNormal"><?php echo $p->dep_first_name." ".$p->dep_last_name; ?></td>
										<td class="blackMediumNormal"><?php echo $p->dep_email; ?></td>
										<td class="blackMediumNormal"><?php echo $p->company_name; ?></td>
										<td class="blackMediumNormal"><?php echo $p->email; ?></td>
										<td class="blackMediumNormal"><?php echo $p->website; ?></td>
										<td class="blackMediumNormal"><?php echo $p->REQ_IP_ADD; ?></td>
										<td class="blackMediumNormal">
										   <!-- <a href="javascript:void(0);" onclick="get_availability('<?php echo $p->id; ?>');" title="<?php echo _('Check for availability'); ?>" ><?php echo _('Check Avaiability'); ?></a> -->
										   <a href="<?php echo base_url(); ?>mcp/dep/company_view/<?php echo $p->id; ?>" title="<?php echo _('View Details'); ?>"><?php echo _('View'); ?></a> |
										   <a href="<?php echo base_url(); ?>mcp/dep/approve/<?php echo $p->id; ?>" onClick="return confirm('<?php echo _("Are you sure? After approving company will be added to OBS and a mail will be send to Company and to Partner for their CPs respectively");?>');" title="<?php echo _('Approve'); ?>"><?php echo _('Approve'); ?></a> |
										   <a href="<?php echo base_url(); ?>mcp/dep/disapprove/<?php echo $p->id; ?>" onClick="return confirm('<?php echo _("Are you sure? After disapproving this info will be deleted.");?>');" title="<?php echo _('Disapprove'); ?>"><?php echo _('Disapprove'); ?></a>
										</td>
									  </tr>
									<?php } } else { ?>
									  <tr>
									    <td colspan="10" align="center" style="color:red; font-weight:bold;padding:10px;">
										  <?php echo _('Sorry ! No Data Entry from Portal yet.'); ?>
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
