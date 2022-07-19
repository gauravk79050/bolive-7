<!-- <meta http-equiv="refresh" content="60" > -->
<script type="text/javascript">
<!-- -----------------function for auto refresh page --------------------- -->	
var timer = setTimeout(function() {
	  window.location = window.location;
	}, 60000);
	$(function(){
	  $("#btn_auto_load").click(function(e){
	    e.preventDefault();
	    clearTimeout(timer);
	  });
	});	
<!-- -----------------function that will check all the checkbox---------------------- -->	
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
<!-- --------------------------------------------------------------------------------- -->
	
<!-- -------------------function invoked when delete all is clicked--------------------- -->	
	function ValidateSelection(frm, id1,start_index,end_index){	
		var x=true;
		for(var i=parseInt(start_index);i<parseInt(end_index);i++){
			var id = id1 + i;
			//alert(id+'   '+document.getElementById(id).value);
			if(document.getElementById(id).checked){
				x=false;
				confirmDel(frm);
				break;
			}
		}
		if(x){
			alert("<?php echo _('Please select a record')?>");
		}	
		return false;	
	}

	function confirmDel(frm){
		if(confirm("<?php echo _('Do u really want to delete it')?>")){
			var delete_all=new Array();
			var arr=document.getElementsByName("del[]");
			
			var j=0;
			for(i=0;i<arr.length;i++){
				var obj=document.getElementsByName('del[]').item(i);
				if(obj.checked){
					delete_all[j]=obj.value;
					j++;
				}
			}
			order_ids=delete_all.toString();
			/*$.post('<?php echo base_url()?>cp/cdashboard/orders',{'act':'delete_order','delete_row':'all','order_ids':order_ids},            function(data){
				if(data=='success'){
					alert("<?php echo _('Selected orders has been deleted successfully')?>");
					window.location='<?php echo base_url()?>cp/cdashboard/orders';
				}else if(data=='error'){
					alert("<?php echo _('Some error occured')?>");
				}
			});*/
			
		}
	}
<!-- ---------------------------------------------------------------------- -->

<!-- -------------function invoked when print all is clicked--------------- -->
	function ValidateSelection1(frm, id1, start_index,end_index){	
		var x=true;
		for(var i=parseInt(start_index);i<parseInt(end_index);i++){
			var id = id1 + i;
			//alert(document.getElementById(id).value);
			if(document.getElementById(id).checked){
				x=false;
				confirmPrintAll(frm);
				break;
			}
		}
		if(x){
			alert("<?php echo _('Please select atleast one record')?>");
		}	
			return false;	
	}

	function confirmPrintAll(frm){
		if(confirm("<?php echo _('Do you want to print all records')?>")){
			var print_all = new Array();
			var arr = document.getElementsByName("del[]");
			var j =0;
			for(var i = 0; i < arr.length; i++){
            	var obj = document.getElementsByName("del[]").item(i);
				if(obj.checked){
					print_all[j] = obj.value;
					j++;
				}	
			}
			var order_ids=print_all.toString();
			/*$.post('<?php echo base_url()?>cp/cdashboard/orders',{'act':'print_order','print_count':'all','order_ids':order_ids},function(data){
				var my_window=window.open( "", "myWindow", "status = 1, height = 600, width = 550, resizable = 1, scrollbars=yes, left=10, top=100" );
				$(my_window.document).find("body").html(data);
			
			}); */
			
			<!--this will open a new window -->
			//window.open( "<?php echo base_url()?>cp/cdashboard/print_order_details?order_ids="+order_ids+"&print_count=all", "myWindow", "status = 1, height = 600, width = 550, resizable = 1, scrollbars=yes, left=10, top=100" );

		}
	}
<!-- ------------------------------------------------------------------------------- -->	
/*-------------function to show thick box when  client's name is clicked -----------*/
	function show_client_data(order_id){

		tb_show('Details','#TB_inline?height=290&width=400&inlineId=show_client_'+order_id,'');	

	}
	function show_purchase(orders_id){
		$.post('<?php echo base_url()?>cp/desk/show_purchase',{'orders_id':orders_id},function(data){
			$('#show_order_details').html(data);
			tb_show('Details','TB_inline?height=300&width=650&inlineId=show_order_details',null);
		});
	}
