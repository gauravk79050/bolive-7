<link rel="stylesheet" href="<?php echo base_url().'assets/mcp/thickbox/css/thickbox.css'?>" type="text/css"/>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/thickbox/javascript/thickbox.js"></script>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery(".show_thickbox").click(function(){
		var divId = jQuery(this).attr('rel');
		tb_show("<?php echo _("Email Message")?>","#TB_inline?height=500&amp;width=700&amp;inlineId=message_"+divId);
	});	
});

</script>

<style>
.stat_td{
	background-color: #F0F0F0;
    border-color: #003366;
    border-style: solid;
    border-width: 1px;
    font-family: Verdana,Arial,Helvetica,sans-serif;
    font-size: 12px;
    margin: 10px;
    /*width: 440px;*/
    padding: 5px;
}

.stat_td p{
	color: #003366;
	margin-bottom: 5px;
}

.stat_td .upper{
	margin-bottom: 15px;
	font-weight: bold;
}

.stat_td a {
	color: #003366;
	font-weight: bold;
}

.stat_td .link{
	margin: 10px 0 5px;
	text-align: right;
}

.stat_td .inline {
	display: inline-block;
}
</style>
<div style="width:100%">

<!-- start of main body -->

    <table width="100%" cellspacing="0" cellpadding="0" border="0">
    <tr>
       <td valign="top" align="center">
	      <table width="98%" cellspacing="0" cellpadding="0" border="2">            
          <tr>
              <td valign="top" align="center" style="border:#003366 1px solid; padding:15px 0px 0px 0px">
	 	         <table width="98%" cellspacing="12" cellpadding="0" border="0">
	                 <tr>
	                     <td align="center" style="padding-bottom:10px" colspan="4">
						    <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/bg.jpg) left top repeat-x;" class="page_caption">
	                        <tr>
	                            <td height="30" align="left"><h3><?php echo _('Stats').' >> '._("Latest Mail Sent");?></h3></td>
		                        <td height="30" align="right"><div class="icon_button" style="background-image:url(<?php echo base_url();?>assets/mcp/images/undo.jpg); cursor:pointer; float:right" title="Back" onclick="history.back();" "=""></div></td>
	                        </tr>
	                        </table>
						 </td>
	                 </tr>
	
	                 <tr>
						 <td class="stat_td" width="24%" valign="top">
						    
						    <?php if(!empty($latest_mails_sent_bo)){?>
						    <div class="upper"><?php echo _("Latest mails sent");?></div>
						    <table width="100%" cellspacing="10" cellpadding="5">
						     	<?php foreach ($latest_mails_sent_bo as $key => $values){?>
						    	<tr>
						    		<td width="3%"><span><?php echo $key+1; ?></span></td>
						    		<td width="2%"><span><?php echo _("To"); ?></span></td>
						    		<td width="50%" valign="top">
						    			<span> 
						    				<?php if($values->to_type == 'company'){ ?>
						    					<a href="<?php echo base_url();?>mcp/companies/update/<?php echo $values->company_id;?>"><?php echo $values->company_name;?></a>
						    				<?php }elseif ($values->to_type == 'client'){ ?>
						    					<a href="javascript:void(0);"><?php echo $values->firstname_c.' '.$values->lastname_c;?></a>
						    				<?php } ?>
						    			</span>
						    		</td> 
						    		<td width="20%" valign="top"><span>(<?php echo date('M d', strtotime($values->datetime)).' '._("at")." ".date("H:i", strtotime($values->datetime))." "._("hr");?>)</span></td>
						    		<td width="25%" valign="top">
						    			-<a href="javascript:void(0);" rel="<?php echo $values->id;?>" class="show_thickbox"><?php echo _("see mail")?></a>
						    			<div id="message_<?php echo $values->id;?>" style="display:none;"><?php echo $values->message;?></div>
						    		</td>
						    	</tr>
						    	<?php }?>
						     </table>
						    <?php }else{?>
						    <p class="empty"><?php echo _("No latest mail sent yet!");?></p>
						    <?php }?>
						 </td>
						
                	</tr>
				</table>
			 </td>
		  </tr>
		  </table>
	  </td>
  </tr>
  </table>
</div>              
<br />				