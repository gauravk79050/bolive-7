<!-- <meta http-equiv="refresh" content="60" > -->
<link rel="stylesheet" href="<?php echo base_url()?>assets/cp/new_js/pagination/pagination.css"/>

<link rel="stylesheet" href="<?php echo base_url()?>assets/cp/new_css/pending_orders_new.css"/>

<script type="text/javascript" src="<?php echo base_url()?>assets/cp/new_js/pagination/jquery.pagination.js?version=<?php echo version;?>">
</script>

<script type="text/javascript">

<!-- -----this section is for jquery pagination ------------------------------------- -->

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


</script>

<script>
	var msg1="<?php echo _('Please select a record')?>";
	var msg2="<?php echo _('Do u really want to delete it')?>";
	var msg3="<?php echo _('Selected orders has been deleted successfully')?>";
	var msg4="<?php echo _('Some error occured')?>";
	var msg5="<?php echo _('Do u really want to mark this order as Paid ?')?>";
	var msg6="<?php echo _('Selected orders has been marked paid successfully')?>";
	var msg7="<?php echo _('Some error occured')?>";
	var msg8="<?php echo _('Date')?>";
	var msg9="<?php echo _('Name')?>";
	var msg10="<?php echo _("Total")?>";
	var msg11="<?php echo _("Take Away")?>";
	var msg12="<?php echo _("Delivery")?>";
	var msg13="<?php echo _("Shop")?>";
	var msg14="<?php echo _("Action")?>";
	var msg15="<?php echo _('New Client Ordered'); ?>";
	var msg16="<?php echo _("Mark as Paid")?>";
	var msg17="<?php echo _("Delete")?>";
	
</script>

<script type="text/javascript">
var members = new Array();
var dataLength = <?php echo $pending_orders;?>;
</script>




<div id="main">
	<div id="main-header">
 		<h2><?php echo _('Pending Orders'); ?></h2>
      	<span class="breadcrumb"><a href="<?php echo base_url()?>cp/cdashboard"><?php echo _('Home'); ?></a> &raquo; <?php echo _('Pendong Orders'); ?></span>	<?php $messages = $this->messages->get();?>
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
			<li class="select">
				<a href="<?php echo base_url();?>cp/orders/lijst/pending"><?php echo _("Pending Orders");?> (<?php echo $pending_orders;?> )</a>
			</li>
			<?php }?>
			<?php if($cancelled_orders){?>
			<li>
				<a href="<?php echo base_url();?>cp/orders/lijst/cancelled"><?php echo _("Cancelled Orders");?> (<?php echo $cancelled_orders;?> )</a>
			</li>
			<?php }?>
		</ul>
	</div>
	<?php if($this->session->userdata('login_via') == 'mcp'){?>
	<input type="button" style="margin-left:200px; width: 100px; height: 30px;" class="submit" id="btn_auto_load" value="<?php echo _('Stop Auto Load')?>" name="btn_autoload"/>
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
	<script type="text/javascript" src="<?php echo base_url();?>assets/cp/new_js/pending_orders_new.js"></script>