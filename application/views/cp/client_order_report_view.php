<script type="text/javascript" src="<?php echo base_url();?>assets/cp/new_js/jquery.form.js"></script>
<script type="text/javascript">
function process_date_format(date,ret_format,field_update){
   jQuery.post(base_url+'cp/cdashboard/ordered_products',{'act':'format_date','date':date,'format':ret_format},function(date){
      jQuery('#'+field_update).val(date);   
   });
}

function export_report(action_set, date_1, date_2, filter_type){
	window.location = '<?php echo base_url();?>cp/client_orders_report/export_seperate_orders/'+action_set+'/'+filter_type+'/'+date_1+'/'+date_2;
}

function delete_this(id){
	if(confirm('<?php echo _("You want to delete this report?");?>')){
		$.get(
				'<?php echo base_url();?>cp/client_orders_report/delete/'+id,
				function(response){
					alert(response.message);
					if(!response.error){
						$("#row_"+id).remove();
					}
				},
				'json'
		);
	}
}

function delete_selected(){
	var val = new Array();
	var i = 0;
	$("input:checkbox[name='delete[]']:checked").each(function(index){
		val[i] = $(this).val();
		i++;
	});
	if(confirm('<?php echo _("You want to delete these report?");?>')){
		$.post(
				'<?php echo base_url();?>cp/client_orders_report/delete_all/',
				{ ids: val},
				function(response){
					alert(response.message);
					if(!response.error){
						for(var i = 0; i < val.length; i++){
							$("#row_"+val[i]).remove();
						}
					}
				},
				'json'
		);
	}
}
</script>
<script>
$(document).ready(function(){
	var options = { 
	dataType: 'json',
    beforeSend: function() 
    {
    	$("#progress").show();
    	//clear everything
    	/*$("#bar").width('0%');
    	$("#message").html("");
		$("#percent").html("0%");*/
    },
    uploadProgress: function(event, position, total, percentComplete) 
    {
    	/*$("#bar").width(percentComplete+'%');
    	$("#percent").html(percentComplete+'%');*/
    },
    success: function(response) 
    {
        /*$("#bar").width('100%');
    	$("#percent").html('100%');*/
		if(response.error.trim() == "1"){
			alert(response.error);
		}else{
			var data = response.data;
			var new_row = '';
			new_row += "<tr id=\'row_"+data.id+"\'>";
			new_row += "<td width='3%' valign='top'>";
			new_row += '		<input type="checkbox" name="delete[]" id="delete[]" value=\"'+data.id+'\" />';
			new_row += '	</td>';
			new_row += '	<td width="15%" valign="top" >';
		  	new_row += '		'+data.date;
		  	new_row += '	</td>';
		  	new_row += '	<td width="20%" valign="top">';
		  	new_row += '		'+data.start_date+' - '+data.end_date;
		  	new_row += '	</td>'
			/*
		  	new_row += '	<td width="15%" valign="top">';
		  	if(data.invoice == "show_invoice")
		  		new_row += '<?php echo _("Only Invoice");?>';
		  	else if(data.invoice == "show_without_invoice")
		  		new_row += '<?php echo _("Without Invoice");?>';
      		else
      			new_row += '<?php echo _("ALL");?>';
		  	new_row += '	</td>';
		  	new_row += '	<td width="10%" valign="top">';
		  	new_row += '		'+( (data.type == "full")?'<?php echo _("Full report")?>':'<?php echo _("Short report")?>');
		  	new_row += '	</td>';
		  	*/
		  	new_row += '	<td width="27%" valign="top">';
		  	/*new_row += '		<a href="<?php echo base_url();?>assets/pdf_reports/download.php?f='+data.report_name+'" >'+data.report_name+'</a> ('+( ( data.size > (1024*1024) )?( data.size/(1024*1024) ).toFixed(2)+" MB":( data.size/1024 ).toFixed(2)+" KB")+')';*/
		  	new_row += '		<a href="<?php echo base_url();?>assets/pdf_reports/download.php?f='+data.report_name+'" >'+data.report_name+'</a> ('+data.f_size+')';
		  	new_row += '	</td>';
        new_row+='<td width="13%" valign="top">'+data.report_type+'</td>';
		  	new_row += '	<td width="10%" valign="top">';
		  	new_row += '		<a href="javascript:void(0);" class="delete_single" onClick="delete_this('+data.id+')" ><img width="16" height="16" border="0" alt="remove" src="<?php echo base_url();?>assets/cp/images/delete.gif" class="v_align_middle"></a>';
		  	new_row += '	</td>';
		  	new_row += '</tr>';
        //monu-start
        new_row_h='<tr>\
              <th width="3%"><input type="checkbox" name="delete_all" id="delete_all" /></th>\
              <th width="15%"><?php echo _("Date");?></th>\
              <th width="20%"><?php echo _("Daterange");?></th>\
              <!-- <th width="15%"><?php echo _("Invoices");?></th>\
              <th width="10%"><?php echo _("Type");?></th> -->\
              <th width="27%"><?php echo _("File");?></th>\
              <th><?php echo _("Report Type"); ?></th>\
              <th width="10%"><?php echo _("Action");?></th>\
            </tr>';
        $("#reports_list thead").html(new_row_h);  
		  	$("#reports_list tbody").html(new_row);
		  	$("#reports_list").show();
        //monu
		}
    },
	complete: function(response) 
	{
		//$("#message").html("<font color='green'>"+response.responseText+"</font>");
		$("#progress").hide();
	},
	error: function()
	{
		$("#progress").hide();

	}
	}; 
  //monu
  var option_search = { 
  dataType: 'json',
    beforeSend: function() 
    {
      $("#progress").show();
      //clear everything
      /*$("#bar").width('0%');
      $("#message").html("");
    $("#percent").html("0%");*/
    },
    uploadProgress: function(event, position, total, percentComplete) 
    {
      /*$("#bar").width(percentComplete+'%');
      $("#percent").html(percentComplete+'%');*/
    },
    success: function(response) 
    {
        /*$("#bar").width('100%');
      $("#percent").html('100%');*/
    if(response.error.trim() == "1"){
      alert(response.error);
    }else{
      var data = response.data;
      var new_row = '';
      new_row += '<tr id=\"row_'+data.id+'\">';
      //new_row += '  <td width="3%" valign="top">';
      //new_row += '    <input type="checkbox" name="delete[]" id="delete[]" value=\"'+data.id+'\" />';
     // new_row += '  </td>';
        new_row += '  <td width="15%" valign="top" >';
        new_row += '    '+data.date;
        new_row += '  </td>';
        new_row += '  <td width="20%" valign="top">';
        new_row += "    "+data.byDetail;
        new_row += '  </td>'
      /*
        new_row += '  <td width="15%" valign="top">';
        if(data.invoice == "show_invoice")
          new_row += '<?php echo _("Only Invoice");?>';
        else if(data.invoice == "show_without_invoice")
          new_row += '<?php echo _("Without Invoice");?>';
          else
            new_row += '<?php echo _("ALL");?>';
        new_row += '  </td>';
        new_row += '  <td width="10%" valign="top">';
        new_row += '    '+( (data.type == "full")?'<?php echo _("Full report")?>':'<?php echo _("Short report")?>');
        new_row += '  </td>';
        */
        new_row += '  <td width="27%" valign="top">';
        /*new_row += '    <a href="<?php echo base_url();?>assets/pdf_reports/download.php?f='+data.report_name+'" >'+data.report_name+'</a> ('+( ( data.size > (1024*1024) )?( data.size/(1024*1024) ).toFixed(2)+" MB":( data.size/1024 ).toFixed(2)+" KB")+')';*/
        new_row += '    <a href="<?php echo base_url();?>assets/pdf_reports/download.php?f='+data.report_name+'" >'+data.report_name+'</a> ('+data.f_size+')';
        new_row += '  </td>';
        /*new_row += '  <td width="10%" valign="top">';
        new_row += '    <a href="javascript:void(0);" class="delete_single" onClick="delete_this('+data.id+')" ><img width="16" height="16" border="0" alt="remove" src="<?php echo base_url();?>assets/cp/images/delete.gif" class="v_align_middle"></a>';
        new_row += '  </td>';*/
        new_row += '</tr>';

        new_row_h='';
        new_row_h+='<tr>\
              <th width="15%"><?php echo _("Date");?></th>\
              <th width="20%">'+data.by+'</th>\
              <th width="27%"><?php echo _("File");?></th>\
            </tr>';
        $("#reports_list thead").html(new_row_h);    
        $("#reports_list tbody").html(new_row);
        $("#reports_list").show();
    }
    },
  complete: function(response) 
  {
    //$("#message").html("<font color='green'>"+response.responseText+"</font>");
    $("#progress").hide();
  },
  error: function()
  {
    $("#progress").hide();

  }
  }; 

    $("#frm_search").ajaxForm(options);
    $("#frm_search2").ajaxForm(options);
    $("#frm_search1").ajaxForm(option_search);
    $("#delete_all").click(function(){
        if($(this).is(":checked")){
            $("#reports_list").children('tbody').find('input').prop("checked",true);
        }else{
        	$("#reports_list").children('tbody').find('input').prop("checked",false);
        }
    });
});
//monu
</script>
<style>
#order_report_div span {
/*display: block;*/
}
span.bold {
	font-weight: bold;
}
span.small {
	font-size: 13px;
}
span.medium {
	font-size: 14px;
}
.order_number{
	margin-bottom:15px;
}
.order_date{
	margin-top:6px;
}
span.large {
	font-size: 16px;
    margin-top: 40px;
}
span.underline {
	text-decoration: underline;
}
#prod_list tr td {
	border-bottom: 2px dotted #ccc;
	border-top: none;
	padding: 5px 10px;
}
#progress {
	text-align: center;
	font-weight: bold;
	display: none;
	padding: 10px 0;
}

