<style type="text/css">
#sidebar, #sidebarSet{
	display:none;
}
</style>

<div id="main" style="text-transform:none;">		
    <div id="main-header">
	   <h2><?php echo _('FTP SETTINGS')?></h2>
    </div>
	
	<?php $messages = $this->messages->get();?>
	<?php if($messages != array()):?>
		<?php foreach($messages as $key => $val):?>
			<?php foreach($val as $v):?>
				<div  class = "<?php echo $key;?>"><strong><?php echo ucfirst($key);?> : </strong><?php echo $v;?></div>
			<?php endforeach;?>	
		<?php endforeach;?>
    <?php endif;?>
	
	<div id="content" style="width: 100%;">
      <div id="content-container">
	     
		 <div class="box">
			<h3><?php echo _('Settings')?></h3>
			<div class="table">
               
                <form method="post" action="">
                   <table id="ftp-settings" cellspacing="0" cellpadding="0" border="0">
			       <tbody>
                   
                   	  <tr>
                         <td><?php echo _('OBS-Shop'); ?> :</td>
                         <td><input type="text" name="obs_shop" id="obs_shop" value="<?php echo @$company->obs_shop; ?>" style="width: 500px; height: 25px;" />
                             <br />
                             <small><?php echo _('Give the shop url. Eg - http://www.onlinebestelsysteem.net/DEMO/demo.html'); ?></small>
                         </td>
                      </tr>
                      
				      <tr>
                         <td><?php echo _('Shop URL'); ?> :</td>
                         <td><input type="text" name="shop_url" id="shop_url" value="<?php echo @$company->shop_url; ?>" style="width: 500px; height: 25px;" />
                             <br />
                             <small><?php echo _('Give the online-bestellen folder url. Eg - http://www.onlinebestelsysteem.net/DEMO'); ?></small>
                         </td>
                      </tr>  
                      <tr>
                         <td><?php echo _('Client File Location'); ?> :</td>
                         <td><input type="text" name="shop_files_loc" id="shop_files_loc" value="<?php echo @$company->shop_files_loc; ?>" style="width: 500px; height: 25px;" />
                             <br />
                             <small><?php echo _('Give the online-bestellen folder location on your server from root, including the client-files folder name.'); ?></small>
                         </td>
                      </tr>                    
                      <tr>
                         <td><?php echo _('FTP Hostname'); ?> :</td>
                         <td><input type="text" name="ftp_hostname" id="ftp_hostname" value="<?php echo @$company->ftp_hostname; ?>" style="height: 25px;" /></td>
                      </tr>
                      <tr>
                         <td><?php echo _('FTP Username'); ?> :</td>
                         <td><input type="text" name="ftp_username" id="ftp_username" value="<?php echo @$company->ftp_username; ?>" style="height: 25px;" /></td>
                      </tr>
                      <tr>
                         <td><?php echo _('FTP Password'); ?> :</td>
                         <td><input type="text" name="ftp_password" id="ftp_password" value="<?php echo @$company->ftp_password; ?>" style="height: 25px;" /></td>
                      </tr>                      
                      <tr>
                         <td><?php echo _('Access Permission'); ?> :</td>
                         <td>
                             <input type="checkbox" name="access_permission" id="access_permission" value="1" <?php if(@$company->access_permission) { echo 'checked="checked"'; } ?>/>
                             &nbsp;<?php echo _('Allow OBS Administrator to upload (or replace) online-bestellen client files to your FTP server automatically.'); ?>
                         </td>
                      </tr>
                      
                      <tr>
                         <td></td>
                         <td><input type="submit" name="submit" id="submit" value="<?php echo _('Save FTP Settings'); ?>" /></td>
                      </tr>
                      
                   </tbody>
                   </table>
                </form>
               
            </div>
		 </div>
    <!--  start -->

    <div class="box">
          <h3><?php echo _('Codes for your Website')?></h3>
          <div class="table">
            <?php if($api_codes):?>
      <table border="0" cellpadding="0" cellspacing="0" width="100%">
              <tbody>
                <tr>
                  <td class="wd_text" height="50" width="20%"><?php echo _('API Id')?>:</td>
                  <td><?php echo $api_codes[0]->api_id;?></td>
                </tr>
                <tr>
                  <td class="wd_text" height="50"><?php echo _('API Secret')?>:</td>
                  <td><?php echo $api_codes[0]->api_secret;?></td>
                </tr>
                <tr>
                  <td class="wd_text" height="50"><?php echo _('Domain')?>:</td>
                  <td><form id="upd_domain_form" name="upd_domain_form" action="" method="POST"><input name="act" id="act" value="upd_domain" type="hidden"><input type="text" class="text medium" name="domain" id="domain" value="<?php echo $api_codes[0]->domain;?>" /> <input type="submit" name="upd" id="upd" value="<?php echo _("UPDATE");?>" /></form></td>
                </tr>
          
               </tbody>
      </table>
      <?php else:?>
      <table border="0" cellpadding="0" cellspacing="0" width="100%"><tbody><tr>
        <td><?php echo _('Your Api Has Not Been Generated Yet ');?>!!</td>
      </tr></tbody></table>
      <?php endif;?>
          </div>
        </div>

     <!-- end -->
		 
	  </div>