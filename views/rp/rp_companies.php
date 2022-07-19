<link rel="stylesheet" href="<?php echo base_url();?>assets/cp/js/thickbox/css/thickbox.css" type="text/css"/>
<script type="text/javascript" src="<?php echo base_url();?>assets/cp/js/thickbox/javascript/thickbox.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/ui-1.10.2/jquery-ui.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/ui/jquery.ui.base.css" type="text/css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/ui/jquery.ui.theme.css" type="text/css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/ui/jquery.ui.autocomplete.css" type="text/css"/>
<style>
.ui-corner-all{
	color:#003366;
	font-size:12px;
	text-transform:capitalize;
}
.ui-autocomplete {
	max-height: 110px;
	overflow-y: auto;
	/* prevent horizontal scrollbar */
	overflow-x: hidden;
}
</style>
<link href="<?php echo base_url(); ?>assets/cp/new_js/pagination/pagination.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>assets/cp/new_js/pagination/jquery.pagination.js" type="text/javascript"></script>
<script type="text/javascript">
function stripslashes(str) {
	  //       discuss at: http://phpjs.org/functions/stripslashes/
	  //      original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	  //      improved by: Ates Goral (http://magnetiq.com)
	  //      improved by: marrtins
	  //      improved by: rezna
	  //         fixed by: Mick@el
	  //      bugfixed by: Onno Marsman
	  //      bugfixed by: Brett Zamir (http://brett-zamir.me)
	  //         input by: Rick Waldron
	  //         input by: Brant Messenger (http://www.brantmessenger.com/)
	  // reimplemented by: Brett Zamir (http://brett-zamir.me)
	  //        example 1: stripslashes('Kevin\'s code');
	  //        returns 1: "Kevin's code"
	  //        example 2: stripslashes('Kevin\\\'s code');
	  //        returns 2: "Kevin\'s code"

	  return (str + '')
	    .replace(/\\(.?)/g, function(s, n1) {
	      switch (n1) {
	        case '\\':
	          return '\\';
	        case '0':
	          return '\u0000';
	        case '':
	          return '';
	        default:
	          return n1;
	      }
	    });
}

function thickbox_open(id)
{
   tb_show('Remarks','TB_inline?height=230&width=300&inlineId=reseller_remarks_'+id,null);
}
</script>
<script type="text/javascript">
var members = new Array();
var dataLength = <?php echo $company_count;?>;