#progress img {
	vertical-align: middle;
}

#go_back{
	text-align: center;
	font-weight: bold;
	padding: 10px 0;
}

.error_msg{
	text-align: center;
	color: #AA0000;
}
</style>
<div id="main">
<div id="main-header">
  <h2><?php echo _('Report'); ?></h2>
  <span class="breadcrumb"><a href="<?php echo base_url()?>cp/cdashboard"><?php echo _('Home'); ?></a> &raquo; <?php echo _('Report'); ?></span>
  <?php $messages = $this->messages->get();?>
  <?php if($messages != array()): foreach($messages as $type => $message):?>
  <?php if($type == 'success' && $message != array()):?>
  <br />
  <br />
  <div id="succeed"><?php echo $message[0];?></div>
  <?php elseif($type == 'error' && $message != array()):?>
  <br />
  <br />
  <div id="error"><strong><?php echo _('Error')?></strong>:<?php echo $message[0];?></div>
  <?php endif;?>
  <?php endforeach; endif;?>
</div>
<div id="content">
  <div id="content-container">
  	
  	<?php if(!isset($orderData)){?>
    <!-- monu-start-->
    <div class="box">
      <h3><?php echo _('Search Order')?></h3>
      <div class="table">
        <form action="<?php echo base_url();?>cp/client_orders_report/export_seperate_orders_without_date"  method="post" id="frm_search1" name="frm_search1">
          <input type="hidden" value="clients" name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW">
          <table cellspacing="0">
            <tbody>
              <tr>
                <td style="border:none;"><strong><?php echo _('Search')?></strong></td>
                <td align="justify" style="border:none;"><select class="select" id="search_by" name="search_by">
                    <option value="0"><?php echo _('- - Select - -')?></option>
                    <option value="Name"><?php echo _('Name')?></option>
                    <option value="Email"><?php echo _('Email')?></option>
                    <option value="order_number"><?php echo _('Order Number')?></option>
                  </select></td>
                <td style="border:none;"><strong><?php echo _('Keyword')?></strong></td>
                <td valign="baseline" style="border:none;"><input type="text" class="text" id="search_keyword" name="search_keyword"></td>
                <td valign="middle" style="border:none;"><input type="submit" value="<?php echo _('Search')?>" class="submit" id="btn_search" name="btn_search">
                  <input type="button" onclick="this.form.search_by.selectedIndex=0; this.form.search_keyword.value='';" value="Reset" class="submit" id="btn_reset" name="btn_reset">
                  <input type="hidden" value="do_filter" id="act" name="act">
                  <input type="hidden" value="clients" id="view" name="view"></td>
                <td width="20%">&nbsp;</td>
              </tr>
            </tbody>
          </table>
        </form>
        <script type="text/javascript" language="JavaScript">
        var frmvalidator = new Validator("frm_search1");
        frmvalidator.EnableMsgsTogether();
        frmvalidator.addValidation("search_by","dontselect=0","<?php echo _('Please enter a search item, the dropdown list')?>");
        frmvalidator.addValidation("search_keyword","req","<?php echo _('Please provide a keyword please')?>");
        </script>
      </div>
    </div>
    <!-- monu-end -->
    <div class="box">
      <h3><?php echo _("Search Orders");?></h3>
      <div class="table">
        <form name="frm_search" id="frm_search" action="<?php echo base_url();?>cp/client_orders_report/export_seperate_orders" method="post" style="border-bottom:1px solid #E2E2E2;">
          <table cellspacing="0" border="0" width="100%" cellpadding="0">
            <tbody>
              <!-- 
              <tr>
                <td valign="middle" align="justify" colspan="5">
                	<input type="radio" name="show_all_invoice" id="show_all" value="show_all" onclick="javascript: $('#invoice_confirmation').val(this.value);" checked="checked" />
                  	<?php echo _("Show all");?>
                  	<input type="radio" name="show_all_invoice" id="show_invoice" value="show_invoice" onclick="javascript: $('#invoice_confirmation').val(this.value);" />
                  	<?php echo _("Show only invoices");?>
                  	<input type="radio" name="show_all_invoice" id="show_without_invoice" value="show_without_invoice" onclick="javascript: $('#invoice_confirmation').val(this.value);" />
                  	<?php echo _("Show all without invoices");?>
                </td>
              </tr>
               -->
              <tr>
                <td width="16%"><strong><?php echo _('All orders ordered')?></strong></td>
                <td valign="middle" align="justify" width="22%"><div style="float:left">
                    <input type="text" class="text" readonly name="e_start_date" id="e_start_date">
                    <input type="hidden" name="start_date" id="start_date" onChange="process_date_format(this.value,'d-m-Y','e_start_date');">
                  </div>
                  <div style="float:left">
                    <input type="button" value="..." onclick="displayCalendar(document.frm_search.start_date,'yyyy-mm-dd',this);" name="button1" id="button1">
                  </div></td>
                <td width="3%"><strong><?php echo _('to'); ?></strong></td>
                <td valign="middle" align="justify" width="23%"><div style="float:left">
                    <input type="text" class="text" readonly name="e_end_date" id="e_end_date"/>
                    <input type="hidden" name="end_date" id="end_date" onChange="process_date_format(this.value,'d-m-Y','e_end_date');">
                  </div>
                  <div style="float:left">
                    <input type="button" value="..." onclick="displayCalendar(document.frm_search.end_date,'yyyy-mm-dd',this);" name="button2" id="button2"/>
                  </div></td>
                <!-- 
                <td width="31%" valign="middle">
                	<input type="submit" class="submit" value="<?php echo _('Full report')?>" name="full" id="btn_search"/>
                	<input type="submit" class="submit" value="<?php echo _('Short report')?>" name="short" id="btn_search"/>
                  	<input type="button" class="submit" value="<?php echo _('Reset')?>" onclick="this.form.reset();" name="btn_reset" id="btn_resset"/>
                  	<input id="act" type="hidden" name="act" value="do_filter">
                </td>
                 -->
                 <td width="36%" valign="middle">
                 	<input type="checkbox" name="hide_zero_orders" value="hide_zero_orders" /> <?php echo _('Hide 0 Orders'); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                	<input type="submit" class="submit" value="<?php echo _('Report per client')?>" name="full" id="btn_search"/>                	
                  	<input type="button" class="submit" value="<?php echo _('Reset')?>" onclick="this.form.reset();" name="btn_reset" id="btn_resset"/>
                  	<input id="act" type="hidden" name="act" value="do_filter">
                </td>
              </tr >
              </tbody>
          </table>
        </form>
    <script language="JavaScript" type="text/javascript">
      var frmvalidator = new Validator("frm_search");
      frmvalidator.EnableMsgsTogether();
      frmvalidator.addValidation("start_date","req","<?php echo _('Please enter start date')?>");
      frmvalidator.addValidation("end_date","req","<?php echo _('Please enter end date')?>");
    </script>
    <!-- monu-start -->
        <form name="frm_search2" id="frm_search2" action="<?php echo base_url();?>cp/client_orders_report/export_seperate_orders_pickup" method="post" style="border-bottom:1px solid #E2E2E2;">
          <table cellspacing="0" border="0" width="100%" cellpadding="0">
            <tbody>
              <tr>
                <td width="16%"><strong><?php echo _('All orders for pickup / delivery')?></strong></td>
                <td valign="middle" align="justify" width="22%"><div style="float:left">
                    <input type="text" class="text" readonly name="e_start_date_pick" id="e_start_date_pick">
                    <input type="hidden" name="start_date_pick" id="start_date_pick" onChange="process_date_format(this.value,'d-m-Y','e_start_date_pick');">
                  </div>
                  <div style="float:left">
                    <input type="button" value="..." onclick="displayCalendar(document.frm_search2.start_date_pick,'yyyy-mm-dd',this);" name="button1" id="button1">
                  </div></td>
                <td width="3%"><strong><?php echo _('to'); ?></strong></td>
                <td valign="middle" align="justify" width="23%"><div style="float:left">
                    <input type="text" class="text" readonly name="e_end_date_pick" id="e_end_date_pick"/>
                    <input type="hidden" name="end_date_pick" id="end_date_pick" onChange="process_date_format(this.value,'d-m-Y','e_end_date_pick');">
                  </div>
                  <div style="float:left">
                    <input type="button" value="..." onclick="displayCalendar(document.frm_search2.end_date_pick,'yyyy-mm-dd',this);" name="button2" id="button2"/>
                  </div></td>
                <!-- 
                <td width="31%" valign="middle">
                  <input type="submit" class="submit" value="<?php echo _('Full report')?>" name="full" id="btn_search"/>
                  <input type="submit" class="submit" value="<?php echo _('Short report')?>" name="short" id="btn_search"/>
                    <input type="button" class="submit" value="<?php echo _('Reset')?>" onclick="this.form.reset();" name="btn_reset" id="btn_resset"/>
                    <input id="act" type="hidden" name="act" value="do_filter">
                </td>
                 -->
                 <td width="36%" valign="middle">
                  <input type="checkbox" name="hide_zero_orders_pick" value="hide_zero_orders" /> <?php echo _('Hide 0 Orders'); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  <input type="submit" class="submit" value="<?php echo _('Report per client')?>" name="full" id="btn_search"/>                 
                    <input type="button" class="submit" value="<?php echo _('Reset')?>" onclick="this.form.reset();" name="btn_reset" id="btn_resset"/>
                    <input id="act" type="hidden" name="act" value="do_filter">
                </td>
              </tr >
            </tbody>
          </table>
        </form>
    <script language="JavaScript" type="text/javascript">
			var frmvalidator = new Validator("frm_search2");
			frmvalidator.EnableMsgsTogether();
			frmvalidator.addValidation("start_date_pick","req","<?php echo _('Please enter start date')?>");
			frmvalidator.addValidation("end_date_pick","req","<?php echo _('Please enter end date')?>");
		</script>
    <!-- monu-end -->
		<!-- 
        <form name="frm_set" id="frm_set" action="" method="post">
          <input type="hidden" name="invoice_confirmation" id="invoice_confirmation" value="show_all" />
          <table cellspacing="0" border="0" width="90%" cellpadding="0">
            <tbody>
              <tr>
                <td><strong><?php echo _('Or - Print all orders for')?></strong></td>
                <td><input type="hidden" name="date_tomorrow" value="<?php echo date('Y-m-d',strtotime( date('Y-m-d H:i:s',time())." +1 day" )); ?>">
                  <input type="submit" name="tomorrow" value="<?php echo _('Tomorrow'); ?> (<?php echo date('d/m/y',strtotime( date('Y-m-d H:i:s',time())." +1 day" )); ?>)"></td>
                <td><strong><?php echo _('OR'); ?></strong></td>
                <td><input type="hidden" name="date_after_tomorrow" value="<?php echo date('Y-m-d',strtotime( date('Y-m-d H:i:s',time())." +2 day" )); ?>">
                  <input type="submit" name="day_after_tomorrow" value="<?php echo _('Day After Tomorrow'); ?> (<?php echo date('d/m/y',strtotime( date('Y-m-d H:i:s',time())." +2 day" )); ?>)"></td>
              </tr>
            </tbody>
          </table>
        </form>
         -->
      </div>
    </div>
    <div id="progress"><?php echo _("Creating Report");?> <img src="<?php echo base_url();?>assets/cp/images/20122139137.GIF" alt="..."/></div>
    <?php }else{?>
    <div class="go_back"><a href="<?php echo base_url();?>cp/seperate_orders_report"><?php echo _("Go back")?></a></div>
    <?php }?>
    

    <div class="box">
      <h3><?php echo _("Downloads");?></h3>
      <div id="order_report_div" class="table">
        <table cellspacing="0" width="100%" border="0" id="reports_list" <?php if(!isset($saved_reports)){?>style="display:none;"<?php }?>>
          <thead>
            <tr>
            	<th width="3%"><input type="checkbox" name="delete_all" id="delete_all" /></th>
            	<th width="15%"><?php echo _("Date");?></th>
            	<th width="20%"><?php echo _("Daterange");?></th>
            	<!-- <th width="15%"><?php echo _("Invoices");?></th>
            	<th width="10%"><?php echo _("Type");?></th> -->
            	<th width="27%"><?php echo _("File");?></th>
              <th><?php echo _("Report Type"); ?></th>
            	<th width="10%"><?php echo _("Action");?></th>
            </tr>
          </thead>
          <tbody>
          <?php if(isset($saved_reports) && !empty($saved_reports)) { ?>
            <?php foreach($saved_reports as $saved_report) { ?>
            <tr id="row_<?php echo $saved_report['id'];?>">
              <td width="3%" valign="top">
              	<input type="checkbox" name="delete[]" id="delete[]" value="<?php echo $saved_report['id'];?>" />
              </td>
              <td width="15%" valign="top" >
			  	<?php echo date( "d/m/y" ,strtotime($saved_report['date'])); ?>
              </td>
              <td width="20%" valign="top">
              	<?php echo date("d-m-Y", strtotime($saved_report['start_date']))." - ".date("d-m-Y",strtotime($saved_report['end_date'])); ?>
              </td>
              <!-- 
              <td width="15%" valign="top">
              	<?php
              		if($saved_report['invoice'] == "show_invoice")
              			echo _("Only Invoice");
              		elseif($saved_report['invoice'] == "show_without_invoice")
              			echo _("Without Invoice");
              		else
              			echo _("ALL");
              	?>
              </td>
              <td width="10%" valign="top">
              	<?php echo ($saved_report['type'] == 'full')?_("Full report"):_("Short report"); ?>
              </td>
               -->
              <td width="27%" valign="top">
              	<a href="<?php echo base_url();?>assets/pdf_reports/download.php?f=<?php echo $saved_report['report_name'];?>" ><?php echo $saved_report['report_name'];?></a> (<?php echo ($saved_report['size'] > (1024*1024) )?round($saved_report['size']/(1024*1024),2)." MB":round($saved_report['size']/1024,2)." KB"; ?>)
              </td>
              <td valign="top" width="13%">
                <?php echo _($saved_report['report_type']);?>
              </td>
              <td width="10%" valign="top">
              	<a href="javascript:void(0);" class="delete_single" onClick="delete_this('<?php echo $saved_report['id'];?>')" ><img width="16" height="16" border="0" alt="remove" src="<?php echo base_url();?>assets/cp/images/delete.gif" class="v_align_middle"></a>
              </td>
            </tr>
            <?php } ?>
          <?php }?>
          </tbody>
          <tfoot>
          	<tr>
          		<td colspan="7">
          			<input type="button" name="delete_btn" id="delete_btn" value="<?php echo _("DELETE")?>" onClick="delete_selected()" />
          		</td>
          	</tr>
          	<tr>
          		<td colspan="7">
          			<strong><?php echo _("Note");?>:</strong> <?php echo _("Please delete the report if you don't use them to keep the server clean");?>
          		</td>
          	</tr>
          </tfoot>
        </table>

      </div>
    </div>
	
    
  </div>
</div>
