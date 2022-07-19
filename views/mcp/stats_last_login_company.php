
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
		                            <td height="30" align="left"><h3><?php echo _('Stats').' >> '._("Latest login in CP");?></h3></td>
		                            <td height="30" align="right"><div class="icon_button" style="background-image:url(<?php echo base_url();?>assets/mcp/images/undo.jpg); cursor:pointer; float:right" title="Back" onclick="history.back();" "=""></div></td>
		                        </tr>
	                        </table>
						 </td>
	                 </tr>
	
					<tr>
	                	<td class="stat_td" width="28%" valign="top">
						<?php if(!empty($last_login_companies)){?>
						    <div class="upper"><?php echo _("Latest login in CP");?></div>
						    <table width="100%" cellspacing="5" cellpadding="5">
						    	<?php foreach ($last_login_companies as $key => $values){?>
						    	<tr>
						    		<td width="2%" valign="top"><?php echo $key+1;?></td>
						    		<td width="50%" valign="top">
						    			<span class="inline"><a href="<?php echo base_url();?>mcp/companies/update/<?php echo $values->company_id;?>"><?php echo $values->company_name;?></a></span>
						    		</td>
						    		<td width="48%" valign="top"><span>(<?php echo date('M d', strtotime($values->last_login)).' '._("at")." ".date("H:i", strtotime($values->last_login))." "._("hr");?>)</span></td> 
						    	</tr>
						    	<?php }?>
						    </table>
						    <?php }else{?>
						    <p class="empty"><?php echo _("No record yet!");?></p>
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