function get_login(id,username,password)
{  
   jQuery('#login_'+id).css('text-decoration','none');
   jQuery('#login_'+id).html('&nbsp;&nbsp;&nbsp;<img src="<?php echo base_url(); ?>assets/mcp/images/ajax-loader.gif">&nbsp;&nbsp;&nbsp;');
   
   jQuery.post('<?php echo base_url(); ?>cp/login/validate',
         {
		    'act':'do_login',
			'submit':'LOGIN',
			'username':username,
			'password':password
			
		 },function(data){
            
			window.open('<?php echo base_url(); ?>cp');
			jQuery('#login_'+id).css('text-decoration','underline');
			jQuery('#login_'+id).html('LOGIN');
   });
}
function get_login_fdd( comp_id ) {
  jQuery('#login_'+comp_id).css('text-decoration','none');
   jQuery('#login_'+comp_id).html('&nbsp;&nbsp;&nbsp;<img src="<?php echo base_url(); ?>assets/mcp/images/ajax-loader.gif">&nbsp;&nbsp;&nbsp;');

    jQuery.post('<?php echo base_url(); ?>mcp/mcplogin/login_fdd2_via_mcp',
      {
      'comp_id' : comp_id,
    },function(data){
      if(data){
        jQuery('#fdd2_login_'+comp_id).html('LOGIN 20');
        window.open('<?php echo $this->config->item( 'new_obs_url' ); ?>'+"login/loggen_via_mcp_oldobs/"+data+"/"+comp_id,"_blank");
      }
  });
}
jQuery(document).ready(function(){

	<?php
	if(!empty($companies)) {

		$suggest_array = array(); 
		foreach($companies as $comp){
			$suggest_array[] = $comp->company_name;
		}
		echo 'var company_suggest = '.json_encode($suggest_array).';';
		?>
		//autocomplete init
		$( "#search_keyword" ).autocomplete({
			source: company_suggest,
			messages: {
		        noResults: '',
		        results: function() {}
		    }
		});

	<?php } ?>
	
	jQuery(".trial_calendar").live('click' , function(){
		var company_id = jQuery(this).attr('rel');
		jQuery(this).hide('slow');
		jQuery("#trial_date_div_"+company_id).toggle('slow');
	});

	jQuery(".trial_update_cancel").live('click' , function(){
		var company_id = jQuery(this).attr('rel');
		//jQuery(this).hide();
		jQuery("#trial_date_div_"+company_id).toggle('slow');
		jQuery("#trial_calendar_"+company_id).toggle('slow');
	});

	jQuery(".trial_update").live('click' , function(){
		var company_id = jQuery(this).attr('rel');
		var date = jQuery("#trial_details_"+company_id).val();
		var co_email=jQuery("#co_email_"+company_id).val();
        jQuery.post("<?php echo site_url('rp/reseller/trial_date_insert');?>",
				{   
				   'company_id':company_id,
				   'date':date,
				   'co_email':co_email
				},
					
				function(html){
					
						if(html.RESULT=='success')
						{   
						   alert ("<?php echo _('Trial Date updated successfully !'); ?>");
						   return true;
						}
						else
						{
						   alert ("<?php echo _('Sorry ! Could not update the Trial.'); ?>");
						   return false;
						}
				},'json');
		
	});

	jQuery(".upd_remark").live('click',function(){
		company_id = $(this).attr('rel');
		//upd_remark = $('#upd_remark_'+company_id).val();
		upd_remark = $(this).parents('table').find('#upd_remark_'+company_id).val();
		//console.log($('#upd_remark_'+company_id).val());
		//console.log($(this).parents('table').find('#upd_remark_'+company_id).val());
		jQuery.post("<?php echo base_url().'rp/reseller/update_remark';?>",
		{   
		   'company_id':company_id,
		   'remark': upd_remark,
		},
		function(response){
			alert (response.data);
			tb_remove();
		},'json');
	});
});
/* ------------------- To show thick box ---------------------------------------------- */
function show_company_data(company_id){

	tb_show('Details','#TB_inline?height=290&width=400&inlineId=show_company_'+company_id,'');	

}

