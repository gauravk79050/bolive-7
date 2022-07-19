<style>
.blackMediumNormal a{
    color: #000000;
    font-family: Verdana,Arial,Helvetica,sans-serif;
    font-size: 12px;
    font-weight: normal;
}
</style>
<link href="<?php echo base_url(); ?>assets/cp/new_js/pagination/pagination.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>assets/cp/new_js/pagination/jquery.pagination.js" type="text/javascript"></script>
<link href="<?php echo base_url(); ?>assets/mcp/thickbox/css/thickbox.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>assets/mcp/thickbox/javascript/thickbox.js" type="text/javascript"></script>

<script type="text/javascript">
jQuery.noConflict();

function thickbox_open(id)
{
   tb_show('Change Email','TB_inline?height=35&width=300&inlineId=change_email_'+id,null);
}

function select_all(id1, id2, start_index,end_index){
	if(document.getElementById(id1).checked == true){
		for(i=parseInt(start_index); i<parseInt(end_index); i++){
			id = id2+i;
			document.getElementById(id).checked = true;
		}
	}else{
		for(i=parseInt(start_index); i<end_index; i++){
			id = id2+i;			
			document.getElementById(id).checked = false;
		}
	}
}

function check_selection(frm, id1,start_index,end_index){
	var x=true;
	for(var i=parseInt(start_index);i<parseInt(end_index);i++){
		var id = id1 + i;
		//alert(id+'   '+document.getElementById(id).value);
		if(document.getElementById(id).checked){
			x=false;
			break;
		}
	}
	if(x){
		alert("<?php echo _('Please select a company');?>");
		return false;
	}else{
		return true;
	}	
		
}

function delete_this(company_id,counter,page_index){
	
	if(confirm('<?php echo _("Are you really want to delete this company ?");?>')){
		jQuery.post(
				base_url+'mcp/email_verification/delete',
				{
					'company_id' : company_id,
					'email' : jQuery("#mail_"+company_id).text()
				},
				function(response){
					alert(response.message);
					if(!response.error){
						jQuery("tr#list_"+counter).remove();
						members.splice(counter, 1);
						pageselectCallback(page_index);				
					}
				},
				'json'
			);
	}
}


function keep(company_id,counter,page_index)
{
	//if(confirm('<?php // echo _("Are you really want to keep this company ?");?>'))	
	jQuery.post(
			base_url+'mcp/email_verification/keep',
			{
				'email' : jQuery("#mail_"+company_id).text()
			},
			function(response){
				//alert(response.message);
				if(!response.error){
					jQuery("tr#list_"+counter).remove();
					members.splice(counter, 1);
					pageselectCallback(page_index);				
				}
			},
			'json'
		);	
}

var members = new Array();
<?php $counter = 0; if(isset($content) && !empty($content)) { foreach($content as $cont){ 
echo "members[".$counter."] = ['".$cont->id."','".addslashes($cont->company_name)."','".addslashes($cont->first_name.' '.$cont->last_name)."','".addslashes($cont->email)."','".addslashes($cont->website)."','".addslashes($cont->ac_type_id)."','".addslashes($cont->username)."','".addslashes($cont->password)."'];\n";?>
<?php $counter++; }} ?>
var currentTime = new Date();
var month = currentTime.getMonth() + 1;
var day = currentTime.getDate();
var year = currentTime.getFullYear();
var current_date=month + "/" + day + "/" + year;

