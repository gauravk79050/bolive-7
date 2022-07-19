<!-- <meta http-equiv="refresh" content="60" > -->
<script type="text/javascript">
var timer = setTimeout(function() {
	  window.location = window.location;
	}, 60000);
	$(function(){
	  $("#btn_auto_load").click(function(e){
	    e.preventDefault();
	    clearTimeout(timer);
	  });
	});
	var company_role = "<?php echo $company_role;?>";

	// function that will check all the checkbox	
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
	
	// function invoked when delete all is clicked	
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

	// Function to delete multi orders
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
			$.post('<?php echo base_url()?>cp/orders/lijst/cancelled',{'act':'delete_order','delete_row':'all','order_ids':order_ids},            function(data){
				if(data.trim()=='success'){
					alert("<?php echo _('Selected orders has been deleted successfully')?>");
					window.location='<?php echo base_url()?>cp/orders/lijst/cancelled';
				}else if(data.trim()=='error'){
					alert("<?php echo _('Some error occured')?>");
				}
			});
			
		}
	}

	// function will show div that contains the purchases
	function show_purchases(orders_id){
		$.post('<?php echo base_url()?>cp/orders/lijst/cancelled',{'act':'show_order_details','orders_id':orders_id},function(data){
			console.log(data);
			$('#show_order_details').html(data);
			tb_show('Details','TB_inline?height=300&width=650&inlineId=show_order_details',null);
		});
	}

	// function to show thick box when  client's name is clicked 
	function show_client_data(order_id){

		tb_show('Details','#TB_inline?height=290&width=400&inlineId=show_client_'+order_id,'');	

	}

	// Function to mark any order as paid successfully
	function mark_paid(order_id){
		if(confirm("<?php echo _('Do u really want to mark this order as Paid ?')?>")){
			
			$.post('<?php echo base_url()?>cp/orders/lijst/cancelled',{'act':'update_order','order_id':order_id},
					function(data){
					if(data.trim()=='success'){
						alert("<?php echo _('Selected orders has been marked paid successfully')?>");
						window.location='<?php echo base_url()?>cp/orders/lijst/cancelled';
					}else if(data.trim()=='error'){
						alert("<?php echo _('Some error occured')?>");
					}
			});
			
		}
	}

	// Function for cancelled order
	function sendmail(order_id,obj){
			$.post(
					'<?php echo base_url()?>cp/orders/lijst/cancelled',
					{'act':'cancel_order','order_id':order_id},
					function(data){
					if(data.trim()=='success'){
						$(obj).parent().next('td').children().attr('checked', true);
						alert("<?php echo _('mail sent successfully')?>");
					}else if(data.trim()=='error'){
						$(obj).parent().next('td').children().attr('checked', false);
						alert("<?php echo _('Some error occured')?>");
					}
			});
	}
</script>
<!-- -----this section is for jquery pagination ------------------------------------- -->
<link rel="stylesheet" href="<?php echo base_url()?>assets/cp/new_js/pagination/pagination.css"/>
<script type="text/javascript" src="<?php echo base_url()?>assets/cp/new_js/pagination/jquery.pagination.js"></script>
<script type="text/javascript">
var members = new Array();
var dataLength = <?php echo $cancelled_orders;?>;

function urldecode(str) 
{
     if(str)
	 return unescape(str.replace(/\+/g, " "));
	 else
	 return '';
}

