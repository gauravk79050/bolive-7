
<div id="main">
	<div id="main-header">
 		<h2><?php echo _('Report'); ?></h2>
      	<span class="breadcrumb"><a href="<?php echo base_url()?>cp/cdashboard"><?php echo _('Home'); ?></a> &raquo; <?php echo _('Report'); ?></span>
		<?php $messages = $this->messages->get();?>
		<?php if($messages != array()): foreach($messages as $type => $message):?>

			<?php if($type == 'success' && $message != array()):?>
				<br /><br />
				<div id="succeed"><?php echo $message[0];?></div>
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
				   <form name="frm_search" id="frm_search" action="<?php echo base_url()?>cp/orders/ordered_products_per_supp" method="post" style="border-bottom:1px solid #E2E2E2;">
            			<table cellspacing="0" border="0" width="100%" cellpadding="0">
	            			<tbody>
	                			<tr>
	                  				<td width="16%"><strong><?php echo _('Print all ordered products per supplier from')?></strong></td>
	                  				<td valign="middle" align="justify" width="21%">
										<div style="float:left;width: 85%;"><input type="text" class="text" readonly="readonly" name="e_start_date" id="e_start_date"><input type="hidden" name="start_date" id="start_date" onChange="process_date_format(this.value,'d-m-Y','e_start_date');"></div>
					  					<div style="float:left"><input type="button" value="..." onclick="displayCalendar(document.frm_search.start_date,'yyyy-mm-dd',this);" name="button1" id="button1"></div>
									</td>
									<td width="4%"> <strong><?php echo _('to'); ?></strong> </td>
	                   				<td valign="middle" align="justify" width="22%">
										<div style="float:left;width: 85%;"><input type="text" class="text" readonly="readonly" name="e_end_date" id="e_end_date"/><input type="hidden" name="end_date" id="end_date" onChange="process_date_format(this.value,'d-m-Y','e_end_date');"></div>
										<div style="float:left"><input type="button" value="..." onclick="displayCalendar(document.frm_search.end_date,'yyyy-mm-dd',this);" name="button2" id="button2"/></div>
	                  				</td>
	                  				<td valign="middle" align="justify" width="22%">
										<div style="float:left"><input type="checkbox" name="e_hide_remark" id="e_hide_remark" value="1" />  <?php echo _("Hide Remark");?></div>
	                  				</td>
	                 				 <td width="37%" valign="middle"><input type="submit" class="submit" value="<?php echo _('Search')?>" name="btn_search" id="btn_search"/>
	                    			<input type="button" class="submit" value="<?php echo _('Reset')?>" onclick="this.form.reset();" name="btn_reset" id="btn_resset"/>
	                    			<input type="hidden" value="get_products" name="act" id="act"/>
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
				</div>
		    </div>

			<div class="box">
          		<h3>
				  <?php echo _("Overview");?>
				  <?php if(isset($date_set_1) && isset($date_set_2) && $date_set_2 != '') { ?>
				  (<?php echo _('From'); ?> : <?php echo date('d-m-Y',strtotime($date_set_1)); ?> - <?php echo _('To'); ?> : <?php echo date('d-m-Y',strtotime($date_set_2)); ?>)
				  <?php }elseif(isset($date_set_1) && isset($date_set_2) && $date_set_2 != '') { ?>
				  (<?php echo _('Date'); ?> : <?php echo date('d-m-Y',strtotime($date_set_1)); ?>)
				  <?php } ?>
				</h3>
				<div class="table">
				<?php if(isset($supp_products) && !empty($supp_products)) { $total = 0; ?>
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
					</tr>
				  </thead>
				  <tbody>
				  <?php foreach ($supp_products['supp_det'] as $s){?>
				  <tr>
				    <?php //monu ?>
				  	<td colspan="<?php if(isset($dontShowRemark) && $dontShowRemark){ echo "4"; }else{ echo "5"; } ?>">

				  	<?php if (stripslashes($s['rs_name']) == '-'){?>
				  		<strong><?php echo _('Own Products');?></strong>
				  	<?php }else{?>
				  		<strong><?php echo stripslashes($s['rs_name']);?></strong>
				  	<?php }?>
				  	</td>
				  	<?php //monu ?>
				  </tr>
				    <?php foreach($supp_products['prod_det'][$s['rs_id']] as $p) { ?>
				    <tr>
					   <td><?php echo stripslashes($p->proname); ?></td>
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
					</tr>
				    <?php } ?>
				    <?php }?>
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
	<script type="text/javascript" src="<?php echo base_url();?>assets/cp/new_js/ordered_supp_products_new.js"></script>