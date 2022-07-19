<link href="<?php echo base_url(); ?>assets/cp/new_js/pagination/pagination.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>assets/cp/new_js/pagination/jquery.pagination.js" type="text/javascript"></script>
<script type="text/javascript">
   var base_url 			= '<?php echo base_url(); ?>';
   var fdd_tv_status_msg 	= "<?php echo _('FDD-TV status updated successfully !'); ?>";
   var failed_status_msg 	= "<?php echo _('Sorry ! Could not update'); ?>";
   var webshop_status_msg	= "<?php echo _('Webshop status updated successfully !'); ?>";
</script>
<link href="<?php echo base_url(); ?>assets/mcp/thickbox/css/thickbox.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>assets/mcp/thickbox/javascript/thickbox.js" type="text/javascript"></script>

<script type="text/javascript">

jQuery.noConflict();
jQuery('html').click(function(e) {   
   if(!jQuery(e.target).hasClass('login_dropdown') ){
        jQuery(".login_options").css('display','none');         
   }
}); 

jQuery(document).ready(function(){
	jQuery(document).on('click','.login_dropdown', function(e){
		var company_id = jQuery(this).data('comp_id');
	   	if(jQuery(".login_options").is(":visible")){
	        jQuery(".login_options").hide(); 
	        return false;        
	    }else{
			jQuery(".login_option"+company_id).show();
	    }
	});
	jQuery(document).on('change','.admin_country', function(e){

	});
	jQuery(".trial_calendar").live('click' , function(){
		var company_id = jQuery(this).attr('rel');
		jQuery(this).hide('slow');
		jQuery("#trial_date_div_"+company_id).toggle('slow');
	});

	jQuery(".trial_update_cancel").live('click' , function(){
		var company_id = jQuery(this).attr('rel');
		jQuery("#trial_date_div_"+company_id).toggle('slow');
		jQuery("#trial_calendar_"+company_id).toggle('slow');
	});

	jQuery(".trial_update_end").live('click' , function(){
		var company_id = jQuery(this).attr('rel');
		var co_email = jQuery("#co_email_"+company_id).val();
        jQuery.post("<?php echo site_url('mcp/companies/trial_date_end');?>",
				{   
				   'company_id':company_id,
				   'co_email':co_email
				},
					
				function(html){
					
						if(html.RESULT.trim()=='success')
						{   
						   alert ("<?php echo _('Trial ends successfully !'); ?>");
						   return true;
						}
						else
						{
						   alert ("<?php echo _('Sorry ! Could not end trial.'); ?>");
						   return false;
						}
				},'json');
		
	});
	
	jQuery(".trial_update").live('click' , function(){
		var company_id = jQuery(this).attr('rel');
		var date = jQuery("#trial_details_"+company_id).val();
		var co_email=jQuery("#co_email_"+company_id).val();
        jQuery.post("<?php echo site_url('mcp/companies/trial_date_insert');?>",
				{   
				   'company_id':company_id,
				   'date':date,
				   'co_email':co_email
				},
					
				function(html){
					
						if(html.RESULT.trim()=='success')
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

	jQuery(".fdd_tv_status").live('click' , function(){
		var comp_id 	= jQuery(this).closest('tr').attr( 'id' ),
			tv_status 	= 0;
		if( jQuery(this).is( ':checked' ) ) {
			tv_status = 1;
		}
		jQuery.post("<?php echo site_url('mcp/companies/update_tv_webshop_status');?>",{   
				   'comp_id': comp_id,
				   'status'	: tv_status,
				   'column'	: 'fdd_tv'
				},

				function(response){
					
						if(response.status.trim()=='success')
						{   
						   alert ( fdd_tv_status_msg );
						   return true;
						}
						else
						{
						   alert ( failed_status_msg );
						   return false;
						}
				},'json');

	});
		
	var check_obj;
	jQuery(document).on('change' ,'#webshop_value', function(){

		var comp_id = jQuery('#TB_ajaxContent').children().find('.webshop_comp_id').val();
		var shop_detail = jQuery(this).val();
		if(shop_detail == 0 || shop_detail == 1 || shop_detail == 2 || shop_detail == 3 ){
				jQuery.post("<?php echo site_url('mcp/companies/update_tv_webshop_status');?>",
				{   
				   'comp_id': comp_id,
				   'status'	: shop_detail,
				   'column'	: 'shop_version'
				},
					
				function(response){
						if(response.status.trim()=='success')
						{   
						   alert ( webshop_status_msg );
						    tb_remove();
						   return true;
						}
						else
						{
						   alert ( failed_status_msg );
						   return false;
						}
				},'json');
		}
	});

	jQuery(document).on('click' ,'.webshop_thickbox_close', function(){
		
		if(check_obj != undefined){
			jQuery(check_obj).attr('checked', false);
			tb_remove();
		}

	});

	jQuery(document).on('change','.webshop_status',function(){
		if(jQuery(this).is(':checked'))
		{
			var overlay = jQuery(document).find('#TB_overlay');
			jQuery(overlay).on('click',function(){
				jQuery(check_obj).attr('checked', false);
				tb_remove();
			});
		}
	});

	jQuery(".webshop_status").live('click' , function(){
		var comp_id 		= jQuery(this).closest('tr').attr( 'id' );
			webshop_status 	= 0;
		check_obj = jQuery(this);
	
		if( jQuery(this).is( ':checked' ) ) {
			tb_show('Webshop Details','TB_inline?height=50&width=300&inlineId=webshop_status_',null);
			jQuery('#TB_ajaxContent').children().find('.webshop_comp_id').val(comp_id);

		}
		else
		{
 			jQuery.post("<?php echo site_url('mcp/companies/update_tv_webshop_status');?>",
				{   
				   'comp_id': comp_id,
				   'status'	: 0,
				   'column'	: 'shop_version'
				},
					
				function(response){
						if(response.status.trim()=='success')
						{   
						   alert ( webshop_status_msg );
						   return true;
						}
						else
						{
						   alert ( failed_status_msg );
						   return false;
						}
				},'json');

		}
		jQuery('#TB_closeAjaxWindow').hide();
	});

});

function thickbox_open(id)
{
   tb_show('Company Details','TB_inline?height=230&width=300&inlineId=company_details_'+id,null);
}
function thickbox_trial_date(id)
{
   tb_show('Trial Date Details','TB_inline?height=230&width=300&inlineId=trial_date_details_'+id,null);
  
}

function company_status(id,status){
                        
	jQuery.post("<?php echo site_url('mcp/companies/company_status');?>",
	{   
	   'id':id,
	   'status':status
	},
		
	function(html){
		
			if(html.RESULT.trim()=='success')
			{   
			   alert ("<?php echo _('Status updated successfully !'); ?>");
			   return true;
			}
			else
			{
			   alert ("<?php echo _('Sorry ! Could not update the status.'); ?>");
			   return false;
			}
	},'json');
}

function change_ac_type(comp_id,ac_type_id){
    
	jQuery.post("<?php echo site_url('mcp/companies/change_ac_type');?>",
	{   
	   'id':comp_id,
	   'ac_type_id':ac_type_id
	},
		
	function(html){
		
			if(html.RESULT.trim()=='success')
			{   
			   alert ("<?php echo _('Account type has been updated successfully !'); ?>");
			   return true;
			}
			else
			{
			   alert ("<?php echo _('Sorry ! Could not update the account type.'); ?>");
			   return false;
			}
			
	},'json');
	
}
function change_data_type(comp_id,data_type){
    
	jQuery.post("<?php echo site_url('mcp/companies/change_data_type');?>",
	{   
	   'id':comp_id,
	   'data_type':data_type
	},
		
	function(html){
		
			if(html.RESULT.trim()=='success')
			{   
			   alert ("<?php echo _('Data has been updated successfully !'); ?>");
			   return true;
			}
			else
			{
			   alert ("<?php echo _('Sorry ! Could not update the data.'); ?>");
			   return false;
			}
			
	},'json');
	
}

function change_obsdesk_status(id,status){ 
                        
	jQuery.post("<?php echo site_url('mcp/companies/obsdesk_status');?>",
	{   
	   'id':id,
	   'status':status
	},
		
	function(html){
		
			if(html.RESULT.trim()=='success')
			{   
			   alert ("<?php echo _('Order Desk status updated successfully !'); ?>");
			   return true;
			}
			else
			{
			   alert ("<?php echo _('Sorry ! Could not update the desk status.'); ?>");
			   return false;
			}
	},'json');
}

function change_bo_shop_status(id,status){ 
    
	jQuery.post("<?php echo site_url('mcp/companies/change_bo_status');?>",
	{   
	   'id':id,
	   'value':status
	},
		
	function(html){
		
			if(html.RESULT.trim()=='success')
			{   
			   alert ("<?php echo _('Bestelonline shop status updated successfully !'); ?>");
			   return true;
			}
			else
			{
			   alert ("<?php echo _('Sorry ! Could not update the Bestelonline shop status.'); ?>");
			   return false;
			}
	},'json');
}

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

var members = new Array();
<?php $counter = 0; if(!empty($content)) { foreach($content as $cont){ 
      $account_types = $this->MCompanies->get_account_types();
	  $ct = $this->Mcompany_type->select(array('id'=>$cont->type_id)); 
	  if(!empty($ct))
		 $company_type = $ct[0]->company_type_name;
	  else
		 $company_type = _('NONE');
		 
	  $status = '<select style="width:90px" class="textbox" type="select" id="status" name="status" onchange="company_status('.$cont->id.',this.value);"><option value="0" '.(($cont->status==0)?'selected="selected"':'').'>'._('INACTIVE').'</option><option value="1" '.(($cont->status==1)?'selected="selected"':'').'>'._('ACTIVE').'</option></select>';
		
	  $action = '<a href="'.base_url().'mcp/companies/update/'.$cont->id.'">MOREINFO</a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="get_login(\''.$cont->id.'\',\''.$cont->username.'\',\''.$cont->password.'\');" id="login_'.$cont->id.'">LOGIN</a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="get_login_fdd(\''.$cont->id.'\' );" id="fdd2_login_'.$cont->id.'"> LOGIN 20</a>';

	  $ac_type = '';

      if(!empty($account_types))
      {
	     $ac_type = '<select class="textbox" name="ac_type_id" onchange="change_ac_type(\''.$cont->id.'\',this.value);">';
		 
		 foreach($account_types as $at)
		 {
		    $ac_type .= '<option value="'.$at->id.'" '.(($cont->ac_type_id==$at->id)?'selected="selected"':'').'>'.strtoupper($at->ac_title).'</option>';
		 }
		 
		 $ac_type .= '</select>';
	  }
	  
	  $obsdesk_status = '';
	  
	  $obsdesk_status .= '<select class="textbox" name="obsdesk_status" onchange="change_obsdesk_status(\''.$cont->id.'\',this.value);">';
      $obsdesk_status .= '<option value="0" '.(($cont->obsdesk_status==0)?'selected="selected"':'').'>'._('INACTIVE').'</option>';
	  $obsdesk_status .= '<option value="1" '.(($cont->obsdesk_status==1)?'selected="selected"':'').'>'._('ACTIVE').'</option>';
	  $obsdesk_status .= '</select>';
	  
	  $bestelonline_shop_status = '<select class="textbox" name="bestelonline_status">';
      $bestelonline_shop_status .= '<option value="0" '.(($cont->shop_testdrive==0)?'selected="selected"':'').'>'._('Active').'</option>';
	  $bestelonline_shop_status .= '<option value="1" '.(($cont->shop_testdrive==1)?'selected="selected"':'').'>'._('TestDrive').'</option>';
	  $bestelonline_shop_status .= '</select>';
	  
?><?php $trail_date1= strtotime($cont->trial);
$date = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y')));
$current=strtotime($date);?>
<?php echo "members[".$counter."] = ['".$cont->id."','".addslashes($cont->company_name)."','".addslashes($company_type)."','".addslashes($cont->city)."','".date('d-m-Y',strtotime($cont->registration_date))."','".addslashes($status)."','".addslashes($ac_type)."','".addslashes($action)."','".addslashes($cont->address.'<br />'.$cont->city.'<br />'.$cont->zipcode)."','".addslashes($cont->phone)."','".addslashes($cont->email)."','".addslashes($cont->first_name.' '.$cont->last_name)."','".addslashes($obsdesk_status)."','".$cont->ac_type_id."','".date('d-m-Y',$trail_date1)."','".$cont->email."','".$cont->excel_import_file_name."','".$current."','".$trail_date1."','".$cont->on_trial."',".$cont->last_30_day_order."'];\n";?>
<?php $counter++; }} ?>
var currentTime = new Date();
var month = currentTime.getMonth() + 1;
var day = currentTime.getDate();
var year = currentTime.getFullYear();
var current_date=month + "/" + day + "/" + year;

</script>




<!-- -----this section is for jquery pagination ------------------------------------- -->
<script type="text/javascript">
var members = new Array();
var dataLength = <?php echo $company_count;?>;
function urldecode(str) 
{
     if(str)
	 return unescape(str.replace(/\+/g, " "));
	 else
	 return '';
}

function pageselectCallback(page_index, jq){
	// Get number of elements per pagionation page from form
	var items_per_page = 10;
	var max_elem = Math.min(items_per_page, (dataLength -(items_per_page*(page_index))));
	var newcontent = '';

	var start_index = page_index*items_per_page;
	var end_index = max_elem;
	
	jQuery('#company_list').html('<tr><td width="93%" colspan="12"><img  height="200" width="200" style="margin-left:510px;" src="<?php echo base_url(); ?>assets/mcp/images/loading.gif" /></td></tr>');

	jQuery.ajax({
		url: '<?php echo base_url();?>mcp/companies/ajax_companies',
		type:'POST',
		dataType: 'json',
		data:{
				start: start_index,
				limit: end_index,
				btn_search: jQuery("#hdn_btn_search").val(),
				search_by: jQuery("#hdn_search_by").val(),
				search_keyword: jQuery("#hdn_search_keyword").val(),
				ac_type_id: jQuery("#hdn_ac_type_id").val(),
				order_by: jQuery("#hdn_order_by").val(),
				admin_country : jQuery("#hdn_country").val()
			},
		success: function(members){
			if(members.length){
				for(var i=0;(i<max_elem && i<members.length);i++)
				{ 
					newcontent += '<tr id="'+members[i][0]+'">';
					newcontent += '<td width="3%" class="blackMediumNormal">'+members[i][0]+'</td>';
					newcontent += '<td width="3%" class="blackMediumNormal">'+ (members[i][29] !='' ? members[i][29] : '--' ) +'</td>';
					newcontent += '<td width="10%" class="blackMediumNormal"><a href="javascript:thickbox_open(\''+members[i][0]+'\');" class="thickbox_open">'+stripslashes(members[i][1])+'</a></td>';
					newcontent += '<td width="10%" class="blackMediumNormal">'+members[i][2]+'</td>';
					if ( members[i][26] != '' && members[i][3] == ''  ) {
						city = "-- <span style='background-color: red;' > &nbsp&nbsp</span>";
					}
					else {
						city = members[i][3];
					}
					newcontent += '<td width="8%" class="blackMediumNormal">'+city+'</td>';
					newcontent += '<td width="9%" class="blackMediumNormal" align="center">'+members[i][4]+'</td>';
					newcontent += '<td width="9%" class="blackMediumNormal" align="center">'+members[i][30]+'</td>';
					newcontent += '<td width="7%" class="blackMediumNormal">'+members[i][5]+'</td>';
					newcontent += '<td width="4%" class="blackMediumNormal">'+members[i][6]+'</td>';
					newcontent += '<td width="4%" class="blackMediumNormal">'+members[i][33]+'</td>';
					newcontent += '<td width="8%" class="blackMediumNormal">'+members[i][12]+'</td>';
					newcontent += '<td width="6%" class="blackMediumNormal">'+members[i][25]+'</td>';
					newcontent += '<td width="3%" class="blackMediumNormal">'+members[i][20]+'</td>';
					newcontent += '<td width="3%" class="blackMediumNormal">'+members[i][21]+'</td>';
					newcontent += '<td width="9%" class="blackMediumNormal">'+members[i][22]+'</td>';
					newcontent += '<td width="9%" class="blackMediumNormal">'+members[i][27]+'</td>';
					newcontent += '<td width="9%" class="blackMediumNormal">'+members[i][31]+'</td>';
					newcontent += '<td width="9%" class="blackMediumNormal">'+members[i][32]+'</td>';
					// newcontent += '<td width="9%" align="center" class="blackMediumNormal">';
					// newcontent += '<input class="webshop_status" style="vertical-align:middle" type="checkbox" value="" ';
					// if(members[i][24] != 0)
					// {
					// 	newcontent += 'checked="checked" /><span> Webshop </span>';
					// }
					// else{
					// 	newcontent += '/><span> Webshop </span>';
					// }	
					// newcontent += '</td>';
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

		
					newcontent += '</tr>';
				
				jQuery('#company_list').html(newcontent);
				}
			}else{
				jQuery('#company_list').html('<tr><td valign="middle" height="40" align="center" colspan="12" class="redMediumBold" style="border:#003366 1px solid;"><strong><?php echo _('No Companies Found !!!');?></strong></td></tr>');
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
  <input type="hidden" name="hdn_country" id="hdn_country" value="<?php if(isset($_POST['admin_country'])) { echo $_POST['admin_country']; } ?>" />
  
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
                        <td align="center" style="padding-bottom:10px">
						
						<table width="100%" cellspacing="0" cellpadding="0" border="0" style="background
						  :url(<?php echo base_url(); ?>assets/mcp/images/bg.jpg) left top repeat-x;" class="page_caption">
                            <tbody>
                              <tr height="26">
                                <td width="50%" align="left"><h3><?php echo _('Companies Manager'); ?></h3></td>
                                <td width="50%" align="right">
                                  
								  <div onclick="history.back();" title="Back" style="background-image:url(<?php echo base_url(); ?>assets/mcp/images/undo.jpg); cursor:pointer; float:right" class="icon_button"></div>
                                  <div style="background-image:url(<?php echo base_url(); ?>assets/mcp/images/add.png);float:right;" title="<?php echo _('Add Company'); ?>" class="icon_button" onClick="window.location.href='<?php echo base_url(''); ?>mcp/companies/companies_add_edit';" id="btn_add"> </div>
                                  <div>
                                  	<?php
                                  	if ( $admin_data['country-all'] == 'yes' ) { ?>
                                  		<form method="post">
										    <select name="admin_country" onchange="this.form.submit();" style="font-size: 10px;">
										        <option value="21" <?php if( $admin_data['admin_country'] == '21' ) echo "selected" ?> ><?php echo _('Belgium'); ?></option>
	                                  			<option value="150" <?php if( $admin_data['admin_country'] == '150' ) echo "selected" ?> ><?php echo _('Netherlands'); ?></option>
										    </select>
										</form>
                                  	<?php 	
                                  	} 
                                  	?>
                                  	
                                  </div>
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
                                            <form action="<?php echo base_url().'mcp/companies'; ?>" enctype="multipart/form-data" method="post" id="frm_search" name="frm_search">
                                            
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
                                <td height="40" align="right">
								   
								   <div style="float:right; width:80%; padding:5px;">
								
								       <div id="Pagination" class="Pagination"></div>
								   
								   </div>
								</td>
                              </tr>
                              <tr>
                                <td><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/pink_table_bg.jpg) left repeat;border:1px solid #003366;">
                                    <thead> 
                                      <tr style="background:#003366;">
                                        <td width="3%" class="whiteSmallBold"><?php echo _('ID');?></td>
                                        	<td width="3%" class="whiteSmallBold"><?php echo _('Client Nbr');?></td>
											<td width="10%" class="whiteSmallBold"><?php echo _('Company Name');?></td>
											<td width="10%" class="whiteSmallBold"><?php echo _('Company Type');?></td>
											<td width="8%" class="whiteSmallBold"><?php echo _('City');?></td>
											<td width="9%" class="whiteSmallBold" align="center"><?php echo _('Registration Date');?></td>
											<td width="9%" class="whiteSmallBold" align="center"><?php echo _('End Date');?></td>
											<td width="7%" class="whiteSmallBold"><?php echo _('Status');?></td>
											<td width="4%" class="whiteSmallBold"><?php echo _('Type');?></td>
											<td width="4%" class="whiteSmallBold"><?php echo _('Data');?></td>
											<td width="8%" class="whiteSmallBold"><?php echo _('Desk Status');?></td>
											<td width="6%" class="whiteSmallBold"><?php echo _('Lang');?></td>
											<td width="3%" class="whiteSmallBold"><?php echo _('Total Recipes');?></td>
											<td width="4%" class="whiteSmallBold"><?php echo _('Recipes OK');?></td>
											<td width="9%" class="whiteSmallBold"><?php echo _('%');?></td>
											<td width="9%" class="whiteSmallBold"><?php echo _('Favorites');?></td>
											<!-- 08/04/2019 -->
											<td width="9%" class="whiteSmallBold"><?php echo _('Last login');?></td>
											<td width="9%" class="whiteSmallBold"><?php echo _('Linked');?></td>
											<!-- <td width="4%" class="whiteSmallBold"><?php echo _('Mailing');?></td> -->
											<!-- <td width="5%" class="whiteSmallBold"><?php echo _('Clients');?></td> -->
											<td width="14%" align="center" class="whiteSmallBold"><?php echo _('Action');?></td>
                                      </tr>
									</thead>
									<tbody id="company_list">  
										<tr>
	                                        <td width="93%" colspan="12"><img  height="200" width="200" style="margin-left:510px;" src="<?php echo base_url(); ?>assets/mcp/images/loading.gif" /></td>	                                        
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
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td style="padding-left:20px;">
           <img src="<?php echo base_url(); ?>assets/mcp/images/arrow_down_green.png" style="float:left;" />
           <a href="<?php echo base_url()?>mcp/companies/download_active_emails" style="color:#205A46; font-weight:bold;float:left;">
             <?php echo _("Export Email Addresses of all ACTIVE Companies");?>
           </a>
           <a href="<?php echo base_url()?>mcp/companies/get_companies_without_logo" class="cmp_without_logo" style="color:#205A46;font-weight:normal;float:left;margin-left:16px;"><?php echo _('Show all shops without logo');?></a>
           <a href="<?php echo base_url()?>mcp/companies/download_client_system_detail_excel" class="cmp_without_logo" style="color:#205A46;font-weight:normal;float:left;margin-left:16px;"><?php echo _('Company detail using system');?></a>
 

           <a class="no_image_xls" href="<?php echo base_url()?>mcp/companies/download_no_images" style="color:#205A46;float:left;margin-left:16px;">
             <?php echo _("XLS allergenenchecker-no-images");?>
           </a>
            <a class="light_admins" href="<?php echo base_url()?>mcp/companies/download_light_admins" style="color:#205A46;float:left;margin-left:16px;">
             <?php echo _("XLS allergenenchecker-social-backlinks");?>
           </a>
        </td>
      </tr>
            <tr>
	      <td style="padding:20px;">
		      <form action="<?php echo base_url().'mcp/companies'; ?>" enctype="multipart/form-data" method="post" id="frm_maint" name="frm_maint">
			      <input type="checkbox" name="maintenence" value="1" <?php if ($maint == '1'){?>checked<?php }?>> <span><?php echo _('Product maintenance mode');?></span>
				  <input type="submit" value="Submit" name="sub_main">
			</form> 
			<form action="<?php echo base_url().'mcp/companies'; ?>" enctype="multipart/form-data" method="post">
			      <input type="checkbox" name="label_api" value="1" <?php if ($label_api == '1'){?>checked<?php }?>> <span><?php echo _('LABELER API active');?></span>
				  <input type="submit" value="Submit" name="label_api_main">
			</form>
			<form action="<?php echo base_url().'mcp/companies'; ?>" enctype="multipart/form-data" method="post">
			      <input type="checkbox" name="digi_api" value="1" <?php if ($digi_api == '1'){?>checked<?php }?>> <span><?php echo _('DIGI API active');?></span>
				  <input type="submit" value="Submit" name="digi_api_main">
			</form>
			<form action="<?php echo base_url().'mcp/companies'; ?>" enctype="multipart/form-data" method="post">
			      <input type="checkbox" name="xerxes_api" value="1" <?php if ($xerxes_api == '1'){?>checked<?php }?>> <span><?php echo _('XERXES API active');?></span>
				  <input type="submit" value="Submit" name="xerxes_api_main">
			</form>
	      
	      </td>
      </tr>
      <!-- <tr>
	      <td style="padding:20px;">
		      <form action="<?php echo base_url().'mcp/companies'; ?>" enctype="multipart/form-data" method="post" id="frm_maint" name="frm_maint">
			      <input type="checkbox" name="maintenence" value="1"> <span><?php echo _('maintenance mode');?></span>
				  <input type="submit" value="Submit" name="sub_main">
			</form> 
	      
	      </td>
      </tr> -->
      <tr>
        <td align="right" style="padding-right:50px">
        <a href="<?php echo base_url()?>mcp/overview/recipe_entered_statistics"><?php echo _("Recipe-entered-statistics");?></a>
           <a href="<?php echo base_url()?>mcp/companies/download_client_excel"><img width="200" height="70" border="0" src="<?php echo base_url(); ?>assets/mcp/images/download_excel.jpg"></a>
        </td>
      </tr>
    </tbody>
  </table>

					   
  <!-- end of main body -->
</div>

<div id="webshop_status_" style="display:none;">
	
	<div style="float:right;left:auto;position: absolute;right: 3px;top:3px" class="webshop_thickbox_close"><a class="webshop_thickbox_close" href="#"><img width="16" height="16" src="<?php echo base_url();?>assets/mcp/thickbox/javascript/Delete.gif"></a></div>
	<table style="margin:0 auto;" cellspacing="8">
	  <tr class = "succ_msg" style="display: none">
	  	<td><?php echo _('Shop Updated Successfully'); ?></td>
	  </tr>
      <tr>
	     <td><?php echo _(' Select Webshop'); ?></td>
		 <td><select id = "webshop_value" class="textbox">
		 	<option value="0"><?php echo _(' Select Shop'); ?></option>
		 	<option value="1"><?php echo _(' Old Shop'); ?></option>
		 	<option value="2"><?php echo _(' New Shop'); ?></option>
		 	<option value="3"><?php echo _(' Iframe Shop'); ?></option>
		 	</select>
		 </td>
		 <input type="hidden" class="webshop_comp_id" value=""></input>
		
	  </tr>
   </table>
</div>
<script type="text/javascript">
	jQuery(document).on('change' ,'#mailing_pack', function(){
		var comp_id = jQuery(this).parent('.blackMediumNormal').parent('tr').attr('id');
		var pack_id = jQuery(this).find(":selected").val();
		// alert(pack_id);
		 jQuery.post("<?php echo site_url('mcp/companies/update_mailing_package');?>",
				{   
				   'company_id':comp_id,
				   'pack_id' : pack_id 
				},	
				function(html){
					
						if(html.RESULT.trim()=='success')
						{   
						   alert ("<?php echo _('Pacakege updated successfully !'); ?>");
						   return true;
						}
						else
						{
						   alert ("<?php echo _('Sorry ! Could not update the Package.'); ?>");
						   return false;
						}
				},'json');
		

	});

</script>