function pageselectCallback(page_index, jq){
	// Get number of elements per pagionation page from form
	var items_per_page = 10;
	//var max_elem = Math.min((page_index+1) * items_per_page, dataLength);
	var max_elem = Math.min(items_per_page, (dataLength -(items_per_page*(page_index))));
	var newcontent = '';

	var start_index = page_index*items_per_page;
	var end_index = max_elem;
	
	jQuery('#company_list').html('<tr><td width="93%" colspan="12"><img  height="200" width="200" style="margin-left:510px;" src="<?php echo base_url(); ?>assets/mcp/images/loading.gif" /></td></tr>');

	jQuery.ajax({
		url: '<?php echo base_url();?>rp/reseller/ajax_companies',
		type:'POST',
		dataType: 'json',
		data:{
				start: start_index,
				limit: end_index,
				btn_search: jQuery("#hdn_btn_search").val(),
				search_by: jQuery("#hdn_search_by").val(),
				search_keyword: jQuery("#hdn_search_keyword").val(),
				ac_type_id: jQuery("#hdn_ac_type_id").val(),
				order_by: jQuery("#hdn_order_by").val()
			},
		success: function(members){
			if(members.length){
				for(var i=0;(i<max_elem && i<members.length);i++)
				{
					//console.log(members[i]);
					newcontent += '<tr>';
					newcontent += '<td class="blackMediumNormal" height="30px">'+members[i][0]+'</td>';
					newcontent += '<td class="blackMediumNormal"><a onclick="show_company_data('+members[i][0]+')" href="javascript: void(0);">'+members[i][1]+'</a></td>';
					newcontent += '<td class="blackMediumNormal">'+members[i][21]+'</td>';
					
					newcontent += '<td class="blackMediumNormal">'+members[i][28]+'</td>';
					newcontent += '<td class="blackMediumNormal">'+members[i][29]+'</td>';
					newcontent += '<td class="blackMediumNormal">'+members[i][30]+'</td>';
					
					// newcontent += '<td class="blackMediumNormal">'+members[i][22]+'<?php //echo $c->partner_total_amount; ?></td>';
					//newcontent += '<td class="blackMediumNormal">'+(members[i][25] == '1'?(members[i][23])+' &euro;':'0 &euro;')+'</td>';

					//newcontent += '<td class="blackMediumNormal">'+(parseFloat(members[i][23])?(members[i][23])+' &euro;':'0 &euro;')+'</td>';
					
					newcontent += '<td class="blackMediumNormal" colspan="2">'+members[i][23]+'</td>';
					
					
					//newcontent += '<td class="blackMediumNormal">'+(members[i][23]?'PAID':'UNPAID')+'</td>';

					newcontent += '<td class="blackMediumNormal"><a href="javascript:thickbox_open(\''+members[i][0]+'\');"><?php echo _('Remarks'); ?></a></td>';
					newcontent += '<td width="8%" class="blackMediumNormal">';
					if( (members[i][13]=='2' || members[i][13]=='3') && members[i][16] <= members[i][17] && members[i][18] == 1) {
						newcontent += '     <a href="javascript:void(0);" id="trial_calendar_'+members[i][0]+'" class="trial_calendar" rel="'+members[i][0]+'">+<?php echo _('Trial Date ends on:'); ?>'+members[i][14]+'</a>';
						newcontent += '     <div id="trial_date_div_'+members[i][0]+'" style="display: none;" >';
						newcontent += '     <table style="margin:0 auto;" >';
						newcontent += '          <tr>';
						newcontent += '     <td><input name="trial_details_'+members[i][0]+'" id="trial_details_'+members[i][0]+'" type="text" class="textbox" size="10" value="" /><img border="0" alt="image" src="<?php echo base_url();?>assets/mcp/images/cal.jpeg" width="30" height="30" name="trial_date_picker_'+members[i][0]+'" id="trial_date_picker_'+members[i][0]+'" style="vertical-align:bottom"></td>';
						newcontent += '   <input type="hidden" class="co_email" id="co_email_'+members[i][0]+'" name="co_email_'+members[i][0]+'" value="'+members[i][10]+'" /></tr>';
						newcontent += '  <tr>';
						newcontent += ' <td><a href="javascript: void(0);" rel="'+members[i][0]+'" class="trial_update"><?php echo _("Update")?></a>&nbsp;&nbsp;<a href="javascript: void(0);" rel="'+members[i][0]+'" class="trial_update_cancel"><?php echo _("Close")?></a></td>';
						newcontent += '  </tr>';
						newcontent += ' </table>';
						newcontent += '     </div>';
						newcontent += '   <script type="text/javascript">var cal'+members[i][0]+' = Calendar.setup({ onSelect: function(cal'+members[i][0]+') { cal'+members[i][0]+'.hide() } }); cal'+members[i][0]+'.manageFields("trial_date_picker_'+members[i][0]+'", "trial_details_'+members[i][0]+'", "%Y-%m-%d");<\/script>'; 
                    }
                    newcontent += ' </td>';
					newcontent += '<td class="blackMediumNormal">';

					newcontent += '   <a href="javascript:void(0);" onclick="get_login_fdd(\''+members[i][0]+'\',\&quot;'+members[i][25]+'\&quot;,\''+members[i][26]+'\');" title="<?php echo _('Login to Client\\\'s Control Panel'); ?>" ><?php echo _('LOGIN'); ?></a>';


					newcontent += '<div id="reseller_remarks_'+members[i][0]+'" style="display:none;">\
					<table style="margin:0 auto;" cellspacing="8">\
				      <tr>\
						 <td>\
						 <textarea name="upd_remark_'+members[i][0]+'" id="upd_remark_'+members[i][0]+'">'+((members[i][27] != 0 && members[i][27] != 'null')?members[i][27]:"")+'</textarea>\
						 </td>\
					  </tr>\
					  <tr>\
						 <td>\
						 <input type="button" class="upd_remark btnWhiteBack" value="<?php echo _('Submit'); ?>" rel="'+members[i][0]+'">\
						 </td>\
					  </tr>\
				   </table>\
				   </div>';
							
					newcontent += '</td>';
					newcontent += '</tr>';

					
					/*newcontent += '<tr>';
					newcontent += '<td width="3%" class="blackMediumNormal">'+members[i][0]+'</td>';
					newcontent += '<td width="10%" class="blackMediumNormal"><a href="javascript:thickbox_open(\''+members[i][0]+'\');" class="thickbox_open">'+stripslashes(members[i][1])+'</a></td>';
					newcontent += '<td width="10%" class="blackMediumNormal">'+members[i][2]+'</td>';
					newcontent += '<td width="8%" class="blackMediumNormal">'+members[i][3]+'</td>';
					newcontent += '<td width="9%" class="blackMediumNormal" align="center">'+members[i][4]+'</td>';
					newcontent += '<td width="7%" class="blackMediumNormal">'+members[i][5]+'</td>';
					newcontent += '<td width="4%" class="blackMediumNormal">'+members[i][6]+'</td>';
					newcontent += '<td width="8%" align=\"center\" class="blackMediumNormal">'+members[i][20]+'</td>';
					newcontent += '<td width="8%" class="blackMediumNormal">'+members[i][12]+'</td>';
					newcontent += '<td width="8%" class="blackMediumNormal">';
					if( (members[i][13]=='2' || members[i][13]=='3') && members[i][17] <= members[i][18] && members[i][19] == 1) { 
						
						newcontent +='<a href="javascript:void(0);" id="trial_calendar_'+members[i][0]+'" class="trial_calendar" rel="'+members[i][0]+'">Trial Date ends on: '+members[i][14]+'</a>';
						
						newcontent += '<div id="trial_date_div_'+members[i][0]+'" style="display: none;" >\
									<table style="margin:0 auto;" >\
						 				<tr>\
							    		<td><input name="trial_details_'+members[i][0]+'" id="trial_details_'+members[i][0]+'" type="text" class="textbox" size="10" value="" /><img border="0" alt="image" src="<?php echo base_url();?>assets/mcp/images/cal.jpeg" width="30" height="30" name="trial_date_picker_'+members[i][0]+'" id="trial_date_picker_'+members[i][0]+'" style="vertical-align:bottom"></td>\
							    		<input type="hidden" class="co_email" id="co_email_'+members[i][0]+'" name="co_email_'+members[i][0]+'" value="'+members[i][15]+'"</tr>\
							    		<tr>\
							    		<td><a href="javascript: void(0);" rel="'+members[i][0]+'" class="trial_update"><?php echo _("Update")?></a>&nbsp;&nbsp;<a href="javascript: void(0);" rel="'+members[i][0]+'" class="trial_update_cancel"><?php echo _("Close")?></a>&nbsp;&nbsp;<a href="javascript: void(0);" rel="'+members[i][0]+'" class="trial_update_end"><?php echo _("End")?></a></td>\
									    </tr>\
							    		</table>\
							   		 </div>\
								   	 <script type="text/javascript">var cal'+members[i][0]+' = Calendar.setup({ onSelect: function(cal'+members[i][0]+') { cal'+members[i][0]+'.hide() } }); cal'+members[i][0]+'.manageFields("trial_date_picker_'+members[i][0]+'", "trial_details_'+members[i][0]+'", "%Y-%m-%d");<'+'/script>';
					}
					newcontent += '</td>';
					newcontent += '<td width="9%" align=\"center\" class="blackMediumNormal">'+members[i][21]+'</td>';
	
					newcontent += '<td align="center" width="9%" class="blackMediumNormal">'+members[i][7];
	
					newcontent += '<div id="company_details_'+members[i][0]+'" style="display:none;">\
									<table style="margin:0 auto;" cellspacing="8">\
								      <tr>\
									     <th style="text-align:right;"><?php echo _('Name'); ?> :</th>\
										 <td style="text-align:left;">'+members[i][11]+'</td>\
									  </tr>\
									  <tr>\
									     <th style="text-align:right;vertical-align:top;"><?php echo _('Address'); ?> :</th>\
										 <td style="text-align:left;">'+members[i][8]+'</td>\
									  </tr>\
									  <tr>\
									     <th style="text-align:right;"><?php echo _('Telephone'); ?> :</th>\
										 <td style="text-align:left;">'+members[i][9]+'</td>\
									  </tr>\
									  <tr>\
									     <th style="text-align:right;"><?php echo _('Email'); ?> :</th>\
										 <td style="text-align:left;"><a href="mailto:'+members[i][10]+'&body=<?php echo _('Hello').','; ?>">'+members[i][10]+'</a></td>\
									  </tr>\
								   </table>\
								   </div>';
	
					newcontent += '</td>';
					newcontent += '</tr>';*/
				
				jQuery('#partner_list').html(newcontent);
				}
			}else{
				jQuery('#partner_list').html('<tr><td valign="middle" height="40" align="center" colspan="12" class="redMediumBold" style="border:#003366 1px solid;"><strong><?php echo _('No Companies Found !!!');?></strong></td></tr>');
			}
		}
	});
    
	// Prevent click event propagation
	return false;
}
function getOptionsFromForm(){
	
	var opt = {callback: pageselectCallback};

	opt['items_per_page'] = 10;

	opt['num_display_entries'] = 4;

	opt['num_edge_entries'] = 2;

	opt['prev_text'] = '&laquo; <?php echo _('Prev'); ?>';

	opt['next_text'] = '<?php echo _('Next'); ?> &raquo;';

	return opt;

}
jQuery(document).ready(function(){

	var optInit =  jQuery(document).ready(function(){
		var optInit = getOptionsFromForm();		
		jQuery("#Pagination").pagination(dataLength, optInit);
	});
	
	jQuery("#Pagination").pagination(dataLength, optInit);
 });
