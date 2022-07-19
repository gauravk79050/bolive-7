<style type="text/css">
#company_list tr{
}

#company_list td{
	padding-top:10px;
	padding-bottom:10px;
	border-bottom:1px solid #ccc;
}
</style>

<link href="<?php echo base_url(); ?>assets/cp/new_js/pagination/pagination.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>assets/cp/new_js/pagination/jquery.pagination.js" type="text/javascript"></script>

<script type="text/javascript">
var members = new Array();
<?php $counter = 0; if(!empty($company_ftp_settings)) { foreach($company_ftp_settings as $cont){ ?>
<?php if( $cont->access_permission == 1 ) { ?>
<?php echo "members[".$counter."] = ['".$cont->cmp_id."','".urlencode($cont->company_name)."','".urlencode($cont->first_name.' '.$cont->last_name)."','".$cont->email."','".$cont->ftp_hostname."','".$cont->shop_url."'];\n"; ?>
<?php $counter++; ?>
<?php }}} ?>
</script>


<script type="text/javascript">
function urldecode(str) 
{
     if(str)
	 return unescape(str.replace(/\+/g, " "));
	 else
	 return '';
}

function pageselectCallback(page_index, jq){

	var items_per_page = 10;
	var max_elem = Math.min((page_index+1) * items_per_page, members.length);
	var newcontent = '';
	
	var j=0;
	var start_index = page_index*items_per_page;
	var end_index = max_elem;
	for(var i=page_index*items_per_page;i<max_elem;i++)
	{
	
		newcontent += '<tr>';
				
		newcontent += '<td width="5%"  class="blackMediumNormal"><input type="checkbox" name="company_id[]" value="'+members[i][0]+'" /></td>';
        newcontent += '<td width="5%"  class="blackMediumNormal">'+members[i][0]+'</td>';
        newcontent += '<td width="14%" class="blackMediumNormal">'+urldecode(members[i][1])+'</td>';
        newcontent += '<td width="10%" class="blackMediumNormal">'+urldecode(members[i][2])+'</td>';
        newcontent += '<td width="15%" class="blackMediumNormal">'+members[i][3]+'</td>';
        newcontent += '<td width="15%" class="blackMediumNormal">'+members[i][4]+'</td>';
        newcontent += '<td width="5%" class="blackMediumNormal">';
        newcontent += '<a href="'+members[i][5]+'" target="_blank" style="color:#003366;font-size:12px;"><?php echo _('Visit Shop'); ?></a>';
        newcontent += '</td>';		
		
		newcontent += '</tr>';
		
		j++;
	}
	
	jQuery('#company_list').html(newcontent);
	
	return false;
}

function getOptionsFromForm()
{
	var opt = {callback: pageselectCallback};

	opt['items_per_page'] = 10;
	opt['num_display_entries'] = 4;
	opt['num_edge_entries'] = 2;
	opt['prev_text'] = '&laquo; <?php echo _('Prev'); ?>';
	opt['next_text'] = '<?php echo _('Next'); ?> &raquo;';

	return opt;

}
 
jQuery(document).ready(function($){

	var optInit = getOptionsFromForm();

	if(members.length > 0)
	{
		$("#Pagination").pagination(members.length, optInit);
	}
	else
	{
		$('#company_list').html('<tr><td valign="middle" height="40" bgcolor="#FFFFFF" align="center" style="border:#003366 1px solid;" class="redMediumBold" colspan="7"><?php echo _('Sorry, No records found !'); ?></td></tr>');
	}
	
	$('#chk-unchk-all').click(function(){
	   if( $('input[type=checkbox]').attr( 'checked' ) )
	     $('input[type=checkbox]').attr( 'checked', 'checked' );
	   else
	     $('input[type=checkbox]').removeAttr( 'checked' ); 
	});
});
  
</script>

<div style="width:100%">

  <br />
  <h3 style="margin:0 15px;"><?php echo _('Upgrade Client Files'); ?></h3>
 
  <?php if( isset($message['success']) ) { ?>
  <div class="success" style="padding:10px 15px;color:green;font-weight:bold;"><p style="margin:0px;"><?php echo $message['success']; ?></p></div>
  <?php } elseif( isset($message['error']) ) { ?>
  <div class="error" style="padding:10px 15px;color:red;font-weight:bold;"><p style="margin:0px;"><?php echo $message['error']; ?></p></div>
  <?php } ?>
  
  <br />
  <p style="margin:0 15px;"><?php echo _('Select companies from the following list, and click \'Update Client Files\' button to automatically update the obs client files, on their FTP Server.'); ?></p>
  


<form method="post" action="">

<table width="98%" cellspacing="0" cellpadding="10" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/pink_table_bg.jpg) left repeat;border:1px solid #003366;margin:20px auto;">
   <thead> 
      <tr style="background:#003366;">
        <td width="5%" class="whiteSmallBold"><input type="checkbox" name="company_id" value="0" id="chk-unchk-all" /></td>
        <td width="5%" class="whiteSmallBold"><?php echo _('ID');?></td>
        <td width="14%" class="whiteSmallBold"><?php echo _('Company Name');?></td>
        <td width="10%" class="whiteSmallBold"><?php echo _('Name');?></td>
        <td width="15%" class="whiteSmallBold"><?php echo _('Email');?></td>
        <td width="15%" class="whiteSmallBold"><?php echo _('Hostname');?></td>
        <td width="5%" class="whiteSmallBold"><?php echo _('Action');?></td>
      </tr>
   </thead>
   <tbody id="company_list">  	  
   
   </tbody>
   <tfoot>
     <tr>
       <td colspan="5"><input type="submit" name="submit" id="submit" value="Update Client Files" class="btnWhiteBack" /></td>
       <td colspan="2" align="right" id="Pagination"></td>
     </tr>
   </tfoot>


</table>

</form>

</div>