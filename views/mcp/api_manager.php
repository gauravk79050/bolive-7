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
<?php $counter = 0; if(!empty($content)) { foreach($content as $cont){ ?>
<?php   
       $action = '';
	   
       if(@$cont->api_id && @$cont->api_secret && @$cont->domain) {
	     $action .= _('Key Issued');
	   } else {
	     $action .= '<a href="'.site_url('mcp/api/generate_api/'.$cont->cmp_id).'">'._('Generate API').'</a>';
	   }
	   
	   $action .= '<br /><br />';
	   
	   $action .= '<a href="javascript:void(0);" onclick="get_login(\''.$cont->cmp_id.'\',\''.$cont->username.'\',\''.$cont->password.'\');" id="login_'.$cont->cmp_id.'">'._('LOGIN').'</a>';
	   
?>
<?php echo "members[".$counter."] = ['".$cont->cmp_id."','".urlencode($cont->company_name)."','".urlencode($cont->first_name.' '.$cont->last_name)."','".$cont->email."','".((@$cont->api_id)?@$cont->api_id:'-')."','".((@$cont->api_secret)?@$cont->api_secret:'-')."','".((@$cont->domain)?@$cont->domain:'-')."','".(($cont->status==0)?(_('Inactive')):(_('Active')))."','".urlencode($action)."'];\n";?>
<?php $counter++; }} ?>
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
		newcontent += '<td width="5%"  class="blackMediumNormal">'+members[i][0]+'</td>';
		newcontent += '<td width="14%" class="blackMediumNormal">'+urldecode(members[i][1])+'</td>';
		newcontent += '<td width="12%" class="blackMediumNormal">'+urldecode(members[i][2])+'</td>';
		newcontent += '<td width="10%" class="blackMediumNormal">'+members[i][3]+'</td>';
		newcontent += '<td width="10%" class="blackMediumNormal">'+members[i][4]+'</td>';
		newcontent += '<td width="15%" class="blackMediumNormal">'+members[i][5]+'</td>';
		newcontent += '<td width="10%" class="blackMediumNormal">'+members[i][6]+'</td>';
		newcontent += '<td width="9%"  class="blackMediumNormal">'+members[i][7]+'</td>';
		newcontent += '<td align="center" width="15%" class="blackMediumNormal">'+urldecode(members[i][8])+'</td>';
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
		$(".Pagination").pagination(members.length, optInit);
	}
	else
	{
		$('#company_list').html('<tr><td valign="middle" height="40" bgcolor="#FFFFFF" align="center" style="border:#003366 1px solid;" class="redMediumBold" colspan="9"><?php echo _('Sorry, No result found.'); ?></td></tr>');
	}

});
  
</script>

<div style="width:100%">

<br />
<h3><?php echo _('API Manager'); ?></h3>
<br />


<table cellspacing="0" cellpadding="0" border="0" width="98%" style="background:url(<?php echo base_url(); ?>								   assets/mcp/images/pink_table_bg.jpg) left repeat; margin:0px auto;">
<tbody>
  <tr>
	<td height="20" bgcolor="#003366" class="whiteSmallBold"><?php echo _('Search Company'); ?></td>
  </tr>
  <tr>
	<td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid; padding:5px">
	   <table width="100%" 
	  cellspacing="0" cellpadding="0" border="0">
		<form action="<?php echo base_url().'mcp/api'; ?>" enctype="multipart/form-data" method="post" id="frm_search" name="frm_search">
		
		<tbody>
		  <tr>
			<td width="69" height="22" class="blackMediumNormal"><b><?php echo _('Search By'); ?></b></td>
			<td width="126">
			 <select style="width:120px" class="textbox" id="search_by" name="search_by">
				<option value="0">- - <?php echo _('Search By'); ?> - -</option>
				<option value="id"><?php echo _('ID'); ?></option>
				<option value="company_name"><?php echo _('Company Name'); ?></option>
				<option value="email"><?php echo _('Email'); ?></option>
				<option value="username"><?php echo _('Username'); ?></option>
				<option value="city"><?php echo _('City'); ?></option>
			  </select>
			</td>
			<td width="109" class="blackMediumNormal">
			   <b><?php echo _('Search Keyword'); ?></b>
			</td>
			<td width="160">
			  <input type="text" style="width:140px" class="textbox" id="search_keyword" name=
			  "search_keyword">
			</td>
			<td width="345"><span style="padding:0px 3px 3px 0px">
			  <input type="submit" value="<?php echo _('SEARCH'); ?>" class="btnWhiteBack" id="btn_search" name="btn_search">
			  <input type="button" onClick="this.form.search_by.selectedIndex=0; this.form.search_keyword.value='';this.form.submit();" value="<?php echo _('RESET'); ?>" class="btnWhiteBack" id="btn_reset" name=
				  "btn_reset">
			  <input type="hidden" value="do_filter" id="act" name="act">
			  <input type="hidden" value="companies" id="view" name="view">
			  </span></td>
		  </tr>
		  <script type="text/javascript" language="JavaScript">

				var frmvalidator = new Validator("frm_search");

				frmvalidator.EnableMsgsTogether();

				frmvalidator.addValidation("search_by","dontselect=0","<?php echo _('Please select a column on which to search.'); ?>");
				frmvalidator.addValidation("search_keyword","req","<?php echo _('Please enter search keyword.'); ?>");

		  </script>
		</tbody>
		
		</form>
		
	  </table></td>
  </tr>
