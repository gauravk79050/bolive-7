<div id="content">
    <div id="main-header">
    	<h2><?php echo _('Merchant Info')?>  </h2>
	</div>
	<div id="content-container">
      <div class="box">
	   <h3><?php echo _("Account Details"); ?></h3>
        <div class="table merchant_create">
        	<form method="POST" action="<?php echo base_url();?>cp/payment/payment_method" name="submit" enctype="multipart/form-data">
				
				<table cellspacing="0" class="override">
              		<tbody>
              			<tr>
                  			<td class="textlabel"><?php echo _("Merchant Name"); ?></td>
                  			<td><input type="text" class="text short" name="merchant_name" required /></td>
               			</tr>
               			<tr>
                  			<td class="textlabel"><?php echo _("Curo ID"); ?></td>
                  			<td><input type="text" class="text short" name="curo_id"  required/></td>
               			</tr>
               		
               			<tr>
                  			<td class="textlabel"><?php echo _("Secret Key"); ?></td>
                  			<td><input type="password" class="text short" name="secret_key" required/></td>
               			</tr>
               			<tr>
                  			<td class="textlabel"><?php echo _("Username"); ?></td>
                  			<td><input type="text" class="text short" name="username" required /></td>
               			</tr>
               			<tr>
                  		<td class="textlabel"><?php echo _("Password"); ?></td>
                  			<td><input type="passowrd" class="text short" name="pwd" required /></td>
               			</tr>
               			<tr>
                  			<td class="textlabel"><?php echo _("Site Id"); ?></td>
                  			<td> <input type="text" class="text short" name="site_id" required /></td>
               			</tr>
               			<tr>
                  			<td class="textlabel"><?php echo _("Site Name"); ?></td>
                  			<td> <input type="text" class="text short" name="site_name" required /></td>
               			</tr>
               			<tr>
                  			<td class="textlabel"><?php echo _("Site Url"); ?></td>
                  			<td><input type="text" class="text short" name="site_url" required /></td>
               			</tr>
               			<tr>
                  			<td class="textlabel"><?php echo _("Site Hash key"); ?></td>
                  			<td><input type="password" class="text short" name="site_hash_key" required /></td>
               			</tr>
               		</tbody>
               </table>
				<br />
				<input type="submit" name="save_registered" value="<?php echo _("Save");?>" />
			</form>
        </div>
       </div>
      </div>
   </div>