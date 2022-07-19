<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="<?php echo base_url()?>assets/cp/new_css/style.css" rel="stylesheet" />
<link href="<?php echo base_url()?>assets/cp/new_css/table.css" rel="stylesheet" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo _('Print - Ordered Products'); ?> (<?php echo $date_set_1.($date_set_2?(' - '.$date_set_2):''); ?>)</title>
</head>
<body style="background:none;">
<?php if(isset($products) && !empty($products)) { $total = 0; ?>
<br />
<p style="text-align:center;">
   <strong><?php echo _('Ordered Products List'); ?></strong>
</p>
<p style="text-align:center;">
   <strong>
      <?php if($date_set_1 && $date_set_2) { ?>
      (<?php echo _('From'); ?> : <?php echo $date_set_1; ?> - <?php echo _('To'); ?> : <?php echo $date_set_2; ?>)
	  <?php }elseif($date_set_1 && !$date_set_2) { ?>
	  (<?php echo _('Date'); ?> : <?php echo $date_set_1; ?>)
	  <?php } ?>
   </strong>
</p>
<br />
<table cellspacing="0" border="0" id="prod_list">
  <thead>
	<tr>
	   <th><?php echo _('Product'); ?></th>
	   <th><?php echo _('Amount'); ?></th>
	   <th><?php echo _('Price'); ?></th>
	   <?php if(!$dont_show_remarks){?>
	   <th><?php echo _('All Comments'); ?></th>
	   <?php }?>
	   <th style="text-align:right;"><?php echo _('SubTotal'); ?></th>
	</tr>
  </thead>
  <tbody>
	<?php foreach($products as $p) { ?>
	<tr>
	   <td><?php echo $p->proname; ?></td>
	   <?php /*?><td><?php echo $p->counter; ?></td><?php */?>
	   <td><?php echo ($p->quantity).(($p->content_type)?' gr.':''); ?></td>
	   <td><?php $price = ($p->content_type)?(defined_money_format($p->price_weight*1000)):(defined_money_format($p->price_per_unit));  echo $price.($p->content_type?'&nbsp;&euro;/kg':'&nbsp;&euro;') ?></td>
	   <?php if(!$dont_show_remarks){?>
	   <td>
		   <?php if(is_array($p->pro_remark_arr) && !empty($p->pro_remark_arr)){ ?>
		   <?php foreach($p->pro_remark_arr as $remark){ ?>
		   &nbsp;-&nbsp;<?php echo $remark; ?><br />
		   <?php } ?>
		   <?php } ?>
	   </td>
	   <?php }?>
	   <td style="text-align:right;">
		   <?php $total += ($p->total); ?>
		   <?php echo defined_money_format($p->total).'&nbsp;&euro;'; ?>
	   </td>
	</tr>
	<?php } ?>
	<tr>
	   <td colspan="<?php if(isset($dont_show_remarks) && $dont_show_remarks){ echo "3"; }else{ echo "4"; } ?>" style="text-align:right;"><strong><?php echo _('Total'); ?> : </strong></td>
	   <td style="text-align:right;"><?php echo defined_money_format($total).'&euro;'; ?></td>
	</tr>
  </tbody>
</table>
<script type="text/javascript">
	window.print();
</script>
<?php } else { ?>
<strong><?php echo _('Sorry ! No records selected.'); ?></strong>
<?php } ?>
</body>
</html>