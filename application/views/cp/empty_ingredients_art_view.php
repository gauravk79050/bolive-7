<style>
.pws_art_box table label {
    display: inline-block;
    margin-top: 8px;
    max-width:89px;
	width:100%;
}
.pws_art_box_head td {
    background: rgba(0, 0, 0, 0) none repeat scroll 0 0 !important;
    border: 0 none !important;
    margin: 0 !important;
    padding-top: 10px;
}
.pws_art_box_head {
    background-color: #f1f1f1;
}
.pws_art_box_head input {
    width: 64%;
}
.pws_art_save {
    text-align: right;
    padding-bottom: 20px;
    padding-top: 20px;
}
.pws_art_save input[type="button"] {
    background-color: #d5d5d5;
    border: 1px solid #a5a5a5;
    padding: 3px 7px;
}
.pws_art_box > p {
    padding-bottom: 14px !important;
    padding-top: 14px !important;
}
</style>
<script type="text/javascript">
$(".producer").select2();
$(".supplier").select2();
</script>
<div class="pws_art_box">
<p><?php echo _("It's very important that you pass us the right article nbr from"); ?> <strong><?php echo $filtered_list['proname'];?></strong><?php echo '. ';?><?php echo _("We prefer to have the articlenbr of the producer,if not known you may pass us the one from the supplier");?></p>
<table cellspacing="0px">
	<thead class="pws_art_box_head">
		<tr>
			<td>
				<label for="Producer"><?php echo _('Producer').'/';?><?php echo _('Merk');?></label>
				<select style="max-width: 150px;" class="producer" onchange="show_new_producer(this,<?php echo $pro_id?>)" >
					<option value="0" >--<?php echo _('none')?>--</option>
				<?php foreach ($producers_list as $producers_list_key=>$producers_list_val){?>
					<option <?php if($filtered_list['fdd_producer_id'] == $producers_list_val['s_id']){echo 'selected="selected"';}?> value="<?php echo $producers_list_val['s_id'];?>"><?php echo stripslashes($producers_list_val['s_name']);?></option>
				<?php }?>
					<option value="-1"><?php echo _('Not found? add new'); ?></option>
				</select>
			</td>
			<td>
				<label for="Artnbr"><?php echo _('Art.nbr')?></label>
				<input type="text" class="text" id="fdd_prod_art_nbr" value="<?php echo $filtered_list['fdd_prod_art_num']?>">
			</td>
		</tr>
		<tr>
			<td>
				<label for="Supplier"><?php echo _('Supplier');?></label>
				<select style="max-width: 150px;" class="supplier" onchange="show_new_supplier(this,<?php echo $pro_id?>)" >
				   	<option value="0" >--<?php echo _('none')?>--</option>
				<?php foreach($supplier_list as $supplier_list_key=>$supplier_list_val){?>
					<option <?php if($filtered_list['fdd_supplier_id'] == $supplier_list_val['rs_id']){echo 'selected="selected"';}?> value="<?php echo $supplier_list_val['rs_id']?>"><?php echo stripslashes($supplier_list_val['rs_name'])?></option>
				<?php }?>
					<option value="-1"><?php echo _('Not found? add new'); ?></option>
				</select>
			</td>
			<td>
				<label for="Artnbr"><?php echo _('Art.nbr')?></label>
				<input type="text" class="text" id="fdd_supp_art_nbr" value="<?php echo $filtered_list['fdd_supp_art_num'];?>">
			</td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
			</td>
		
			<td class="pws_art_save">
				<input type="hidden" id="product_refused" name="product_refused" value="<?php echo $product_refused ?>">
				<input type="hidden" value="<?php echo $filtered_list['fdd_producer_id']?>" id="supplier_id">
				<input type="hidden" value="<?php echo $filtered_list['fdd_supplier_id']?>" id="real_supplier_id">
				<input type="hidden" value="<?php echo $filtered_list['fdd_prod_art_num']?>" id="old_fdd_prod_art_nbr">
				<input type="hidden" value="<?php echo $filtered_list['fdd_supp_art_num']?>" id="old_fdd_sup_art_nbr">
				

				<input type="button" value="<?php echo _('Save');?>" onclick="save_data(<?php echo $pro_id?>,this)">
				<input type="button" value="<?php echo _('Cancel');?>" onclick="close_box();">
			</td>
		</tr>
	</tbody>
</table>
</div>