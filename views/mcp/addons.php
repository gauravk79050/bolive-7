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
                        <td align="center" style="padding-bottom:5px"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(''); ?>assets/mcp/images/bg.jpg) left top repeat-x;" class="page_caption">
                            <tbody>
                              <tr>
                                <td width="50%" align="left"><h3><?php echo _('Addon Manager'); ?></h3></td>
                                <td width="50%" align="right"><div onclick="history.back();" title="Back" style="background-image:url(<?php echo base_url(''); ?>assets/mcp/images/undo.jpg); cursor:pointer; float:right" class="icon_button"></div>
                                	<div style="background-image:url(<?php echo base_url(''); ?>assets/mcp/images/add.png);float:right;" title="<?php echo _('Add New Addon'); ?>" class="icon_button" onClick="window.location.href='<?php echo base_url(''); ?>mcp/addon/add_addon'" id="btn_add"></div>
                               	</td>
                              </tr>
                            </tbody>
                          </table></td>
                      </tr>
                      <tr>
                        <td align="center"><table width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tbody>
                              <tr>
                                <td height="22" align="right"><div style="float:right; width:80%"> <?php /*?><span class="paging_nolink">&lt;&lt;Vorige</span>&nbsp;<span class="paging_selected">1</span>&nbsp;<span class="paging_nolink">Volgende&gt;&gt;</span><?php */?> </div></td>
                              </tr>
                              <tr>
                                <td bgcolor="#003366"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/pink_table_bg.jpg) left repeat; text-align:left;">
                                    <tbody>
                                      <tr>
                                        <td width="7%" class="whiteSmallBold"><?php echo _('ID'); ?></td>
                                        <td width="23%" class="whiteSmallBold"><?php echo _('Addon Name'); ?></td>
                                        <td width="30%" class="whiteSmallBold"><?php echo _('Addon Desciption') ;?></td>
                                        <td width="30%" class="whiteSmallBold" style="text-align: center;"><?php echo _('Addon Price/mnth'); ?></td>
                                       <!--   <td width="30%" class="whiteSmallBold" style="text-align: center;"> --><?php //echo _('Addon Display Order'); ?><!--  </td>-->
                                        <td width="25%" align="right" style="padding-right:40px" class="whiteSmallBold"><?php echo _('Options'); ?></td>
                                      </tr>
                                      <tr>
                                        <td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid" colspan="5"><table width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tbody>
                                              <?php if(!empty($addons)):?>
                                              <?php foreach($addons as $row):?>
                                              <tr>
                                                <td width="7%" class="blackMediumNormal"><?php echo $row->addon_id ?></td>
                                                <td width="23%" class="blackMediumNormal"><?php echo $row->addon_title ?></td>
                                                <td width="30%" class="blackMediumNormal"><?php echo $row->addon_description ?> </td>
                                                <td width="33%" class="blackMediumNormal" style="text-align: center;"><?php echo $row->addon_price ?></td>
                                               <!--   <td width="33%" class="blackMediumNormal" style="text-align: center;"> --><?php //echo $row->addon_display_order ?><!--  </td>-->
                                                <td width="25%" class="blackMediumNormal"><?php echo anchor(base_url('')."mcp/addon/update/".$row->addon_id,img(array('src'=>base_url('')."assets/mcp/images/edit.jpg")))?></td>
                                              </tr>
                                              <?php endforeach;?>
                                              <?php endif;?>
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
          </table></td>
      </tr>
    </tbody>
  </table>
  <!-- end of main body -->
</div>