</tbody>
</table>


<table width="98%" cellspacing="0" cellpadding="10" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/pink_table_bg.jpg) left repeat;border:1px solid #003366;margin:20px auto;">
<thead> 
  <tr style="background:#003366;">
	<td width="5%" class="whiteSmallBold"><?php echo _('ID');?></td>
	<td width="14%" class="whiteSmallBold"><?php echo _('Company Name');?></td>
	<td width="10%" class="whiteSmallBold"><?php echo _('Name');?></td>
	<td width="15%" class="whiteSmallBold"><?php echo _('Email');?></td>
	<td width="10%" class="whiteSmallBold"><?php echo _('API Id');?></td>
	<td width="10%" class="whiteSmallBold"><?php echo _('API Key');?></td>
	<td width="10%" class="whiteSmallBold"><?php echo _('Domain');?></td>
	<td width="9%" class="whiteSmallBold"><?php echo _('Status');?></td>
	<td width="15%" align="center" class="whiteSmallBold"><?php echo _('Action');?></td>
  </tr>
</thead>
<tbody id="company_list">  
  <?php /*if(!empty($content)) { foreach($content as $cont):?>
  <tr>
	<td width="5%" class="blackMediumNormal"><?php echo $cont->cmp_id; ?></td>
	<td width="14%" class="blackMediumNormal"><?php echo $cont->company_name?></td>
	<td width="10%" class="blackMediumNormal"><?php echo $cont->first_name?> <?php echo $cont->last_name?></td>
	<td width="15%" class="blackMediumNormal"><?php echo $cont->email?></td>
	<td width="10%" class="blackMediumNormal"><?php echo (@$cont->api_id)?@$cont->api_id:'-'; ?></td>
	<td width="10%" class="blackMediumNormal"><?php echo (@$cont->api_secret)?@$cont->api_secret:'-'; ?></td>
	<td width="10%" class="blackMediumNormal"><?php echo (@$cont->domain)?@$cont->domain:'-'; ?></td>
	<td width="9%" class="blackMediumNormal"><?php if($cont->status==0){ echo _('Inactive'); }else{ echo _('Active'); }; ?></td>
	<td width="15%" align="center" class="blackMediumNormal">
	   <?php if(@$cont->api_id && @$cont->api_secret && @$cont->domain) { ?>
	     Key Issued
	   <?php } else { ?>
	   <a href="<?php echo site_url('mcp/api/generate_api/'.$cont->cmp_id);?>">
	     Generate API
	   </a>
	   <?php } ?>
	   <br /><br />
	   <a href="javascript:void(0);" onclick="get_login('<?php echo $cont->cmp_id; ?>','<?php echo $cont->username; ?>','<?php echo $cont->password; ?>');" id="login_<?php echo $cont->cmp_id; ?>"><?php echo _('LOGIN'); ?></a>
	</td>
  </tr>
  <?php endforeach; ?>
  
  <?php } else { ?>
  <tr>
	  <td valign="middle" height="40" bgcolor="#FFFFFF" align="center" style="border:#003366 1px solid;" class="redMediumBold" colspan="9">
		 <?php echo _('No approved companies.'); ?>
	  </td>
  </tr>
  <?php }*/ ?>
		
</tbody>
<tfoot>
  <tr>
      <td colspan="9" align="right">
	     <div id="Pagination" class="Pagination"></div>
	  </td>
  </tr>
</tfoot>
</table>


</div>