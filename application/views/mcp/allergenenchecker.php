<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery(".change_status").change(function(){
		var banner_id = jQuery(this).attr("id").split("_")['1'];
		jQuery.post(
				'<?php echo base_url();?>mcp/allergenenchecker/change_status',
				{
					'banner_id': banner_id,
					'status'   : jQuery(this).val()
				},
				function(response){
					alert(response.message);
				},
				'json'
		);
	});

	jQuery(".delete_banner").click(function(){

		if(confirm("Are you sure want to delete this")){
			var banner_id = jQuery(this).attr("rel");
			jQuery.post(
					'<?php echo base_url();?>mcp/allergenenchecker/delete',
					{
						'banner_id': banner_id
					},
					function(response){
						if(!response.error){
							jQuery("#row_"+banner_id).remove();
						}else{
							alert(response.message);
						}
					},
					'json'
			);
		}
	});
});
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
                        <td align="center"><table width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tbody>
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
                                        <td align="center" bgcolor="#FFFFFF" style="border:#003366 1px solid" colspan="5">
                                        	<h6 style="font-size: 14px;"><?php echo _( "Add Banner" );?> </h6>
										    <form action="" method="post" id="banner_add" name="banner_add" enctype="multipart/form-data" style="margin: 20px">
										    	<label><?php echo _( "Select Type" );?></label>
										    	<select name="type_id" >
													<?php if( isset( $company_type ) && !empty( $company_type ) ) {
														foreach ( $company_type as $key => $value ) {?>
	                                        				<option value="<?php echo $value[ 'id' ];?>" >
	                                        					<?php echo ucfirst( $value[ 'company_type_name' ] );?>
	                                    					</option>
															
														<?php
														}
													}?> 
	                                        	</select>
										    	<input type="file" name="banner" id="banner" />
										    	<input type="submit" name="add_banner" id="add_banner" value="<?php echo _("Add")?>" /><br>
										    </form>
										</td>
									</tr>
											
                                    </tbody>
                                  </table></td>
                              </tr>
                              <tr>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td bgcolor="#003366"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/pink_table_bg.jpg) left repeat; text-align:left;">
                                    <tbody>
                                      <tr>
                                        <td width="50%" align="center" class="whiteSmallBold"><?php echo _('Banner'); ?></td>
                                        <td width="10%" align="center" class="whiteSmallBold"><?php echo _('Type Name'); ?></td>
                                        <td width="10%" align="center" class="whiteSmallBold"><?php echo _('Status'); ?></td>
                                        <td width="10%" align="right" style="padding-right:40px" class="whiteSmallBold"><?php echo _('Options'); ?></td>
                                      </tr>
                                      <tr>
                                        <td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid" colspan="4">
										    <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/pink_table_bg.jpg) left repeat; text-align:left;">
			                                    <tbody>
			                                      <?php if(!empty($content)):?>
			                                      <?php foreach ($content as $values):?>
			                                      <tr id="row_<?php echo $values->id;?>">
			                                        <td width="50%"><img src="<?php echo base_url();?>assets/mcp/images/<?php echo $values->banner?>" style="max-width:50%;"/></td>
			                                        <td width="10%" align="center">
			                                        	<?php echo ucfirst( $values->company_type_name );?>
			                                        </td>
			                                        <td width="10%" align="center">
			                                        	<select id="status_<?php echo $values->id;?>" name="status_<?php echo $values->id;?>" class="change_status" >
			                                        		<option value="1" <?php if($values->status == 1 ){?>selected="selected"<?php }?>><?php echo _("Active");?></option>
			                                        		<option value="0" <?php if($values->status == 0 ){?>selected="selected"<?php }?>><?php echo _("Inactive");?></option>
			                                        	</select>
			                                        </td>
			                                        <td width="10%" align="center">
			                                        	<a href="javascript:;" class="delete_banner" rel="<?php echo $values->id;?>"><img src="<?php echo base_url();?>assets/mcp/images/delete.jpg" alt="<?php echo _("Delete")?>"/></a>
			                                        </td>
			                                      </tr>
			                                      <tr>
			                                        <td colspan="3">&nbsp;</td>
			                                      </tr>
			                                      <?php endforeach;?>
			                                      <?php else:?>
			                                      <tr>
			                                        <td align="center" colspan="3"><?php echo _("No banner found");?></td>
			                                      </tr>
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
