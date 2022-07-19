<script type="text/javascript">
function process_date_format(date,ret_format,field_update)
{
   jQuery.post('<?php echo base_url()?>cp/orders/ordered_products',{'act':'format_date','date':date,'format':ret_format},function(date){
      jQuery('#'+field_update).val(date);
   });
}

function print_Popup(action_set, date_1, date_2, dont_show_remark)
{
   window.open("<?php echo base_url()?>cp/orders/print_ordered_products/"+dont_show_remark+"/"+date_1+"/"+date_2, "Print - Ordered Products ("+date_1+((date_2)?' - '+date_2:'')+")", "status = 1, height = 550, width = 700, resizable = 1, scrollbars = yes, left = 10, top = 50");
}
function set_remark_confirmation(){

	if($("#e_hide_remark").is(":checked")){
		$("#single_hide_remark").val("1");
	}else{
		$("#single_hide_remark").val("0");
	}

}
</script>
<script src="<?php echo base_url();?>assets/cp/new_js/jquery.dymo.sdk.min.js?version=<?php echo version;?>" type="text/javascript" charset="UTF-8"></script>

<script type="text/javascript" src="<?php echo base_url().'assets/cp/new_js/product_all.js';?>"></script>

<div id="main">

	<div id="main-header">
 		<h2><?php echo _('Report'); ?></h2>
      	<span class="breadcrumb"><a href="<?php echo base_url()?>cp/cdashboard"><?php echo _('Home'); ?></a> &raquo; <?php echo _('Report'); ?></span>
		<?php $messages = $this->messages->get();?>
		<?php if($messages != array()): foreach($messages as $type => $message):?>

			<?php if($type == 'success' && $message != array()):?>
				<br /><br />
				<div id="succeed"><strong><?php echo _('Succeed')?></strong>:<?php echo $message[0];?></div>
			<?php elseif($type == 'error' && $message != array()):?>
				<br /><br />
				<div id="error"><strong><?php echo _('Error')?></strong>:<?php echo $message[0];?></div>
			<?php endif;?>

		<?php endforeach; endif;?>
	</div>

	<div id="content">
    	<div id="content-container">
        	<div class="box">
          		<h3><?php echo _("Search Orders");?></h3>
				<div class="table">
				   <form name="frm_search" id="frm_search" action="<?php echo base_url()?>cp/orders/ordered_products" method="post" style="border-bottom:1px solid #E2E2E2;">
            			<table cellspacing="0" border="0" width="100%" cellpadding="0">
	            			<tbody>
	                			<tr>
	                  				<td width="16%"><strong><?php echo _('Print all ordered products from')?></strong></td>
	                  				<td valign="middle" align="justify" width="21%">
										<div style="float:left"><input type="text" class="text" readonly="readonly" name="e_start_date" id="e_start_date"><input type="hidden" name="start_date" id="start_date" onChange="process_date_format(this.value,'d-m-Y','e_start_date');"></div>
					  					<div style="float:left"><input type="button" value="..." onclick="displayCalendar(document.frm_search.start_date,'yyyy-mm-dd',this);" name="button1" id="button1"></div>
									</td>
									<td width="4%"> <strong><?php echo _('to'); ?></strong> </td>
	                   				<td valign="middle" align="justify" width="22%">
										<div style="float:left"><input type="text" class="text" readonly="readonly" name="e_end_date" id="e_end_date"/><input type="hidden" name="end_date" id="end_date" onChange="process_date_format(this.value,'d-m-Y','e_end_date');"></div>
										<div style="float:left"><input type="button" value="..." onclick="displayCalendar(document.frm_search.end_date,'yyyy-mm-dd',this);" name="button2" id="button2"/></div>
	                  				</td>
	                  				<td valign="middle" align="justify" width="22%">
										<div style="float:left"><input type="checkbox" name="e_hide_remark" id="e_hide_remark" value="1" onclick="set_remark_confirmation();" />  <?php echo _("Hide Remark");?></div>
	                  				</td>
	                 				 <td width="37%" valign="middle"><input type="submit" class="submit" value="<?php echo _('Search')?>" name="btn_search" id="btn_search"/>
	                    			<input type="button" class="submit" value="<?php echo _('Reset')?>" onclick="this.form.reset();" name="btn_reset" id="btn_resset"/>
	                    			<input type="hidden" value="get_products" name="act" id="act"/>
									</td>
	                			</tr >
	              			</tbody>
              			</table>
            		</form>
            		<script>
            		var frmvalidator = new Validator("frm_search");
						frmvalidator.EnableMsgsTogether();
						frmvalidator.addValidation("start_date","req","<?php echo _('Please enter start date')?>");
						frmvalidator.addValidation("end_date","req","<?php echo _('Please enter end date')?>");
            		</script>

					<form name="frm_set" id="frm_set" action="<?php echo base_url()?>cp/orders/ordered_products" method="post">
					<input type="hidden" name="single_hide_remark" id="single_hide_remark" value="0" />
					    <table cellspacing="0" border="0" width="90%" cellpadding="0">
							<tbody>
	                			<tr>
								   <td><strong><?php echo _('Or - Print all ordered products for')?></strong></td>
								   <td>
								     <input type="hidden" name="date_tomorrow" value="<?php echo date('Y-m-d',strtotime( date('Y-m-d H:i:s',time())." +1 day" )); ?>">
								     <input type="submit" name="tomorrow" value="<?php echo _('Tomorrow'); ?> (<?php echo date('d/m/y',strtotime( date('Y-m-d H:i:s',time())." +1 day" )); ?>)">
								   </td>
								   <td> <strong><?php echo _('OR'); ?></strong> </td>
								   <td>
								     <input type="hidden" name="date_after_tomorrow" value="<?php echo date('Y-m-d',strtotime( date('Y-m-d H:i:s',time())." +2 day" )); ?>">
								     <input type="submit" name="day_after_tomorrow" value="<?php echo _('Day After Tomorrow'); ?> (<?php echo date('d/m/y',strtotime( date('Y-m-d H:i:s',time())." +2 day" )); ?>)">
								   </td>
								</tr>
							</tbody>
						</table>
					</form>
				</div>
		    </div>

			<div class="box" id="obs_print_report_id" data-start="<?php if(isset($date_set_1)): echo $date_set_1;endif;?>" data-end="<?php if(isset($date_set_2)): echo $date_set_2;endif;?>">
          		<h3>
				  <?php echo _("Overview");?>
				  <?php if(isset($date_set_1) && isset($date_set_2) && $date_set_2 != '')
				  { ?>
				  (<?php echo _('From'); ?> : <?php echo date('d-m-Y',strtotime($date_set_1)); ?> - <?php echo _('To'); ?> : <?php echo date('d-m-Y',strtotime($date_set_2)); ?>)
				  <?php }elseif(isset($date_set_1) && !isset($date_set_2) && $date_set_1 != '') { ?>
				  (<?php echo _('Date'); ?> : <?php echo date('d-m-Y',strtotime($date_set_1)); ?>)
				  <?php } ?>
				</h3>
				<div class="table">
				<?php if(isset($products) && !empty($products)) { $total = 0; ?>
				<table cellspacing="0" border="0" id="prod_list">
				  <thead>
				    <tr>
					   <th><?php echo _('Product'); ?></th>
					   <th><?php echo _('Amount'); ?></th>
					   <th><?php echo _('Price'); ?></th>
					   <?php if(!$dontShowRemark){?>
					   <th width="30%"><?php echo _('All Comments'); ?></th>
					   <?php }?>
					   <th style="text-align:right;padding-right:50px"><?php echo _('SubTotal'); ?></th>
					   <th></th>
					</tr>
				  </thead>
				  <tbody>
				    <?php foreach($products as $p) { ?>
				    <tr>
					   <td><?php echo $p->proname; ?></td>
					   <?php
						    $qnty_unit = '';
						    $price_unit = '&nbsp;&euro;';
						    $price = 0;
					   		if($p->content_type == 1){
					   			$qnty_unit = ' gr.';
					   			$price_unit .= '/Kg';
					   			$price = $p->price_weight*1000;
					   		}elseif($p->content_type == 2){
					   			$qnty_unit = ' person';
								$price_unit .= '/'._('Person');
								$price = $p->price_per_person;
					   		}else{
					   			$price = $p->price_per_unit;
					   		}
					   ?>
					   <td><?php echo ($p->quantity).$qnty_unit; ?></td>
					   <td><?php echo defined_money_format($price).$price_unit; ?></td>
					   <?php if(!$dontShowRemark){?>
					   <td>
					       <?php if(is_array($p->pro_remark_arr) && !empty($p->pro_remark_arr)){ ?>
							   <?php foreach($p->pro_remark_arr as $remark){ ?>
							   &nbsp;-&nbsp;<?php echo $remark; ?><br />
							   <?php } ?>
					       <?php } ?>
					   </td>
					   <?php }?>
					   <td style="text-align:right;padding-right:50px">
					       <?php $total += ($p->total); ?>
					       <?php echo defined_money_format($p->total).'&nbsp;&euro;'; ?>
					   </td>
					   <?php
					   		if(!empty($date_set_1) && !empty($date_set_2))
					   		{
					   			$date_set_1_1=$date_set_1.'/'.$date_set_2.'/'.$p->products_id;
					   		}
					   		else
					   		{
					   			$date_set_1_1=$date_set_1.'/NULL/'.$p->products_id;
					   		}
					   ?>
					   <td width="10%">
					   <a class="print" href="javascript: void(0);"  onclick="print_labeler_product_report(<?php echo $p->products_id;?>,'label');"><img width="16" height="16" border="0" alt="label" class="v_align_middle" src="<?php echo base_url().'assets/cp/images/per_product.png'?>" title="Download Label"></a>
					   <a class="print" target="_blank" href="<?php echo base_url().'cp/orders/ordered_products_list/excel/'.$date_set_1_1; ?>" title="Download Excel" style="text-decoration: none;"><input type="button" value="Export"></a>
					   </td>

					</tr>
				    <?php } ?>
				    <tr>
					   <td colspan="<?php if(isset($dontShowRemark) && $dontShowRemark){ echo "3"; }else{ echo "4"; } ?>" style="text-align:right;padding-top:20px"><strong><?php echo _('Total'); ?> : </strong></td>
					   <td width="5%" style="text-align:right;padding-right:50px;padding-top:20px"><strong><?php echo money_format("%!^2n",round($total,2)).'&nbsp;&euro;'; ?></strong></td>
					</tr>
					<tr>
					   <td colspan="<?php if(isset($dontShowRemark) && $dontShowRemark){ echo "4"; }else{ echo "5"; } ?>" style="text-align:right;">
					   	 <input type="hidden" name="dont_show_remark" id="dont_show_remark" value="<?php if(isset($dontShowRemark) && $dontShowRemark){ echo "1"; }else{ echo "0"; } ?>" />
					     <input type="button" name="get_print_out" id="get_print_out" value="<?php echo _('Print Overview'); ?>" onClick="print_Popup('<?php echo $action_set; ?>','<?php echo $date_set_1; ?>','<?php echo $date_set_2; ?>','<?php if(isset($dontShowRemark) && $dontShowRemark){ echo "1"; }else{ echo "0"; } ?>');">
					   </td>

					</tr>
				  </tbody>
				</table>
				<?php }?>
				</div>
		    </div>
		</div>
	</div>
