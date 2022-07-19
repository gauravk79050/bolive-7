<script type="text/javascript">
	function confirm_delete(id){
		if(confirm("<?php echo _('Do you really want to delete it');?>")){
		window.location.href ="<?php echo base_url().'mcp/notifications/delete/';?>"+id;	
		}
	}
</script>
<div style="width:100%">
  <!-- start of main body -->
  <table width="100%" cellspacing="0" cellpadding="0" border="0">
    <tbody>
      <tr>
        <td valign="top" align="center">
        <table width="98%" cellspacing="0" cellpadding="0" border="0">
            <tbody>
              <tr>
                <td valign="top" align="center" style="border:#003366 1px solid; padding:15px 0px 0px 0px"><table width="98%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                      <tr>
                        <td align="center" style="padding-bottom:5px">
                        <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(''); ?>assets/mcp/images/bg.jpg) left top repeat-x;" class="page_caption">
                            <tbody>
                              <tr>
                                <td width="50%" align="left"><h3><?php echo _('All Notifications'); ?></h3></td>
                                <td width="50%" align="right"><div onclick="history.back();" title="Back" style="background-image:url(<?php echo base_url(''); ?>assets/mcp/images/undo.jpg); cursor:pointer; float:right" class="icon_button"></div>
                                  <div style="background-image:url(<?php echo base_url(''); ?>assets/mcp/images/add.png);float:right;" title="<?php echo _('Add New Notification'); ?>" class="icon_button" onClick="window.location.href='<?php echo base_url(); ?>mcp/notifications/notification_addedit'" id="btn_add"></div></td>
                              </tr>
                            </tbody>
                          </table></td>
                      </tr>
                      <tr>
                        <td align="center">
                        <table width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tbody>
                              <tr>
                                <td height="22" align="right"><div style="float:right; width:80%"> </div></td>
                              </tr>
                              <tr>
                                <td bgcolor="#003366">
                                <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/pink_table_bg.jpg) left repeat; text-align:left;">
                                    <tbody>
                                      <tr>
                                        <td width="30%" class="whiteSmallBold"><?php echo _('Subject'); ?></td>
                                        <td width="15%" class="whiteSmallBold"><?php echo _('For Account Type') ;?></td>
                                        <td width="25%" class="whiteSmallBold"><?php echo _('For Company Type') ;?></td>
                                        <td width="10%" class="whiteSmallBold"><?php echo _('Date Created'); ?></td>
                                        <td width="10%" class="whiteSmallBold"><?php echo _('Up To'); ?></td>
                                        <td width="10%" align="right" style="padding-right:100px" class="whiteSmallBold"><?php echo _('Options'); ?></td>
                                      </tr>
                                      <tr>
                                        <td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid" colspan="6">
                                        	<table width="100%" cellspacing="0" cellpadding="0" border="0">
	                                            <tbody>
	                                              <?php if(!empty($content)):?>
	                                              <?php foreach($content as $key=>$row):?>
	                                              <tr>
	                                                <td width="30%" class="blackMediumNormal"><?php echo $row['subject'] ?></td>
	                                                <td width="15%" class="blackMediumNormal"><?php echo $ac_type[$key]; ?> </td>
                                                  <td width="25%" class="blackMediumNormal"><?php echo $comp_type_id[$key]; ?> </td>
	                                                <td width="10%" class="blackMediumNormal"><?php echo substr($row['created_date'], 0,10) ?></td>
	                                                <td width="10%" class="blackMediumNormal"><?php echo $row['upto_date']; ?></td>
	                                                <td width="10%" class="blackMediumNormal" style="margin-left:15px">
		                                                <?php echo anchor(base_url('')."mcp/notifications/notification_addedit/".$row['id'], img(array('src'=>base_url('')."assets/mcp/images/edit.jpg",)))?>
		                                                <a href="javascript:;" onclick="confirm_delete(<?php echo $row['id'];?>)"><img style="width: 16px;" alt="" src="<?php echo base_url()."assets/cp/images/delete.gif"; ?>"></a>
	                                                </td>
	                                              </tr>
	                                              <?php endforeach;?>
	                                              <?php endif;?>
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
