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
<?php echo "members[".$counter."] = ['".$cont['company_id']."','".urlencode($cont['company_name'])."','".$cont['subscribers']."','".$cont['mail_send_last_month']."','".$cont['mail_type']."','".$cont['mail_sent_for_current_type']."','".$cont['credits']."','".$cont['username']."','".$cont['password']."'];\n";?>
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

function change_credits(companyId, creditsLeft){
	var credits = prompt("<?php echo _("Please enter number of credits to be left");?>",creditsLeft);

	if (credits != null && credits != creditsLeft)
	{
		jQuery.post(
				base_url+'mcp/mail_manager/change_credits',
				{'company_id': companyId,'credits': credits},
				function(response){
					alert(response);
					var newHtml = '<a onClick=\"change_credits('+companyId+','+credits+');\" href=\"javascript:void(0);\" >'+credits+'</a>';
					jQuery("#credits_left_"+companyId).html(newHtml);
				}
			);
	}
}

function set_mail_type(companyId, mail_type){

	if(mail_type == 'credits'){
		var credits = prompt("<?php echo _("Please enter number of credits");?>");

		if (credits != null && credits != 0)
		{
			jQuery.post(
					base_url+'mcp/mail_manager/change_mail_type',
					{'company_id': companyId, 'mail_type': mail_type, 'credits': credits},
					function(response){
						alert(response);
						var newHtml = '<a onClick=\"change_credits('+companyId+','+credits+');\" href=\"javascript:void(0);\" >'+credits+'</a>';
						jQuery("#credits_left_"+companyId).html(newHtml);
						//jQuery("#credits_left_"+companyId).text(credits);
					}
				);
		}
	}else{
		jQuery.post(
				base_url+'mcp/mail_manager/change_mail_type',
				{'company_id': companyId, 'mail_type': mail_type},
				function(response){
					alert(response);
					jQuery("#credits_left_"+companyId).html('---');
				}
			);
	}
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
		newcontent += '<td align="center" width="20%" class="blackMediumNormal">'+urldecode(members[i][1])+'</td>';
		newcontent += '<td align="center" width="15%" class="blackMediumNormal">'+members[i][2]+'</td>';
		newcontent += '<td align="center" width="15%" class="blackMediumNormal">'+members[i][3]+'</td>';
		newcontent += '<td align="center" width="15%" class="blackMediumNormal">';
		newcontent += '	<select onChange=\"set_mail_type('+members[i][0]+',this.value)\" >';
		newcontent += '		<option value=\"not_active\" '+( (members[i][4] == 'not_active')?'selected="selected"':'' )+'>Not Activated</option>';
		newcontent += '		<option value=\"free\" '+( (members[i][4] == 'free')?'selected="selected"':'' )+'>Free</option>';
		newcontent += '		<option value=\"monthly\" '+( (members[i][4] == 'monthly')?'selected="selected"':'' )+'>Monthly</option>';
		newcontent += '		<option value=\"credits\" '+( (members[i][4] == 'credits')?'selected="selected"':'' )+'>Credits</option>';
		newcontent += '	</select>'
		newcontent += '</td>';
		if(members[i][4] == 'credits'){
			newcontent += '<td align="center" width="15%" class="blackMediumNormal"><span id=\"credits_left_'+members[i][0]+'\"><a onClick=\"change_credits('+members[i][0]+','+(members[i][6]-members[i][5])+');\" href=\"javascript:void(0);\" >'+(members[i][6]-members[i][5])+'</a></span></td>';
		}else{
			newcontent += '<td align="center" width="15%" class="blackMediumNormal"><span id=\"credits_left_'+members[i][0]+'\">---</span></td>';
		}
		newcontent += '<td align="center" width="20%" class="blackMediumNormal"><a href=\"javascript:void(0);\" onclick=\"get_login(\''+members[i][0]+'\',\''+members[i][7]+'\',\''+members[i][8]+'\',\'mail_manager\');\" id=\"login_'+members[i][0]+'\"><?php echo _("LOGIN MAIL MANAGER");?></a></td>';
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

function chang_value(value){
	if(value == 'subs_type'){
		jQuery("#search_keyword").val('--');
		jQuery("#search_keyword").hide();
		jQuery("#mail_type").show();
		
	}else{
		jQuery("#search_keyword").val('');
		jQuery("#search_keyword").show();
		jQuery("#mail_type").hide();
	}
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
<h3 style="margin-left: 15px;"><?php echo _('Mail Manager'); ?></h3>
<br />


<table cellspacing="0" cellpadding="0" border="0" width="98%" style="background:url(<?php echo base_url(); ?>assets/mcp/images/pink_table_bg.jpg) left repeat; margin:0px auto;">
<tbody>
  <tr>
	<td height="20" bgcolor="#003366" class="whiteSmallBold"><?php echo _('Search Company'); ?></td>
  </tr>
  <tr>
	<td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid; padding:5px">
	   <table width="100%" 
	  cellspacing="0" cellpadding="0" border="0">
		<form action="<?php echo base_url().'mcp/mail_manager'; ?>" enctype="multipart/form-data" method="post" id="frm_search" name="frm_search">
		
		<tbody>
		  <tr>
			<td width="69" height="22" class="blackMediumNormal"><b><?php echo _('Search By'); ?></b></td>
			<td width="126">
			 <select style="width:135px" class="textbox" id="search_by" name="search_by" onChange="chang_value(this.value);">
				<option value="0">- - <?php echo _('Search By'); ?> - -</option>
				<option value="id"><?php echo _('ID'); ?></option>
				<option value="subs_type"><?php echo _('Subscription type'); ?></option>
			  </select>
			</td>
			<td width="109" class="blackMediumNormal">
			   <b><?php echo _('Search Keyword'); ?></b>
			</td>
			<td width="160">
			  <input type="text" style="width:140px" class="textbox" id="search_keyword" name="search_keyword" />
			  <select name="mail_type" id="mail_type" class="textbox" style="display: none;">
			  	<option value="free"><?php echo _("Free");?></option>
			  	<option value="monthly"><?php echo _("Monthly");?></option>
			  	<option value="credits"><?php echo _("Credits");?></option>
			  	<option value="not_active"><?php echo _("Not activated");?></option>
			  </select> 
			</td>
			<td width="345"><span style="padding:0px 3px 3px 0px">
			  <input type="submit" value="<?php echo _('SEARCH'); ?>" class="btnWhiteBack" id="btn_search" name="btn_search">
			  <input type="button" onClick="this.form.search_by.selectedIndex=0; this.form.search_keyword.value='';" value="<?php echo _('RESET'); ?>" class="btnWhiteBack" id="btn_reset" name=
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
	<td align="center" width="20%" class="whiteSmallBold"><?php echo _('Company Name');?></td>
	<td align="center" width="15%" class="whiteSmallBold"><?php echo _('Subscribers');?></td>
	<td align="center" width="15%" class="whiteSmallBold"><?php echo _('Mail sent last month');?></td>
	<td align="center" width="15%" class="whiteSmallBold"><?php echo _('Method');?></td>
	<td align="center" width="15%" class="whiteSmallBold"><?php echo _('Credits left');?></td>
	<td align="center" width="20%" align="center" class="whiteSmallBold"><?php echo _('Action');?></td>
  </tr>
</thead>
<tbody id="company_list">  
		
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