function pageselectCallback(page_index, jq){

	//var cal = Calendar.setup({onSelect: function(cal) { cal.hide() }});cal.manageFields("trial_date_picker", "trial_details", "%Y-%m-%d");
	var items_per_page = 10;
	var max_elem = Math.min((page_index+1) * items_per_page, members.length);
	var newcontent = '';
	
	var start_index = page_index*items_per_page;
	var end_index = max_elem;
	for(var i=page_index*items_per_page;i<max_elem;i++)
	{
		//alert(members[i][6]);  
		newcontent += '<tr id=\"list_'+i+'\">';
		newcontent += '<td width="3%" class="blackMediumNormal"><input type="checkbox" id="chk'+i+'" name="bulk_email[]" value="'+members[i][0]+'"></td>';
		newcontent += '<td width="3%" class="blackMediumNormal"><a target="_blank" href="<?php echo base_url(); ?>mcp/companies/update/'+members[i][0]+'">'+members[i][0]+'</a></td>';
		newcontent += '<td width="10%" class="blackMediumNormal">'+members[i][1]+'</td>';
		newcontent += '<td width="15%" class="blackMediumNormal">'+members[i][2]+'</td>';
		newcontent += '<td width="20%" class="blackMediumNormal">';
		newcontent += '		<a href=\"javascript: void(0);\" onclick="thickbox_open('+members[i][0]+');" id=\"mail_'+members[i][0]+'\" class=\"thickbox\" >'+members[i][3]+'</a>';
		newcontent += '		<div id=\"change_email_'+members[i][0]+'\" style="display: none;" >';
		newcontent += '			<input type=\"text\" id=\"email_value_'+members[i][0]+'\" name=\"email_value_'+members[i][0]+'\" value=\"'+members[i][3]+'\"  />';
		newcontent += '     	<input type="button" class="btnWhiteBack change_email" data=\"'+members[i][0]+'\" value="<?php echo _("Change");?>" >';
		newcontent += '		</div>';
		newcontent += '</td>';
		newcontent += '<td width="15%" class="blackMediumNormal"><a target=\"_blank\" href=\"'+( (members[i][4].replace(/\\/g, '').indexOf('http://') != -1 || members[i][4].replace(/\\/g, '').indexOf('https://') != -1)?members[i][4].replace(/\\/g, ''):'http://'+members[i][4].replace(/\\/g, '') )+'\">'+members[i][4].replace(/\\/g, '')+'</a></td>';
		newcontent += '<td width="5%" class="blackMediumNormal">';
		if(members[i][5] == 1)
			newcontent += '<?php echo _("Free");?>';
		if(members[i][5] == 2)
			newcontent += '<?php echo _("Basic");?>';
		if(members[i][5] == 3)
			newcontent += '<?php echo _("Pro");?>';
		newcontent += '</td>';
		newcontent += '<td width="29%" class="blackMediumNormal">';
		newcontent += '<a onclick="delete_this('+members[i][0]+','+i+','+page_index+');" href="javascript:void(0);"><?php echo _("DELETE");?></a>';
		newcontent += '--<a onclick="keep('+members[i][0]+','+i+','+page_index+');" href="javascript:void(0);"><?php echo _("KEEP");?></a>--';
		newcontent += '<a id="login_'+members[i][0]+'" onclick="get_login('+members[i][0]+',\''+members[i][6]+'\',\''+members[i][7]+'\',\'settings\');" href="javascript:void(0);"><?php echo _("LOGIN");?></a>';
		newcontent += '</td>';
		newcontent += '</tr>';
	}
	
	jQuery("#check_all").attr("onclick","select_all('check_all','chk',"+start_index+","+(end_index)+");");
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


//jQuery(document).on("click","change_email",function(){
jQuery('.change_email').live('click',function(){
	var new_email = jQuery(this).parents("div").find("input[type=text]").val();
	var company_id = jQuery(this).attr("data");
	jQuery.post(
			base_url+'mcp/email_verification/change_email',
			{
				'company_id' : company_id,
				'new_email' : new_email
			},
			function(response){
				alert(response.message);
				if(!response.error){
					jQuery("#mail_"+company_id).text(new_email);
				}
				
				self.parent.tb_remove();
			},
			'json'
		);
});

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
<style>
.blackMediumNormal{
	padding: 10px 5px !important;
}

</style>
<?php if($this->session->flashdata('upload_error')){?>
<div style="text-align: center;margin:auto;color:red"><?php echo _($this->session->flashdata('upload_error'));?></div>
<?php }?>
<div style="width:100%">
  
  	<!-- start of main body -->
	<div valign="top" align="center" style="border: 1px solid #003366;margin: 1% auto 0; padding: 10px;width: 95%;">
    	<div>
        	<div width="100%" style="height:26px;background:url(<?php echo base_url(); ?>assets/mcp/images/bg.jpg) left top repeat-x; text-align:left;" class="page_caption">
				<h3><?php echo _('Email Verification'); ?></h3>
            </div>
                          			
            <div>
            	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="blackMediumNormal">
                	<tbody>
                    	<tr>	
                        	<td align="center">
                            	<form id="email_verification_form_admin" action="" method="post" enctype="multipart/form-data">
                          			<table cellpadding="8">
                            			<tbody>
                            				<tr>
                               					<td><strong style="color:#002EBC;"><?php echo _("ADMIN upload csv file with invalid mailaddress");?> : </strong></td>
                               					<td><input type="file" id="admin_csv" name="admin_csv" /></td>
                               					<td><input type="submit" id="admin_submit" name="admin_submit" value="<?php echo _("Submit");?>" /></td>
											</tr>
                          				</tbody>
                          			</table>
                    			</form>
                         	</td>
                     	</tr>
                        <tr>
                        	<td>&nbsp;</td>
                        </tr>
                        <tr>	
                        	<td align="center">
                            	<form id="email_verification_form_client" action="" method="post" enctype="multipart/form-data">
                          			<table cellpadding="8">
                            			<tbody>
                            				<tr>
                               					<td><strong style="color:#002EBC;"><?php echo _("CLIENT upload csv file with invalid mailaddress");?> : </strong></td>
                               					<td><input type="file" id="admin_csv" name="admin_csv" /></td>
                               					<td><input type="submit" id="client_submit" name="client_submit" value="<?php echo _("Submit");?>" /></td>
											</tr>
                          				</tbody>
                          			</table>
                    			</form>
                        	</td>
                        </tr>
                        <tr>
                        	<td>&nbsp;</td>
                        </tr>
                        <?php if(isset($content) && !empty($content)) { ?>
                        <tr>
                        	<td>
                            	<form action="" method="POST">
	                            	<table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/pink_table_bg.jpg) left repeat;border:1px solid #003366;">
	                                    <thead> 
	                                    	<tr style="background:#003366;">
	                                      		<td width="3%" class="whiteSmallBold"><input type="checkbox" id="check_all" name="check_all" onclick="select_all('check_all','chk',0,10);" values="all"></td>
		                                        <td width="3%" class="whiteSmallBold"><?php echo _('ID');?></td>
		                                        <td width="10%" class="whiteSmallBold"><?php echo _('Company Name');?></td>
		                                        <td width="15%" class="whiteSmallBold"><?php echo _('Name');?></td>
		                                        <td width="20%" class="whiteSmallBold"><?php echo _('Email');?></td>
		                                        <td width="24%" class="whiteSmallBold"><?php echo _('Host');?></td>
		                                        <td width="5%" class="whiteSmallBold"><?php echo _('Type');?></td>
		                                        <td width="20%" class="whiteSmallBold"><?php echo _('Action');?></td>
		                                    </tr>
										</thead>
										<tbody id="company_list">  
		                               	</tbody>
										<tfoot>
											<tr>
												<td colspan="5" align="left">
													<input type="submit" name="delete_selected" id="delete_selected" class="btnWhiteBack" value="<?php echo _("Delete Selected Companies");?>" onclick="return confirm('<?php echo _("Aro you sure you want to delete selected company?")?>');">
												
													<input type="submit" name="keep_selected" id="keep_selected" class="btnWhiteBack" value="<?php echo _("Keep Selected Companies");?>" );">
												</td>
												<td colspan="6" align="right">
													 <div id="Pagination" class="Pagination"></div>
												</td>
											</tr>
										</tfoot>
									</table>
                                </form>
                    		</td>
						</tr>
						<?php } ?>
                  	</tbody>
				</table>
			</div>
        </div>
    </div>
	<!-- end of main body -->
</div>
