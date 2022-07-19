<style type="text/css">
.textbox{
	width:200px;
}
#TB_window{
	/*top:2% !important;*/
}
.tipsy { font-size: 15px !important;}
.tipsy-arrow { background-position: bottom left !important;}
</style>
<link rel="stylesheet" href="<?php echo base_url();?>assets/cp/js/thickbox/css/thickbox.css" type="text/css"/>
<script type="text/javascript" src="<?php echo base_url();?>assets/cp/js/thickbox/javascript/thickbox.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery.tipsy.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tipsy.css" type="text/css"/>
<script type="text/javascript">
jQuery(document).ready(function(){

	jQuery(".suggestion_approve").live('click' , function(){
		var id = jQuery(this).attr('rel');
        jQuery.post("<?php echo base_url();?>rp/reseller/approve_suggestion",
				{   
				   'id':id
				},
				function(html){
					
					if(html.RESULT=='success')
					{   
					   alert ("<?php echo _('suggestion updated successfully !'); ?>");
					   return true;
					}
					else
					{
					   alert ("<?php echo _('Sorry'); ?>");
					   return false;
					}
			},'json');
		
	});
	jQuery(".suggestion_disapprove").live('click' , function(){
		var id = jQuery(this).attr('rel');
		alert("This will delete the suggetion permanently want to continue press ok");
        jQuery.post("<?php echo base_url();?>rp/reseller/disapprove_suggestion",
				{   
				   'id':id

				},
				function(html){
					
						if(html.RESULT=='success')
						{   
						   alert ("<?php echo _('Suggestion deleted successfully !'); ?>");
						   return true;
						}
						else
						{
						   alert ("<?php echo _('Sorry'); ?>");
						   return false;
						}
				},'json');
		
	});
});

function disapprove(id){
	$.ajax({
		url: '<?php echo base_url();?>rp/reseller/disapprove_suggestion',
		type: 'post',
		async:false,
		data: { 'id':id},
		dataType: 'json',
		success: function(response){
			alert(response.RESULT);
		}
	});
	window.parent.tb_remove();

}

function show_company_data(id){

	tb_show('Details','<?php echo base_url();?>rp/reseller/suggested_corrections_detail/'+id+'?height=500&width=700','');	

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
                              <td width="50%" align="left"><h3><?php echo _('Suggested Corrections'); echo "("; if(isset($company_suggested_corrections) && !empty($company_suggested_corrections ) ){echo count($company_suggested_corrections);} else {echo 0;} echo ")";?></h3></td>
                              <td width="50%" align="right">
                              </td>
                           </tr>
                        </tbody>
                        </table>
                        
                     </td>
                     
                   </tr>
                   <tr>
                        <td align="center"><table width="100%" cellspacing="0" cellpadding="0" border="0" class="blackMediumNormal">
                            <tbody>
                              <tr>
                                <td height="22" align="right"><div style="float:right; width:80%; padding:5px;">
                                    
									<div id="Pagination" class="Pagination"></div>
                                  </div></td>
                              </tr>
                              <tr>
                                <td><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/pink_table_bg.jpg) left repeat;border:1px solid #003366;">
                                    <thead>
                                      <tr style="background:#003366;">
                                        <td class="whiteSmallBold"><?php echo _('ID');?></td>
										<td class="whiteSmallBold"><?php echo _('Company ID');?></td>
                                        <td class="whiteSmallBold"><?php echo _('Company Name');?></td>
                                        <!-- <td class="whiteSmallBold">TEL</td> -->
                                        <td class="whiteSmallBold"><?php echo _('Action To View Page');?></td>
                                      </tr>
                                    </thead>
                                    <tbody id="partner_list">
                                    <?php if(!empty($company_suggested_corrections)) { foreach($company_suggested_corrections as $c) { ?>
                                    <tr>
									    <td class="blackMediumNormal" height="30px"><?php echo $c['id']; ?></td>
									    <td class="blackMediumNormal" height="30px"><?php echo $c['company_id']; ?></td>
									    <td class="blackMediumNormal" height="30px"><?php echo $c['company_name']; ?></td>
										<td class="blackMediumNormal"><a onclick="show_company_data(<?php echo $c['id'];?>)" href="javascript: void(0);"><?php echo _("View"); ?></a></td>
										
									  </tr>
									  <?php } } else { ?>
									  <tr>
									    <td colspan="10" align="center" style="color:red; font-weight:bold;padding:10px;">
										  <?php echo _('Sorry ! No suggestions Yet..'); ?>
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
        </table>
        <td ></td>
        </td>
    </tr>
  </tbody>
  
</table>

</td>
</tr>
</tbody>
</table>
</div>