</script>
<div style="width:100%">

  <input type="hidden" name="hdn_btn_search" id="hdn_btn_search" value="<?php if(isset($_POST['btn_search'])) { echo $_POST['btn_search']; } ?>" />
  <input type="hidden" name="hdn_search_by" id="hdn_search_by" value="<?php if(isset($_POST['search_by'])) { echo $_POST['search_by']; } ?>" />
  <input type="hidden" name="hdn_search_keyword" id="hdn_search_keyword" value="<?php if(isset($_POST['search_keyword'])) { echo $_POST['search_keyword']; } ?>" />
  <input type="hidden" name="hdn_ac_type_id" id="hdn_ac_type_id" value="<?php if(isset($_POST['ac_type_id'])) { echo $_POST['ac_type_id']; } ?>" />
  <input type="hidden" name="hdn_order_by" id="hdn_order_by" value="<?php if(isset($_POST['order_by'])) { echo $_POST['order_by']; } ?>" />

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
                                <td width="50%" align="left"><h3><?php echo _('Companies Associated'); ?></h3></td>
                                <td width="50%" align="right"><div onclick="history.back();" title="Back" style="background-image:url(<?php echo base_url(); ?>assets/mcp/images/undo.jpg); cursor:pointer; float:right" class="icon_button"></div>
                                 
								</td>
                              </tr>
                            </tbody>
                          </table></td>
                      </tr>
                      <tr>
                        <td align="center"><table width="100%" cellspacing="0" cellpadding="0" border="0" class="blackMediumNormal">
                            <tbody>
                              <tr>
                                <td><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>
								   assets/mcp/images/pink_table_bg.jpg) left repeat;">
                                    <tbody>
                                      <tr>
                                        <td height="20" bgcolor="#003366" class="whiteSmallBold"><?php echo _('Search Company'); ?></td>
                                      </tr>
                                      <tr>
                                        <td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid; padding:5px">
										   <table width="100%" 
										  cellspacing="0" cellpadding="0" border="0">
                                            <form action="<?php echo base_url().'rp/reseller'; ?>" enctype="multipart/form-data" method="post" id="frm_search" name="frm_search">
                                            
                                            <tbody>
                                              <tr>
                                                <td width="69" height="22" class="blackMediumNormal"><b><?php echo _('Search By'); ?></b></td>
                                                <td width="126">
												 <select style="width:120px" class="textbox" id="search_by" name="search_by">
                                                    <option value="0">- - <?php echo _('Search By'); ?> - -</option>
                                                    <option value="id"><?php echo _('ID'); ?></option>
                                                    <option value="company_name" selected="selected"><?php echo _('Company Name'); ?></option>
                                                    <option value="email"><?php echo _('Email'); ?></option>
                                                    <option value="username"><?php echo _('Username'); ?></option>
                                                    <option value="city"><?php echo _('City'); ?></option>
                                                  </select>
												</td>
                                                <td width="120" class="blackMediumNormal">
												   <b><?php echo _('Search Keyword'); ?></b>
												</td>
                                                <td width="160">
												  <input type="text" style="width:140px" class="textbox" id="search_keyword" name=
												  "search_keyword">
												</td>
												
												<td width="50" class="blackMediumNormal">
												   <b><?php echo _('Type'); ?></b>
												</td>
												<td>
												   <?php if(!empty($account_types)) { ?>
													<select class="textbox" name="ac_type_id" id="ac_type_id">
													 <option value=""><?php echo _('ALL'); ?></option>
													 <?php foreach($account_types as $at) { ?>
													  <option value="<?php echo $at->id; ?>">
													    <?php echo strtoupper($at->ac_title); ?>
													  </option>
													 <?php } ?>
													</select>
												   <?php } ?>
												</td>
												
												<td width="70" class="blackMediumNormal">
												   <b><?php echo _('Order By'); ?></b>
												</td>
												<td style="padding-right:10px;">
													<select class="textbox" name="order_by" id="order_by">
													 <option value=""><?php echo _('None'); ?></option>
													 <option value="id"><?php echo _('ID'); ?></option>
													 <option value="city"><?php echo _('City'); ?></option>
													 <option value="registration_date"><?php echo _('Registration Date'); ?></option>
													</select>												   
												</td>
												
                                                <td width="200"><span style="padding:0px 3px 3px 0px">
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
                                  </table></td>
                              </tr>
                              <tr>
                                <td height="22" align="right"><div style="float:right; width:80%; padding:5px;">
                                    <?php //echo $this->pagination->create_links(); ?>
									<div id="Pagination" class="Pagination"></div>
                                  </div></td>
                              </tr>
                              <tr>
                                <td><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/pink_table_bg.jpg) left repeat;border:1px solid #003366;">
                                    <thead>
                                      <tr style="background:#003366;">
                                        <td class="whiteSmallBold"><?php echo _('ID');?></td>
										<td class="whiteSmallBold"><?php echo _('Company Name');?></td>
										<td class="whiteSmallBold"><?php echo _('Start Date');?></td>
                                        <!-- <td class="whiteSmallBold"><?php //echo _('End Date');?></td> -->
                                        <td class="whiteSmallBold"><?php echo _('Total Products');?></td>
                                        <td class="whiteSmallBold"><?php echo _('Recipes OK');?></td>
                                        <td class="whiteSmallBold">%</td>
                                        <td class="whiteSmallBold" colspan="2"><?php echo _('More info');?></td>
                                        <!-- <td class="whiteSmallBold">TEL</td> -->
                                        <td class="whiteSmallBold"><?php echo _('Remarks');?></td>
                                        <!-- <td class="whiteSmallBold"><?php echo _('Status');?></td> -->
                                        <td class="whiteSmallBold"><?php echo _('Trial');?></td>
                                        <td class="whiteSmallBold">Actie</td>
                                      </tr>
                                    </thead>
                                    <tbody id="partner_list">
									<?php if(!empty($companies)) { /*foreach($companies as $c) { ?>
									  <tr>
									    <td class="blackMediumNormal" height="30px"><?php echo $c->id; ?></td>
										<td class="blackMediumNormal"><a onclick="show_company_data(<?php echo $c->id;?>)" href="javascript: void(0);"><?php echo $c->company_name; ?></a></td>
										<td class="blackMediumNormal"><?php echo $c->partner_invoice_date; ?></td>
										<td class="blackMediumNormal"><?php //echo $c->partner_total_amount; ?></td>
										<td class="blackMediumNormal"><?php echo $c->partner_total_commission; ?></td>
										<td class="blackMediumNormal" colspan="2"><?php echo $c->partner_message; ?></td>
										
										<!-- <td class="blackMediumNormal"><?php echo $c->phone; ?></td> -->
										<td class="blackMediumNormal"><?php echo ($c->partner_status)?'PAID':'UNPAID'; ?></td>
										<td width="8%" class="blackMediumNormal">
										<?php if($c->ac_type_id=='1'){?>
		                                     <a href="javascript:void(0);" id="trial_calendar_<?php echo $c->id;?>" class="trial_calendar" rel="<?php echo $c->id;?>"><?php echo _('Trial Date ends on:').$c->trial;?></a>
		                                     <div id="trial_date_div_<?php echo $c->id;?>" style="display: none;" >
						                     <table style="margin:0 auto;" >
		 				                      <tr>
			    		                     <td><input name="trial_details_<?php echo $c->id;?>" id="trial_details_<?php echo $c->id;?>" type="text" class="textbox" size="10" value="" /><img border="0" alt="image" src="<?php echo base_url();?>assets/mcp/images/cal.jpeg" width="30" height="30" name="trial_date_picker_<?php echo $c->id;?>" id="trial_date_picker_<?php echo $c->id;?>" style="vertical-align:bottom"></td>
			    		                   <input type="hidden" class="co_email" id="co_email_<?php echo $c->id;?>" name="co_email_<?php echo $c->id;?>" value="<?php echo $c->email;?>" /></tr>
			    		                  <tr>
			    		                 <td><a href="javascript: void(0);" rel="<?php echo $c->id;?>" class="trial_update"><?php echo _("Update")?></a>&nbsp;&nbsp;<a href="javascript: void(0);" rel="<?php echo $c->id;?>" class="trial_update_cancel"><?php echo _("Close")?></a></td>
					                      </tr>
			    		                 </table>
			   		                     </div>
			   	                        <script type="text/javascript">var cal<?php echo $c->id;?> = Calendar.setup({ onSelect: function(cal<?php echo $c->id;?>) { cal<?php echo $c->id;?>.hide() } }); cal<?php echo $c->id;?>.manageFields("trial_date_picker_<?php echo $c->id;?>", "trial_details_<?php echo $c->id;?>", "%Y-%m-%d");</script>
		                                 <?php }?>
		                                 </td>
										<td class="blackMediumNormal">
										   <a href="javascript:void(0);" onclick="get_login('<?php echo $c->id; ?>','<?php echo $c->username; ?>','<?php echo $c->password; ?>');" title="<?php echo _('Login to Client\'s Control Panel'); ?>" id="login_<?php echo $c->id; ?>"><?php echo _('LOGIN'); ?></a>
										</td>
									  </tr>
									<?php } */} else { ?>
									  <tr>
									    <td colspan="10" align="center" style="color:red; font-weight:bold;padding:10px;">
										  <?php echo _('Sorry ! No companies assigned yet.'); ?>
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
                  </table></td>
              </tr>
            </tbody>
          </table></td>
      </tr>
    </tbody>
  </table>
  <!-- ------------------------------------- For popup ---------------------------------------------- -->
  	<?php if(!empty($companies)) { foreach($companies as $c) { ?>
	  <div id="show_company_<?php echo $c->id;?>" style="display:none">					
			<table width="100%" border="0" cellspacing="8" cellpadding="0" class="override">							
				<tr>
					<td class="td_left" align="right"><strong>Type:</strong></td>
					<td class="td_right" align="left"><?php echo $c->company_type_name;?></td>
				</tr>
				<tr>
					<td class="td_left" >&nbsp;</td>
					<td class="td_right">&nbsp;</td>
				</tr>
				<tr>
					<td class="td_left" align="right"><strong>Voornaam:</strong></td>
					<td class="td_right" align="left"><?php echo $c->first_name;?></td>
				</tr>							
				<tr>
					<td class="td_left" align="right"><strong>Achternaam:</strong></td>
					<td class="td_right" align="left"><?php echo $c->last_name;?></td>
				</tr>
				<tr>
					<td class="td_left">&nbsp;</td>
					<td class="td_right">&nbsp;</td>
				</tr>
				<tr>
					<td class="td_left" align="right"><strong><?php echo _('Email')?>:</strong></td>
					<td class="td_right" align="left"><?php echo $c->email;?></td>
				</tr>
				<tr>
					<td class="td_left" align="right"><strong><?php echo _('Website')?>:</strong></td>
					<td class="td_right" align="left"><?php if($c->website){?><a href="<?php echo $c->website;?>" target="_blank"><?php echo $c->website;?></a><?php }else{?>----<?php }?></td>
				</tr>
				<tr>
					<td class="td_left">&nbsp;</td>
					<td class="td_right">&nbsp;</td>
				</tr>
				<tr>
					<td class="td_left" align="right"><strong>TEL:</strong></td>
					<td class="td_right" align="left"><?php echo $c->phone;?></td>
				</tr>
				<tr>
					<td class="td_left">&nbsp;</td>
					<td class="td_right">&nbsp;</td>
				</tr>
				<tr>
					<td class="td_left" align="right"><strong><?php echo _('Registration date')?>:</strong></td>
					<td class="td_right" align="left"><?php echo $c->registration_date;?></td>
				</tr>							
			</table>						
		</div>
	<?php } }?> 
  
</div>