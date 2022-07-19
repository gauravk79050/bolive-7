<!-- MAIN -->
<div id="main">
	<div id="main-header">
    	<h2><?php echo _('Add FoodDESK product')?></h2>
    <div id="content">
    	<div id="content-container">
        	<div class="box">
          		<h3><?php echo _('Upload Productsheet')?></h3>
          		<div class="inside">
              		<form action="<?php echo base_url()?>cp/fooddesk/upload_pdf" enctype="multipart/form-data" method="post" id="change_password" name="change_password">
						
              			<div class="form-item">
                			<label><?php echo _('Product sheet');?></label>
                			<?php if($msg != ''){?>
                			<p style="background-color: #EEEEEE"><span><?php echo $msg;?></span></p>
                			<?php }?>
              				<input type="file" class="text long" id="pdf1" name="pdf1">
              				<input type="file" class="text long" id="pdf2" name="pdf2">
              				<input type="file" class="text long" id="pdf3" name="pdf3">
              				<input type="file" class="text long" id="pdf4" name="pdf4">
              				<input type="file" class="text long" id="pdf4" name="pdf5">
              				<p><span><?php echo _('Upload only pdf files. Uploaded productsheets will be verified by FoodDESK admin after that it will be available for use.');?></span></p>
              				<input type="submit" value="<?php echo _('Upload');?>" class="submit" id="btn_submit" name="btn_submit">
              				<input type="button" value="<?php echo _("Cancel");?>" class="submit" id="cncl_submit" onclick="window.location.assign('<?php echo base_url();?>cp/cdashboard/products')">
						</div>
						
					</form>			 
			  <p>&nbsp;</p>
          </div>
        </div>
      </div>
    </div>
    <!-- /content -->
