<script type="text/javascript">
	function allergens_delete(id){
		if(confirm("<?php echo _('Do you really want to delete it');?>")){
			window.location.href ="<?php echo base_url().'mcp/allergenswords/delete/';?>"+id;	
		}
	}
	function allergens_edit(id){
		jQuery(document).ready(function($){			
			val = $('#'+id+' td:first').text();
			html = '<input type="text" id="aller'+id+'" value="'+val+'">';
			html +='<input type="button" class="aller_update" value ="Update" data-aller="'+id+'">';
			html +='<input type="button" value="Undo" onclick="allergens_undo('+id+',\''+val+'\')">';
			$('#'+id+' td:first').html(html);
			$('#'+id+' td:last').hide();
		});
	}
	function allergens_undo(id,val){
		jQuery(document).ready(function($){
			$('#'+id+' td:first').html(val);
			$('#'+id+' td:last').show();
		});
	}
	jQuery(document).ready(function($){
		$(document).on('click','.aller_update',function(){
			id = $(this).data('aller');
			val = $('#aller'+id).val();
			window.location.href ="<?php echo base_url().'mcp/allergenswords/update?id=';?>"+id+"<?php echo '&aller='?>"+val;
		});
	});
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
                                <td width="50%" align="left"><h3><?php echo _('All Allergens'); ?></h3></td>
                                <td width="50%" align="right"><div onclick="history.back();" title="Back" style="background-image:url(<?php echo base_url(''); ?>assets/mcp/images/undo.jpg); cursor:pointer; float:right" class="icon_button"></div>
                                  <div style="background-image:url(<?php echo base_url(''); ?>assets/mcp/images/add.png);float:right;" title="<?php echo _('Add New Allergens'); ?>" class="icon_button" onClick="window.location.href='<?php echo base_url(); ?>mcp/allergenswords/allergens_add'" id="aller_add"></div></td>
                              </tr>
                            </tbody>
                          </table></td>
                      </tr>
                    </tbody>
                  </table></td>
              </tr>
              
                        <td align="center">
                        <table width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tbody>
                              <tr>
                                <td height="22" align="right"><div style="float:right; width:80%"> <?php /*?><span class="paging_nolink">&lt;&lt;Vorige</span>&nbsp;<span class="paging_selected">1</span>&nbsp;<span class="paging_nolink">Volgende&gt;&gt;</span><?php */?> </div></td>
                              </tr>
                              <tr>
                                <td bgcolor="#003366">
                                <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/pink_table_bg.jpg) left repeat; text-align:left;">
                                    <tbody>
                                      <tr>
                                        <td width="30%" class="whiteSmallBold"><?php echo _('Allergens'); ?></td>
                                        <td width="20%" class="whiteSmallBold"><?php echo _('Date Created'); ?></td>
                                        <td width="15%" align="right" style="padding-right:100px" class="whiteSmallBold"><?php echo _('Options'); ?></td>
                                      </tr>
                                      <tr>
                                        <td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid" colspan="6">
                                        	<table width="100%" cellspacing="0" cellpadding="0" border="0">
	                                            <tbody>
	                                              <?php if(!empty($content)):?>
	                                              <?php foreach($content as $key=>$row):?>
	                                              <tr id="<?php echo $row['id'];?>">
	                                                <td width="30%" class="blackMediumNormal"><?php echo $row['allergens_word'] ?></td>	                                                
	                                                <td width="20%" class="blackMediumNormal"><?php echo substr($row['date_created'], 0,10) ?></td>
	                                                <td width="15%" class="blackMediumNormal" style="margin-left:15px; text-align:center;">
	                                                	<a href="javascript:;" onclick="allergens_edit(<?php echo $row['id'];?>)"><img alt="" src="<?php echo base_url()."assets/cp/images/edit.jpg"; ?>" style="width:16px"></a>
		                                                <a href="javascript:;" onclick="allergens_delete(<?php echo $row['id'];?>)"><img alt="" src="<?php echo base_url()."assets/cp/images/delete.gif"; ?>" style="width:16px"></a>
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
  </table>
  <!-- end of main body -->
</div>