/*----------------------------------------------------------------------------------*/	
</script>
<!-- -----this section is for jquery pagination ------------------------------------- -->
<link rel="stylesheet" href="<?php echo base_url()?>assets/cp/new_js/pagination/pagination.css"/>
<script type="text/javascript" src="<?php echo base_url()?>assets/cp/new_js/pagination/jquery.pagination.js"></script>
<script type="text/javascript">
var members = new Array();
var dataLength = <?php echo count($orders);?>;

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
	//var max_elem = Math.min((page_index+1) * items_per_page, dataLength);
	var max_elem = Math.min(items_per_page, (dataLength -(items_per_page*(page_index))));
	var newcontent = '';

	var start_index = page_index*items_per_page;
	var end_index = max_elem;
	
	$.ajax({
		url: '<?php echo base_url();?>cp/desk/ajax_orders',
		type:'POST',
		dataType: 'json',
		data:{
				start:start_index,
				limit: end_index,
				start_date: $("#filtered_start_date").val(),
				end_date: $("#filtered_end_date").val()
			},
		success: function(members){
			//alert(members.toSource());
			//alert(members[0]); return false;
			newcontent += '<thead>\
					          <tr>\
					            <th><?php echo _('ID'); ?></th>\
					            <th><?php echo _('Counter'); ?></th>\
					            <th><?php echo _('Date'); ?></th>\
					            <th><?php echo _('Name'); ?></th>\
					            <th><?php echo _('Total'); ?></th>\
					            <th><?php echo _('Time'); ?></th>\
					            <th><?php echo _('Order Status'); ?></th>\
					            <th><?php echo _('Action'); ?></th>\
					         </tr>\
					       </thead>';

			for(var i=0;i<max_elem;i++)
			{
				newcontent += '<tr>\
			                 	<td>'+members[i][0]+'</td>\
			                    <td>'+members[i][1]+'</td>\
			                    <td>'+members[i][2]+'</td>\
			                    <td nowrap=\"nowrap\" width=\"70px\"><a onclick= \"show_client_data('+members[i][8]+')\" href=\"#\">'+members[i][6]+'</a>'+members[i][7]+'</td>\
			                    <td nowrap=\"nowrap\" width=\"70px\"><a onclick= \"show_purchase('+members[i][8]+')\" href=\"#\">'+members[i][4]+'&nbsp;&euro;</a></td>\
			                    <td>'+members[i][3]+'</td>\
			                    <td>'+members[i][5]+'</td>\
			                    <td>\
			                        <a href="<?php echo base_url(); ?>cp/desk/orders/edit/'+members[i][0]+'" title="<?php echo _('Edit Order'); ?>"> <img src="<?php echo base_url(); ?>assets/cp/images/edit.gif" /></a>&nbsp;|&nbsp;<a href="<?php echo base_url(); ?>cp/desk/orders/delete/'+members[i][0]+'" title="<?php echo _('Delete Order'); ?>"><img src="<?php echo base_url(); ?>assets/cp/images/delete.gif" height="16" width="16" /></a>\
			                    </td>\
			                 </tr>';
				
			}

			// Replace old content with new content
			$('#order_content').html(newcontent);
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

	opt['prev_text'] = 'Prev';

	opt['next_text'] = 'Next';

	return opt;

}

 $(document).ready(function(){

	var optInit =  $(document).ready(function(){
		var optInit = getOptionsFromForm();
		$("#Pagination").pagination(dataLength, optInit);
	});
	
    $("#Pagination").pagination(dataLength, optInit);
 });
</script>
<style>
.td_left{
	text-align:right;
	
	width:40%;
	padding:10px !important;
	vertical-align: top;
}
.td_right{
	text-align:left;
	
	width:60%;
	padding:10px !important;
}

.td_right a, .td_right a:visited{
	color:#0F4686 !important;
	text-decoration:underline;
}

.td_right a:hover{
	text-decoration:none;
}

.thickbox_footer{
	bottom: 0;
    padding: 5px;
   	background-color:#f0f0f0;
    text-align: center;
    width: 98%;

}
.thickbox_footer_text{
	width:172px;
	height:30px;
	background-color:#0F4686;
	border-radius:5px 5px 5px 5px;
	margin:0 auto;
}

.thickbox_footer_text a {
    color: #FFFFFF !important;
    line-height: 30px;
    margin: 0 auto !important;
    text-decoration: none;
}
.textkleiner {
	color:#909090;
	font-size:11px;
}
.orders_tab{

}
.orders_tab ul{
	margin-left: 1px;
}
.orders_tab ul li{
	float:left; 
	padding: 10px 20px;
	background-color: #EEEEEE;
}
.orders_tab ul li.select{
	background-color: #2D6CB1;
	color: #FFFFFF;
}
.orders_tab ul li.select a{
	color: #FFFFFF;
}
.box{
	border-top: 0 none;
}
</style>

<div id="main">
	<div id="main-header">
 		<h2><?php echo _('Orders'); ?></h2>
      	<span class="breadcrumb"><a href="<?php echo base_url()?>cp/cdashboard"><?php echo _('Home'); ?></a> &raquo; <?php echo _('Orders'); ?></span>	<?php $messages = $this->messages->get();?>
		<?php if($messages != array()): foreach($messages as $type => $message):?>
			
			<?php if($type == 'success' && $message != array()):?>
				<div id="succeed"><strong><?php echo _('Succeed')?></strong>:<?php echo $message[0];?></div>
			<?php elseif($type == 'error' && $message != array()):?>	
				<div id="error"><strong><?php echo _('Error')?></strong>:<?php echo $message[0];?></div>	
			<?php endif;?>
		<?php endforeach; endif;?>
	</div>
	<div class="orders_tab">
		<ul>
			<li>
				<a href="<?php echo base_url();?>cp/orders"><?php echo _("Order via website");?> (<?php echo $obs_orders;?>)</a>
			</li>
			<li class="select">
				<a href="<?php echo base_url();?>cp/desk/orders"><?php echo _("Order via OBSdesk");?> (<?php echo count($orders);?>)</a>
			</li>
			<?php if($obs_pending_orders){?>
			<li>
				<a href="<?php echo base_url();?>cp/orders/lijst/pending"><?php echo _("Pending Orders");?> (<?php echo $obs_pending_orders;?> )</a>
			</li>
			<?php }?>
			<?php if($obs_cancelled_orders){?>
			<li>
				<a href="<?php echo base_url();?>cp/orders/lijst/cancelled"><?php echo _("Cancelled Orders");?> (<?php echo $obs_cancelled_orders;?> )</a>
			</li>
			<?php }?>
		</ul>
	</div>
	<?php if($this->session->userdata('login_via') == 'mcp'){?>
	<input type="button" style="margin-left:202px; width: 100px; height: 30px;" class="submit" id="btn_auto_load" value="<?php echo _('Stop Auto Load')?>" name="btn_autoload"/>
	<?php }?>
	<div class="clear"></div>
	<div id="content">
	
	 <?php $this->messages->display_messages();  ?>
	 
    	<div id="content-container">
    		<?php if($this->company->obsdesk_status) { ?>
			
        	<div class="box">
          		<h3><?php echo _("Search Orders");?></h3>
				<div class="table">
					<input type="hidden" id="filtered_start_date" name="filtered_start_date" value="<?php if(isset($start_date)){ echo $start_date;}?>" />
					<input type="hidden" id="filtered_end_date" name="filtered_end_date" value="<?php if(isset($end_date)){ echo $end_date;}?>" />
					
            		<form name="frm_search" id="frm_search" action="<?php echo base_url()?>cp/desk/orders" method="post">
            			<table cellspacing="0" border="0" width="90%" cellpadding="0">
	            			<tbody>
	                			<tr>
	                  				<td colspan="2" width="22%"><strong><?php echo _('Display all orders between')?></strong></td>
	                  				<td valign="bottom" align="justify" width="30%">
										<div style="float:left"><input type="text" class="text" readonly="readonly" name="start_date" id="start_date"></div>
					  					<div style="float:left"><input type="button" value="..." onclick="displayCalendar(document.frm_search.start_date,'dd/mm/yyyy',this)" name="button1" id="button1"></div>
									</td>
	                   				<td valign="bottom" align="justify" width="30%">
										<div style="float:left"><input type="text" class="text" readonly="readonly" name="end_date" id="end_date"/></div>
										<div style="float:left"><input type="button" value="..." onclick="displayCalendar(document.frm_search.end_date,'dd/mm/yyyy',this)" name="button2" id="button2"/></div>
	                  				</td>
	                 				 <td valign="middle" width="20%"><input type="submit" class="submit" value="<?php echo _('Search')?>" name="btn_search" id="btn_search"/>
		                    			<input type="button" class="submit" value="<?php echo _('Reset')?>" onclick="this.form.reset();" name="btn_reset" id="btn_resset"/>
		                    			<input type="hidden" value="do_filter" name="act" id="act"/>
		                    			<input type="hidden" value="orders" name="view" id="view"/>
									</td>
	                  				<td width="0%">&nbsp;</td>
	                			</tr>
	              			</tbody>
	              		</table>
            		</form>
            		<script language="JavaScript" type="text/javascript">
						var frmvalidator = new Validator("frm_search");
						frmvalidator.EnableMsgsTogether();
						frmvalidator.addValidation("start_date","req","<?php echo _('Please enter start date')?>");
						frmvalidator.addValidation("end_date","req","<?php echo _('Please enter end date')?>");
					</script>
          		</div><!------END OF TABLE DIV------>
        	</div><!--------END OF BOX DIV--------->
		
			<div class="box">
				<h3><?php echo _("Orders Information")?></h3>
				<div class="table">
					<form name="frm_delete_all" id="frm_delete_all">
						<table cellspacing="0" border="0" id="order_content">
						<!--jquery pagination will add content in this table-->
							<tbody>
							</tbody>
						</table>	

					<!------------------this div will show the order details in a thickbox--------------->
								<div id="show_order_details" style="display:none"></div>
					<!---------------------------------------------------------------------------------->
					<input type="hidden" value="multiple_data" name="act" id="act"/>
					</form>
					<div id="Pagination">
					</div>
					<div style="background: none repeat scroll 0 0 #FAFAFA;border-top: 1px solid #E3E3E3;padding: 5px 10px;">
					  <a href="<?php echo base_url()?>cp/orders/ordered_products"><?php echo _('Print Report'); ?></a>
					  <br />
					  <br />
					  <br />
					  <br />
					  
                  	</div>
			    	<div style= "clear:both">
				  	</div>
				</div>
			</div>
			<?php }else{?>
			
			<div class="box">
          		<h3><?php echo _("Desk is not activated");?></h3>
				<div class="table" style="background-color: #ffffff; padding: 15px 22px 0;">
					<div class="desk_info_left">
						<h2><u><?php echo _("OBSdesk-Info")?></u></h2>
						
						<p><?php echo _("Ever wanted a infocenter with touchscreen in your shop where your visitor can search for products or make orders this way? Well... we have it!");?></p>
						
						<p><?php echo _("Here are some interesting features");?>:</p>
						
						
						<ul>
							<li><?php echo _("Infocenter is linked with the same database of your webshop which means if you change something in your Controlpanel(right here), it will be updated automatically on your OBSdesk.");?></li>
							<li><?php echo _("You can use it as plane infocenter(price hidden) or you can activate the order system (people can order and checkout just the same way as your webshop... but then 'offline'.)");?></li>
							<li><?php echo _("Client can printout an overview of your products");?></li>
							<li><?php echo _("Layout is fully brandable, even the bg solor and colors of button are adjustable");?></li>
						</ul>
						
						
						<p><?php echo _("Want a DEMO or more info? Contact us at 0473/250528");?></p>
						
					</div>
					<div class="desk_info_right">
						<img src="<?php echo base_url();?>assets/cp/images/ingo_machine.png" width="100%" height="300px"  />
					</div>
					<div style="clear:both;"></div>
          		</div><!------END OF TABLE DIV------>
        	</div><!--------END OF BOX DIV--------->
        	
			<?php }?>
		</div>
	</div>  