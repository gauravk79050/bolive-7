<div style="width:100%">
  <!-- start of main body -->
  <table width="100%" cellspacing="0" cellpadding="0" border="0">
    <tbody>
      <tr>
        <td valign="top" align="center">
          <table width="98%" cellspacing="0" cellpadding="0" border="0">
            <tbody>
              <tr>
                <td valign="top" align="center" style="border:#003366 1px solid; padding:15px 0px 0px 0px">
                  <table width="98%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                      <tr>
                        <td align="center">
                          <table width="100%" cellspacing="0" cellpadding="0" border="0" class="blackMediumNormal">
                            <tbody>
                              <tr>
                                <td height="22" align="right">
                                  <div style="float:right; width:80%; padding:5px;">
                                    <?php echo $this->pagination->create_links(); ?>
                                    <div id="Pagination" class="Pagination"></div>
                                  </div>
                                </td>
                              </tr>
                              <tr>
                                <td>
                                  <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/pink_table_bg.jpg) left repeat;border:1px solid #003366;">
                                    <thead>
                                      <tr style="background:#003366;">
                                        <td class="whiteSmallBold"><?php echo _('ID');?></td>
                                        <td class="whiteSmallBold"><?php echo _('Company Name');?></td>
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
                									<?php if( !empty( $assignee ) ) { 
                                          foreach( $assignee as $key => $value ) {
                                            $username = $value[ 'username' ];
                                            $password = $value[ 'password' ];
                                            $id       = $value[ 'id' ]; ?>
                        									  <tr>
                        									    <td class="blackMediumNormal" height="30px"><?php echo $id; ?></td>
                          										<td class="blackMediumNormal"><?php echo $value[ 'company_name' ]; ?></td>
                          										<td class="blackMediumNormal"><?php echo $value[ 'first_name' ]; ?></td>
                          										<td class="blackMediumNormal"><?php echo $value[ 'last_name' ]; ?></td>
                          										<td class="blackMediumNormal"><?php echo $value[ 'email' ]; ?></td>
                          										<td class="blackMediumNormal"><?php echo $value[ 'phone' ]; ?></td>
                                              <td class="blackMediumNormal"><?php echo $username; ?></td>
                          										<td class="blackMediumNormal"><?php echo $password; ?></td>
                          										<td align="center" class="blackMediumNormal">
                          										   <a href="javascript:void(0);" onclick="get_login_fdd('<?php echo $id; ?>');" title="<?php echo _('Login as Assignee'); ?>" id="fdd2_login_<?php echo $id; ?>"><?php echo _('LOGIN 20'); ?></a>
                          										</td>
                        									  </tr>
									                         <?php 
                                         } 
                                        } else { ?>
                        									  <tr>
                        									    <td colspan="10" align="center" style="color:red; font-weight:bold;padding:10px;">
                        										    <?php echo _('Sorry ! No Assignee added yet.'); ?>
                        										  </td>
                        									  </tr>
                        									<?php 
                                          } 
                                        ?>
                                    </tbody>
                                  </table>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>
          					  <tr>
          					    <td>&nbsp;</td>
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
</div>