/* -------------------- FUNCTION TO STRIP SLASHES ------------------------------------------ */
function stripslashes(str) {
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

function pageselectCallback(page_index, jq){
	// Get number of elements per pagionation page from form
	var items_per_page = 10;
	//var max_elem = Math.min((page_index+1) * items_per_page, dataLength);
	var max_elem = Math.min(items_per_page, (dataLength -(items_per_page*(page_index))));
	var newcontent = '';

	var start_index = page_index*items_per_page;
	var end_index = max_elem;

	$.ajax({
		url: '<?php echo base_url();?>cp/orders/ajax_cancelled_order',
		type:'POST',
		dataType: 'json',
		data:{
				start:start_index,
				limit: end_index
			},
		success: function(members){
			
			newcontent += '<thead>';
			newcontent += '<tr>';
			newcontent += '<th width=\"1%\"><input type=\"checkbox\" values=\"all\" onClick=\"select_all(\'check_all\',\'chk\','+start_index+','+(start_index+end_index)+');\" name =\"check_all\" id=\"check_all\"/></th>';
			newcontent += '<th width=\"3%\"><?php echo _('Date')?></th>';
			newcontent += '<th width="120px"><?php echo _('Name')?></th>';
			newcontent += '<th><?php echo _("Total")?></th>';
			newcontent += '<th><?php echo _("Take Away")?></th>';
			newcontent += '<th width="80px"><?php echo _("Delivery")?></th>';
			// newcontent += '<th width="100px" style="text-align:left"><?php echo _("Send")?></th>';
			// newcontent += '<th><?php echo _("Order Status")?></th>';
			if(company_role == "super"){
				newcontent += '<th><?php echo _("Shop")?></th>';
			}
			newcontent += '<th>&nbsp;</th>';
			newcontent += '<th><?php echo _("Action")?></th>';
			newcontent += '<th></th>';
			newcontent += '<th></th>';
			/*if(labeler_activated)
				newcontent += '<th>&nbsp;</th>';*/
			newcontent += '</tr>';
			newcontent += '</thead>';
			for(var i=0;i<members.length;i++)
			{
				newcontent += '<tr class=\"'+members[i][8]+'\">';
				newcontent += '<td width=\"2%\" style=\"padding:0 10px;\"><input type="checkbox" value=\"'+members[i][0]+'\" name=\"del[]\" id=\"chk'+(i+start_index)+'\" /></td>';
				newcontent += '<td nowrap=\"nowrap\">'+members[i][1]+'</td>';
				newcontent += '<td nowrap=\"nowrap\" width=\"70px\"><a onclick= \"show_client_data('+members[i][0]+')\" href=\"#\">'+stripslashes( urldecode( decodeURIComponent( members[i][2] ) ) )+'</a>&nbsp;'+((members[i][11]=='1')?'<img src=\"<?php echo base_url();?>assets/cp/images/red_dot.gif\"  width=\"5px\" title=\"<?php echo _('New Client Ordered'); ?>\">':'')+''+stripslashes( members[i][14] )+'</td>';
				newcontent += '<td nowrap=\"nowrap\"><a href=\"javascript:void(0);\" onclick=\"show_purchases('+members[i][0]+')\">'+members[i][3]+'&nbsp;&euro;</a>'+urldecode(members[i][9])+'</td>';
				newcontent += '<td nowrap=\"nowrap\" width=\"90px\">'+urldecode(members[i][4])+'</td>';
				newcontent += '<td nowrap=\"nowrap\" width=\"120px\">'+stripslashes( urldecode(members[i][5]) )+'</td>';
				// newcontent += '<td width=\"60px\" nowrap=\"nowrap\">'+urldecode(members[i][6])+'</td>';
				// newcontent += '<td nowrap=\"nowrap\">'+urldecode(members[i][7])+'</td>';
				if(company_role == "super"){
					newcontent += '<td nowrap=\"nowrap\">'+members[i][12]+'</td>';
				}
				newcontent += '<td nowrap=\"nowrap\">&nbsp;';
				if(members[i][10] == 'subscribe')
				  newcontent += '<img width=\"16\" height=\"16\" border=\"0\" alt=\"Factuur\" src=\"<?php echo base_url();?>assets/cp/images/checked_invoice_64.gif\">';
				newcontent += '</td>';
				
				newcontent += '<td nowrap=\"nowrap\">';
				newcontent += '<a href=\"javascript:void(0);\" onclick=\"mark_paid('+members[i][0]+')\"><?php echo _("Mark as Paid")?></a>'+((members[i][15])?'<?php echo ' ('._("Date passed").')'; ?>':' ');
				newcontent += '</td>';
				newcontent += '<td nowrap=\"nowrap\"><input type="button" value="<?php echo _("Send Mail")?>" onclick="sendmail('+members[i][0]+',this)"></td>';
				var status;
				if(members[i][16] == 1)
				{
					status='checked="checked"';
				}
				else{
					status='';
				}
				newcontent += '<td nowrap=\"nowrap\"><input type="checkbox" name="email_checkbox" '+status+'></td>';
				newcontent += '</tr>';
				//j++;
			}

				newcontent += '<tr>';
				newcontent += '<td colspan=\"2\" style=\"color:#FF0000; font-weight:bold\">';
				newcontent += '<input type=\"button\" class=\"button\" value=\"<?php echo _("Delete")?>\" title=\"Delete\", onclick=\"return ValidateSelection(this.form,\'chk\','+start_index+','+(start_index+max_elem)+'\)\" name=\"button\" id=\"button\"/>';
				newcontent += '</td>';
				// newcontent += '<td colspan=\"9\" style=\"color:#FF0000; font-weight:bold\">';
				// newcontent += '<input type=\"button\" class=\"button\" value=\"<?php echo _("Print All")?>\" title=\"print all\" onclick=\"return ValidateSelection1(this.form,\'chk\', '+start_index+','+(start_index+max_elem)+')\" name=\"button\" id=\"button\"/></td>';															   
				newcontent += '</tr>';
			
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

	var optInit = getOptionsFromForm();	
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
 		<h2><?php echo _('Cancelled Orders'); ?></h2>
      	<span class="breadcrumb"><a href="<?php echo base_url()?>cp/cdashboard"><?php echo _('Home'); ?></a> &raquo; <?php echo _('Cancelled Orders'); ?></span>	<?php $messages = $this->messages->get();?>
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
				<a href="<?php echo base_url();?>cp/orders"><?php echo _("Order via website");?> (<?php echo $orders;?>)</a>
			</li>
			<li>
				<a href="<?php echo base_url();?>cp/desk/orders"><?php echo _("Order via OBSdesk");?> (<?php echo count($desk_orders);?> )</a>
			</li>
			<?php if($pending_orders){?>
			<li>
				<a href="<?php echo base_url();?>cp/orders/lijst/pending"><?php echo _("Pending Orders");?> (<?php echo $pending_orders;?> )</a>
			</li>
			<?php }?>
			<?php if($cancelled_orders){?>
			<li class="select">
				<a href="<?php echo base_url();?>cp/orders/lijst/cancelled"><?php echo _("Cancelled Orders");?> (<?php echo $cancelled_orders;?> )</a>
			</li>
			<?php }?>
		</ul>
	</div>
	<?php if($this->session->userdata('login_via') == 'mcp'){?>
		<input type="button" style="margin-left:200px;width: 100px; height: 30px;" class="submit" id="btn_auto_load" value="<?php echo _('Stop Auto Load')?>" name="btn_autoload"/>
	<?php }?>
	<div class="clear"></div>
	
	<div id="content">
    	<div id="content-container">
			<div class="box">
				<h3><?php echo _("Orders Information")?></h3>
				<div class="table">
					<form name="frm_delete_all" id="frm_delete_all">
						<table cellspacing="0" border="0" id="order_content">
						<!--jquery pagination will add content in this table-->
							<tbody></tbody>
						</table>	

						<!-- >>>>>>>>>>>>>> this div will show the order details in a thickbox <<<<<<<<<<< -->
						<div id="show_order_details" style="display:none"></div>
						<!-- >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< -->
						
						<input type="hidden" value="multiple_data" name="act" id="act"/>
					</form>
					<div id="Pagination"></div>
				</div>
			</div>
		</div>
	</div>  