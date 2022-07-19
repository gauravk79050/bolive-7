<style>
	table td {
		padding: 20px 11px;
}
	.table div {
    margin-top: 15px;
}
#d_all_img > img {
    padding: 0px 0 0 10px;
    padding-right: 37px;
}

#d_all_img > textarea {
     display: inline-block;
    vertical-align: top;
    width: 72%;
}
#d_all_img > input {
    border: medium none;
    border-radius: 0;
    cursor: pointer;
    display: inline-block;
    margin-left: 10px;
    padding: 5px 15px;
    vertical-align: top;
}
</style>
<script>
jQuery('document').ready(function(){
	$('#tex_all_copy').on('click',function(){
		var type = $(this).attr('data-type');
		var copied_text = $('#'+type+'copy').val();
		var $temp = $("<input>");
		$("body").append($temp);
		$temp.val(copied_text).select();
		document.execCommand("copy");
		$temp.remove();
	});
	$('#tex_all_copy2').on('click',function(){
		var type = $(this).attr('data-type');
		var copied_text = $('#'+type+'copy2').val();
		var $temp = $("<input>");
		$("body").append($temp);
		$temp.val(copied_text).select();
		document.execCommand("copy");
		$temp.remove();
	});
});
</script>
<div id="main" style="text-transform:none;">
    <div id="main-header">
	   <h2><?php echo _('Downloads & Labels')?></h2>
    </div>
	<div id="content">
      <div id="content-container">

		 <div class="box">
			<h3><?php echo _('Downloads')?></h3>
			<div class="table">
                   <table  cellspacing="0" cellpadding="0" border="0">
			       <tbody>
			       	 <tr id="notice"><td id="notice_msg"  colspan='2'><?php echo _("The section has been added in the new Controlepanel(2.0)" ); ?><?php echo "<br>"; ?><?php echo _("Please proceed with new Controlepanel(2.0)")?><?php echo "<br>"; ?><a target="_blank" style="color: #8c8c00;text-decoration: none;" href="<?php echo $this->config->item('new_obs_url') ?>"><?php echo rtrim($this->config->item('new_obs_url'),'/') ?></a></td></tr>
                   	 <!--  <tr>
                         <td><?php echo _('FoodDESK Labelsoftware');?></td>
                         <td><?php echo _('Download and install this app to easily print labels from any labelprinter!');?><br><a href="<?php echo $this->config->item('fdd_url');?>apps/labelprinter.html" target="_blank"><?php echo _('More info');?></a></td>

                      </tr>
                      <tr>
                         <td><?php echo _('FoodDESK Mail2print');?></td>
                         <td>
                         <?php echo _('If you are using our webshop-mod then you can easily print an overview of every online order in realtime from any printer connected to your PC.');?>
                         <br>
                         <a href="javascript:;"><?php echo _('More info');?></a>
                         </td>
                      </tr>
                      <tr>
                         <td><?php echo _('FoodDESK Digi');?></td>
                         <td>
                         <?php echo _('Download and install this app to synchronize data between fooddesk and Win Digi.');?>
                         <br>
                         <a href="<?php echo $this->config->item('fdd_url');?>apps/digiApp/publish.htm" target="_blank"><?php echo _('More info');?></a>
                         </td>
                      </tr> -->
                   </tbody>
                   </table>
            </div>
		 </div>
		 <div class="box">
			<h3><?php echo _('Labels')?></h3>
			<div class="table">
                   <table id="" class="" cellspacing="0" cellpadding="0" border="0">
			       <tbody>
			       <tr id="notice"><td id="notice_msg"  colspan='2'><?php echo _("The section has been added in the new Controlepanel(2.0)" ); ?><?php echo "<br>"; ?><?php echo _("Please proceed with new Controlepanel(2.0)")?><?php echo "<br>"; ?><a target="_blank" style="color: #8c8c00;text-decoration: none;" href="<?php echo $this->config->item('new_obs_url') ?>"><?php echo rtrim($this->config->item('new_obs_url'),'/') ?></a></td></tr>
                   	 <!--  <tr>
                         <td width="40%">
	                         <?php echo _('Show to your clients that they can ask any question about allergens by putting one of our labels in your website! You can copy/paste this code into your webpage or just copy and send this code to your webdesigner.');?>
	                         <br>
	                         <div id="d_all_img" style="width: 100%">
	                         	<img src="http://www.fooddesk.be/allergenen-labels/allergenen-label-1.png">
	                         	<textarea rows="8" cols="10" id="allercopy" readonly="readonly"><a href="http://www.fooddesk.be" target="_blank"><img src="<?php echo $this->config->item('fdd_url');?>allergenen-labels/allergenen-label-1.png" alt="FOODDESK allergenen" width="142" height="142" /></a></textarea>
	                         	<input type="button" value="<?php echo _('Copy');?>" data-type="aller" id="tex_all_copy">
	                         </div>
	                         <br>
	                         <div id="d_all_img" style="width: 100%">
	                         	<img src="http://www.fooddesk.be/allergenen-labels/allergenen-label-2.png">
	                         	<textarea rows="8" cols="10" id="allercopy2" readonly="readonly"><a href="http://www.fooddesk.be" target="_blank"><img src="<?php echo $this->config->item('fdd_url');?>allergenen-labels/allergenen-label-2.png" alt="FOODDESK allergenen" width="175" height="61" /></a></textarea>
	                         	<input type="button" value="<?php echo _('Copy');?>" data-type="aller" id="tex_all_copy2">
	                         </div>
                         </td>
                      </tr> -->
                   </tbody>
                   </table>
            </div>
		 </div>
	  </div>
	 </div>